#include <Arduino.h>
#include <HTTPClient.h>
#include <WiFi.h>

// --- Configuration ---
const char *ssid = "tandocADMIN 2.4ghz";
const char *password = "tandocjesse123";

// Endpoints
const char *api_url = "http://192.168.100.169:8000/api/emergency";
const char *status_url = "http://192.168.100.169:8000/api/device/status?device_id=LIB-001";

// The unique device code for this specific ESP32
const char *DEVICE_CODE = "LIB-001"; // Campus Library

// Emergency Contact Phone Number for SMS Alerts (Supports local 09... or international +63...)
const char *EMERGENCY_PHONE_NUMBER = "+639187439096";

// --- Hardware Pins ---
const int BTN_CRITICAL = 14;   // Center Big Red Button: Critical Emergency
const int BTN_MEDICAL = 12;    // Center Small Button: Medical Emergency
const int BTN_SUSPICIOUS = 27; // Center Small Button: Public Safety Emergency

// Side Box Buttons for Clinic Aid Selection (when Medical is pressed)
const int BTN_CLINIC_YES = 32; // Side Top Button: Need Clinic Aid
const int BTN_CLINIC_NO  = 33; // Side Bottom Button: No Clinic Aid Needed

const int LED_RED = 2;   // Top Red Indicator: Alarm Active / Error
const int LED_GREEN = 4; // Top Green Indicator: Ready / Online
const int BUZZER = 5;    // Center Audio Buzzer: Feedback / SOS Alarm

// GSM Module Pins (SIM800L) connected to ESP32 Hardware Serial 2
#define SIM800L_RX 16
#define SIM800L_TX 17

// --- Alarm State & Status Polling ---
bool isDeviceAlarming = false;
unsigned long lastStatusCheckTime = 0;
const unsigned long STATUS_CHECK_INTERVAL = 1500; // Check server every 1.5 seconds

// --- Debounce & Edge Detection ---
struct Button {
  const int   pin;
  bool        lastReading;
  bool        stableState;
  unsigned long lastChangeTime;
  bool        triggered;
};

const unsigned long DEBOUNCE_MS  = 50;
const unsigned long COOLDOWN_MS  = 5000;
unsigned long lastAlertTime      = 0;

Button buttons[] = {
  { BTN_CRITICAL,   HIGH, HIGH, 0, false },
  { BTN_MEDICAL,    HIGH, HIGH, 0, false },
  { BTN_SUSPICIOUS, HIGH, HIGH, 0, false },
  { BTN_CLINIC_YES, HIGH, HIGH, 0, false },
  { BTN_CLINIC_NO,  HIGH, HIGH, 0, false },
};
const int NUM_BUTTONS = 5;

// Function Declarations
void beepBuzzer(int times, int durationMs);
void soundSOSPattern();
void checkAcknowledgeStatus();
void triggerAlarm(String emergencyType, bool needClinic = true);
void sendPanicAlert(String emergencyType, bool needClinic = true);
void initSIM800L();
void sendSMS(String message);
String sendATCommand(String command, String expectedResponse, unsigned long timeoutMs);

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n==========================================");
  Serial.println("  JHCSC SMART PANIC BUTTON SYSTEM (ESP32)");
  Serial.println("==========================================");

  // Initialize GSM Serial (Hardware Serial 2) at 9600 baud
  Serial2.begin(9600, SERIAL_8N1, SIM800L_RX, SIM800L_TX);

  // Initialize Hardware Pins
  pinMode(BTN_CRITICAL, INPUT_PULLUP);
  pinMode(BTN_MEDICAL, INPUT_PULLUP);
  pinMode(BTN_SUSPICIOUS, INPUT_PULLUP);
  pinMode(BTN_CLINIC_YES, INPUT_PULLUP);
  pinMode(BTN_CLINIC_NO, INPUT_PULLUP);

  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(BUZZER, OUTPUT);

  // Initial State: Red ON (Not Connected), Green OFF, Buzzer OFF
  digitalWrite(LED_RED, HIGH);
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(BUZZER, LOW);

  // Initialize SIM800L GSM Modem
  initSIM800L();

  // Connect to WiFi
  Serial.print("Connecting to WiFi (" + String(ssid) + ")");
  WiFi.begin(ssid, password);

  int wifiAttempts = 0;
  while (WiFi.status() != WL_CONNECTED && wifiAttempts < 20) {
    delay(500);
    Serial.print(".");
    wifiAttempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[WiFi SUCCESS] WiFi Connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    // Connected State: Red OFF, Green ON
    digitalWrite(LED_RED, LOW);
    digitalWrite(LED_GREEN, HIGH);
  } else {
    Serial.println("\n[WiFi WARNING] WiFi Connection Failed! Running in Standalone GSM Mode.");
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);
  }

  // Short beep to indicate system readiness
  beepBuzzer(1, 200);
}

void loop() {
  unsigned long now = millis();

  // Serial Passthrough for manual debugging from Serial Monitor
  while (Serial.available()) {
    char c = Serial.read();
    Serial2.write(c);
  }

  // If the device is currently alarming (alert sent, awaiting DRRMO acknowledgment)
  if (isDeviceAlarming) {
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);

    // Pulse buzzer every 1 second until DRRMO responds
    static unsigned long lastBeepPulse = 0;
    if (now - lastBeepPulse >= 1000) {
      lastBeepPulse = now;
      digitalWrite(BUZZER, HIGH);
      delay(150);
      digitalWrite(BUZZER, LOW);
    }

    // Check server status to see if DRRMO acknowledged the alert
    if (now - lastStatusCheckTime >= STATUS_CHECK_INTERVAL) {
      lastStatusCheckTime = now;
      checkAcknowledgeStatus();
    }
  } else {
    // Idle Heartbeat (send every 60 seconds to stay online on dashboard)
    if (now - lastStatusCheckTime >= 60000) {
      lastStatusCheckTime = now;
      checkAcknowledgeStatus();
    }
  }

  // Read physical panic and side buttons
  for (int i = 0; i < NUM_BUTTONS; i++) {
    Button &btn = buttons[i];
    bool reading = digitalRead(btn.pin);

    if (reading == HIGH) {
      btn.triggered = false;
    }

    if (reading != btn.lastReading) {
      btn.lastChangeTime = now;
    }
    btn.lastReading = reading;

    if ((now - btn.lastChangeTime) >= DEBOUNCE_MS) {
      bool prevStable = btn.stableState;
      btn.stableState = reading;

      // Falling edge (HIGH -> LOW) = actual button press
      if (prevStable == HIGH && btn.stableState == LOW && !btn.triggered) {
        if ((now - lastAlertTime) >= COOLDOWN_MS) {
          btn.triggered  = true;
          lastAlertTime  = now;

          if (btn.pin == BTN_CRITICAL) {
            triggerAlarm("Critical Emergency", true);
          } else if (btn.pin == BTN_SUSPICIOUS) {
            triggerAlarm("Public Safety Emergency", false);
          } else if (btn.pin == BTN_CLINIC_YES) {
            // Direct press on Side Top button
            triggerAlarm("Medical Emergency", true);
          } else if (btn.pin == BTN_CLINIC_NO) {
            // Direct press on Side Bottom button
            triggerAlarm("Medical Emergency", false);
          } else if (btn.pin == BTN_MEDICAL) {
            // Medical center button pressed: prompt for clinic aid selection for 2.5 seconds
            Serial.println("\n[MEDICAL ALERT] Medical button pressed!");
            Serial.println("  Press Side Top (GPIO 32) for Clinic Aid, or Side Bottom (GPIO 33) for No Clinic Aid.");
            beepBuzzer(2, 80);

            bool needClinic = true; // default
            unsigned long waitStart = millis();
            while (millis() - waitStart < 2500) {
              if (digitalRead(BTN_CLINIC_NO) == LOW) {
                needClinic = false;
                Serial.println("[CLINIC AID]: Side Bottom pressed -> NO Clinic Aid needed.");
                beepBuzzer(2, 60);
                break;
              }
              if (digitalRead(BTN_CLINIC_YES) == LOW) {
                needClinic = true;
                Serial.println("[CLINIC AID]: Side Top pressed -> YES Clinic Aid requested.");
                beepBuzzer(1, 150);
                break;
              }
              delay(20);
            }
            triggerAlarm("Medical Emergency", needClinic);
          }
        } else {
          Serial.println("[COOLDOWN] Alert suppressed, too soon.");
        }
      }
    }
  }

  delay(10);
}

void triggerAlarm(String emergencyType, bool needClinic) {
    Serial.println("\n🚨 [ALARM TRIGGERED] Panic Button Pressed: " + emergencyType);
    if (emergencyType == "Medical Emergency") {
      Serial.println("   Clinic Aid Required: " + String(needClinic ? "YES" : "NO"));
    }

    // Immediate local feedback
    digitalWrite(LED_GREEN, LOW);
    digitalWrite(LED_RED, HIGH);
    beepBuzzer(2, 150);

    // Process Alert
    sendPanicAlert(emergencyType, needClinic);
}

void sendPanicAlert(String emergencyType, bool needClinic) {
  bool apiSuccess = false;

  // 1. Send via Web API if WiFi is connected
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(api_url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    String payload = "{\"device_id\":\"" + String(DEVICE_CODE) +
                     "\", \"emergency_category\":\"" + emergencyType +
                     "\", \"need_clinic\":" + (needClinic ? "true" : "false") + "}";

    Serial.println("[API] Sending Payload: " + payload);

    int httpResponseCode = http.POST(payload);

    if (httpResponseCode > 0) {
      Serial.println("[API SUCCESS] HTTP Code: " + String(httpResponseCode));
      Serial.println("[API Response]: " + http.getString());
      beepBuzzer(1, 400);
      apiSuccess = true;

      // Enable active SOS alarming until DRRMO responds!
      isDeviceAlarming = true;
      lastStatusCheckTime = millis();
    } else {
      Serial.println("[API ERROR] HTTP Request Failed. Error Code: " + String(httpResponseCode));
    }

    http.end();
  } else {
    Serial.println("[API WARNING] WiFi Disconnected!");
  }

  // 2. ALWAYS Send SMS Notification via SIM800L for EVERY Alert Trigger
  Serial.println("\n[GSM ALERT] Transmitting SMS notification via SIM800L Module...");
  String smsMessage = "JHCSC SMART PANIC ALERT!\nDevice: " + String(DEVICE_CODE) +
                      "\nCategory: " + emergencyType;
  if (emergencyType == "Medical Emergency") {
    smsMessage += "\nClinic Aid: " + String(needClinic ? "YES" : "NO");
  }
  smsMessage += "\nStatus: Immediate Response Required!";
  
  sendSMS(smsMessage);
}

// Robust Helper function to execute AT commands and log real-time serial feedback
String sendATCommand(String command, String expectedResponse, unsigned long timeoutMs) {
  while (Serial2.available()) Serial2.read(); // flush serial buffer

  Serial.print("[AT SEND]: ");
  Serial.println(command);

  Serial2.println(command);

  String response = "";
  unsigned long start = millis();
  while (millis() - start < timeoutMs) {
    while (Serial2.available()) {
      char c = Serial2.read();
      response += c;
      Serial.write(c); // Echo modem response directly to USB Serial Monitor
    }
    if (expectedResponse.length() > 0 && response.indexOf(expectedResponse) >= 0) {
      break;
    }
  }
  return response;
}

// Initialize SIM800L AT commands & Network Registration
void initSIM800L() {
  Serial.println("\n==========================================");
  Serial.println("[SIM800L] Initializing GSM Modem...");
  Serial.println("==========================================");
  
  // Try 9600 baud rate sync
  bool modemReady = false;
  for (int i = 0; i < 3; i++) {
    String res = sendATCommand("AT", "OK", 1000);
    if (res.indexOf("OK") >= 0) {
      modemReady = true;
      break;
    }
    delay(200);
  }

  // If 9600 didn't work, try 115200 and lock baud rate to 9600
  if (!modemReady) {
    Serial.println("[SIM800L] Retrying modem sync at 115200 baud...");
    Serial2.begin(115200, SERIAL_8N1, SIM800L_RX, SIM800L_TX);
    delay(200);

    for (int i = 0; i < 3; i++) {
      String res = sendATCommand("AT", "OK", 1000);
      if (res.indexOf("OK") >= 0) {
        Serial.println("[SIM800L] Responded at 115200! Locking baud rate to 9600...");
        sendATCommand("AT+IPR=9600", "OK", 1000);
        delay(200);
        Serial2.begin(9600, SERIAL_8N1, SIM800L_RX, SIM800L_TX);
        modemReady = true;
        break;
      }
      delay(200);
    }
  }

  if (modemReady) {
    Serial.println("\n[SIM800L SUCCESS] Modem Online! Configuring SMS Parameters...");
    sendATCommand("AT+CMGF=1", "OK", 1000);           // Set Text Mode
    sendATCommand("AT+CSCS=\"GSM\"", "OK", 1000);       // Set Character Set to GSM
    sendATCommand("AT+CSMP=17,167,0,0", "OK", 1000);   // Set SMS Text Mode Parameters
    sendATCommand("AT+CPIN?", "OK", 1000);             // Check SIM card lock status
    sendATCommand("AT+CREG?", "OK", 1000);             // Check network registration (1,1 or 1,5 = registered)
    sendATCommand("AT+CSQ", "OK", 1000);              // Check signal quality
    Serial.println("[SIM800L] GSM Initialization Finished Successfully!\n");
  } else {
    Serial.println("\n[SIM800L ERROR ❌] Modem did NOT respond to AT commands!");
    Serial.println("[HARDWARE CHECKLIST]:");
    Serial.println("  1. ESP32 RX (GPIO 16) -> SIM800L TX");
    Serial.println("  2. ESP32 TX (GPIO 17) -> SIM800L RX");
    Serial.println("  3. Common GND shared between ESP32 and SIM800L");
    Serial.println("  4. SIM800L VCC must be 3.7V - 4.4V with 2A burst current capability!\n");
  }
}

// Helper function to handle SIM800L SMS with full prompt & delivery checks
void sendSMS(String message) {
  Serial.println("\n==========================================");
  Serial.println("[SMS TRANSMISSION START]");
  Serial.println("[Recipient Original]: " + String(EMERGENCY_PHONE_NUMBER));
  Serial.println("[Message Payload]:\n" + message);
  Serial.println("==========================================");

  // 1. Set Text Mode
  sendATCommand("AT+CMGF=1", "OK", 1000);

  // Clear serial input buffer
  while (Serial2.available()) Serial2.read();

  // 2. Format Phone Number to International Standard (+63...) if provided as local (09...)
  String targetNum = String(EMERGENCY_PHONE_NUMBER);
  targetNum.trim();
  if (targetNum.startsWith("0")) {
    targetNum = "+63" + targetNum.substring(1);
  }

  Serial.println("[SMS] Formatted International Phone Number: " + targetNum);

  // 3. Issue AT+CMGS Command
  String cmgsCmd = "AT+CMGS=\"" + targetNum + "\"";
  Serial.println("[SMS] Sending AT Command: " + cmgsCmd);
  Serial2.println(cmgsCmd);

  // 4. Wait for '>' prompt character from SIM800L (up to 4 seconds)
  unsigned long startPrompt = millis();
  bool promptReady = false;
  String promptBuffer = "";

  while (millis() - startPrompt < 4000) {
    while (Serial2.available()) {
      char c = Serial2.read();
      promptBuffer += c;
      Serial.write(c);
      if (c == '>') {
        promptReady = true;
        break;
      }
    }
    if (promptReady) break;
    delay(10);
  }

  if (promptReady) {
    Serial.println("\n[SMS SUCCESS] '>' Prompt received from SIM800L! Transmitting message body...");
    delay(150);

    // 5. Send Message Content
    Serial2.print(message);
    delay(200);

    // 6. Send Ctrl+Z (ASCII 26) to commit and send SMS
    Serial2.write(26);
    Serial.println("\n[SMS] Ctrl+Z (0x1A) sent! Waiting for GSM network delivery confirmation (+CMGS / OK)...");

    // 7. Wait for GSM Network transmission response (up to 12 seconds)
    unsigned long startSend = millis();
    bool delivered = false;
    String modemOutput = "";

    while (millis() - startSend < 12000) {
      while (Serial2.available()) {
        char c = Serial2.read();
        modemOutput += c;
        Serial.write(c);
        if (modemOutput.indexOf("+CMGS:") >= 0 || modemOutput.indexOf("OK") >= 0) {
          delivered = true;
          break;
        }
      }
      if (delivered) break;
      delay(10);
    }

    if (delivered) {
      Serial.println("\n[SMS SUCCESS 🎉] SMS successfully delivered to GSM Network!");
      beepBuzzer(2, 100);
    } else {
      Serial.println("\n[SMS FAILED ❌] Network response timeout or error!");
      Serial.println("[Modem Output Log]: " + modemOutput);
      Serial.println("[TROUBLESHOOTING]:");
      Serial.println("  1. SIM Card Balance: Ensure SIM has SMS load/credits.");
      Serial.println("  2. SIM Registration: Check if SIM card is activated & registered with NTC.");
      Serial.println("  3. Power Supply: SIM800L reboots if voltage drops below 3.7V during 2A TX burst!");
    }
  } else {
    Serial.println("\n[SMS ERROR ❌] Modem failed to send '>' prompt!");
    Serial.println("[Modem Output Log]: " + promptBuffer);
  }
  Serial.println("==========================================\n");
}

// Check server status to see if active alarm was acknowledged
void checkAcknowledgeStatus() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(status_url);
  http.addHeader("Accept", "application/json");

  int httpCode = http.GET();
  if (httpCode == 200) {
    String payload = http.getString();
    bool responded = (payload.indexOf("\"drrmo_responded\":true") >= 0 || payload.indexOf("\"drrmo_responded\": true") >= 0);
    bool noPending = (payload.indexOf("\"has_pending\":false") >= 0 || payload.indexOf("\"has_pending\": false") >= 0);

    if (isDeviceAlarming && (responded || noPending)) {
      Serial.println("\n[ACKNOWLEDGED] DRRMO responded to alert! Stopping device buzzer.");
      isDeviceAlarming = false;

      digitalWrite(BUZZER, LOW);
      digitalWrite(LED_RED, LOW);
      digitalWrite(LED_GREEN, HIGH);
      beepBuzzer(3, 120); // 3 confirmation beeps to notify witness that DRRMO responded!
    }
  }
  http.end();
}

// SOS Morse Code Buzzer Pattern: 3 Short, 3 Long, 3 Short
void soundSOSPattern() {
  // S: 3 short beeps
  for(int i = 0; i < 3; i++) {
    digitalWrite(BUZZER, HIGH); delay(100);
    digitalWrite(BUZZER, LOW);  delay(100);
  }
  delay(200);

  // O: 3 long beeps
  for(int i = 0; i < 3; i++) {
    digitalWrite(BUZZER, HIGH); delay(300);
    digitalWrite(BUZZER, LOW);  delay(100);
  }
  delay(200);

  // S: 3 short beeps
  for(int i = 0; i < 3; i++) {
    digitalWrite(BUZZER, HIGH); delay(100);
    digitalWrite(BUZZER, LOW);  delay(100);
  }
}

// Helper function to control buzzer
void beepBuzzer(int times, int durationMs) {
  for (int i = 0; i < times; i++) {
    digitalWrite(BUZZER, HIGH);
    delay(durationMs);
    digitalWrite(BUZZER, LOW);
    if (i < times - 1) delay(100);
  }
}
