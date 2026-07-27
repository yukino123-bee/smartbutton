#include <Arduino.h>
#include <HTTPClient.h>
#include <WiFi.h>

// --- Configuration ---
const char *ssid = "tandocADMIN 2.4ghz";
const char *password = "tandocjesse123";

// Endpoints
const char *api_url = "http://192.168.100.151:8000/api/emergency";
const char *status_url = "http://192.168.100.151:8000/api/device/status?device_id=GYM-001";

// The unique device code for this specific ESP32
const char *DEVICE_CODE = "GYM-001"; // Campus Gym

// Emergency Contact Phone Number for SMS Alerts
const char *EMERGENCY_PHONE_NUMBER = "09187439096";

// --- Hardware Pins ---
const int BTN_CRITICAL = 14;   // Critical Emergency
const int BTN_MEDICAL = 12;    // Medical Emergency
const int BTN_SUSPICIOUS = 27; // Suspicious Person

const int LED_RED = 2;   // Error / Sending / Active Alarm Indicator
const int LED_GREEN = 4; // Ready / Online Indicator
const int BUZZER = 5;    // Audio Feedback / SOS Alarm

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
};
const int NUM_BUTTONS = 3;
const char* emergencyTypes[] = {
  "Critical Emergency",
  "Medical Emergency",
  "Public Safety Emergency"
};

// Function Declarations
void beepBuzzer(int times, int durationMs);
void soundSOSPattern();
void checkAcknowledgeStatus();
void triggerAlarm(String emergencyType);
void sendPanicAlert(String emergencyType);
void initSIM800L();
void sendSMS(String message);

void setup() {
  Serial.begin(115200);

  // Initialize GSM Serial (Hardware Serial 2) at 9600 baud
  Serial2.begin(9600, SERIAL_8N1, SIM800L_RX, SIM800L_TX);

  // Initialize Hardware Pins
  pinMode(BTN_CRITICAL, INPUT_PULLUP);
  pinMode(BTN_MEDICAL, INPUT_PULLUP);
  pinMode(BTN_SUSPICIOUS, INPUT_PULLUP);

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
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);

  int wifiAttempts = 0;
  while (WiFi.status() != WL_CONNECTED && wifiAttempts < 20) {
    delay(500);
    Serial.print(".");
    wifiAttempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    // Connected State: Red OFF, Green ON
    digitalWrite(LED_RED, LOW);
    digitalWrite(LED_GREEN, HIGH);
  } else {
    Serial.println("\nWiFi Connection Failed! Running in GSM Standalone Mode.");
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);
  }

  // Short beep to indicate system readiness
  beepBuzzer(1, 200);
}

void loop() {
  unsigned long now = millis();

  // If the device is currently alarming (alert sent, awaiting dashboard acknowledgment)
  if (isDeviceAlarming) {
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);

    // Play one SOS pattern sequence
    soundSOSPattern();

    // Check server status to see if admin acknowledged the alert
    if (now - lastStatusCheckTime >= STATUS_CHECK_INTERVAL) {
      lastStatusCheckTime = now;
      checkAcknowledgeStatus();
    }
  }

  // Read physical panic buttons
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

      // Falling edge (HIGH → LOW) = actual button press
      if (prevStable == HIGH && btn.stableState == LOW && !btn.triggered) {
        if ((now - lastAlertTime) >= COOLDOWN_MS) {
          btn.triggered  = true;
          lastAlertTime  = now;
          triggerAlarm(emergencyTypes[i]);
        } else {
          Serial.println("[COOLDOWN] Alert suppressed, too soon.");
        }
      }
    }
  }

  delay(10);
}

void triggerAlarm(String emergencyType) {
    Serial.println("\nPanic Button Pressed: " + emergencyType);

    // Immediate local feedback
    digitalWrite(LED_GREEN, LOW);
    digitalWrite(LED_RED, HIGH);
    beepBuzzer(2, 150);

    // Process Alert
    sendPanicAlert(emergencyType);
}

void sendPanicAlert(String emergencyType) {
  bool apiSuccess = false;

  // 1. Send via Web API if WiFi is connected
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(api_url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    String payload = "{\"device_id\":\"" + String(DEVICE_CODE) +
                     "\", \"emergency_category\":\"" + emergencyType + "\"}";

    Serial.println("Sending API Payload: " + payload);

    int httpResponseCode = http.POST(payload);

    if (httpResponseCode > 0) {
      Serial.println("HTTP Response Code: " + String(httpResponseCode));
      Serial.println("Response: " + http.getString());
      beepBuzzer(1, 500);
      apiSuccess = true;

      // Enable active SOS alarming until dashboard acknowledges!
      isDeviceAlarming = true;
      lastStatusCheckTime = millis();
    } else {
      Serial.println("HTTP Request Failed. Error Code: " + String(httpResponseCode));
    }

    http.end();
  } else {
    Serial.println("WiFi Disconnected!");
  }

  // 2. ALWAYS Send SMS Notification via SIM800L for EVERY Alert Trigger
  Serial.println("\n[ALERT] Transmitting SMS notification via SIM800L GSM Module...");
  String smsMessage = "JHCSC SMART PANIC ALERT!\nDevice: " + String(DEVICE_CODE) +
                      "\nCategory: " + emergencyType +
                      "\nStatus: Immediate Response Required!";
  
  sendSMS(smsMessage);
}

// Initialize SIM800L AT commands & Network
void initSIM800L() {
  Serial.println("\n[SIM800L] Initializing GSM Module...");
  
  // Sync baud rate
  bool modemReady = false;
  for (int i = 0; i < 5; i++) {
    Serial2.println("AT");
    delay(300);
    if (Serial2.find("OK")) {
      modemReady = true;
      break;
    }
  }

  if (modemReady) {
    Serial.println("[SIM800L] Modem responded OK!");

    // Set Text Mode
    Serial2.println("AT+CMGF=1");
    delay(300);

    // Set Character Set to GSM
    Serial2.println("AT+CSCS=\"GSM\"");
    delay(300);

    // Check SIM Card Status
    Serial2.println("AT+CPIN?");
    delay(300);

    // Check Network Registration
    Serial2.println("AT+CREG?");
    delay(300);

    Serial.println("[SIM800L] GSM Initialization complete.");
  } else {
    Serial.println("[SIM800L WARNING] SIM800L not responding to AT commands!");
    Serial.println("[TIP] Verify TX/RX wiring (ESP32 RX=16 to SIM800L TX, ESP32 TX=17 to SIM800L RX) and ensure 3.7V-4.2V 2A power supply.");
  }
}

// Helper function to handle the SIM800L SMS with full prompt & delivery checks
void sendSMS(String message) {
  Serial.println("\n[SMS] Preparing to send SMS via SIM800L...");
  Serial.println("[SMS] Recipient: " + String(EMERGENCY_PHONE_NUMBER));
  Serial.println("[SMS] Message: " + message);

  // 1. Set Text Mode
  Serial2.println("AT+CMGF=1");
  delay(300);

  // Clear serial input buffer
  while (Serial2.available()) Serial2.read();

  // 2. Issue AT+CMGS Command
  Serial2.print("AT+CMGS=\"");
  Serial2.print(EMERGENCY_PHONE_NUMBER);
  Serial2.println("\"");

  // 3. Wait for '>' prompt character from SIM800L (up to 3 seconds)
  unsigned long startPrompt = millis();
  bool promptReady = false;

  while (millis() - startPrompt < 3000) {
    if (Serial2.available()) {
      char c = Serial2.read();
      if (c == '>') {
        promptReady = true;
        break;
      }
    }
  }

  if (promptReady) {
    Serial.println("[SMS] '>' Prompt received! Sending message body...");
    delay(100);

    // 4. Send Message Content
    Serial2.print(message);
    delay(200);

    // 5. Send Ctrl+Z (ASCII 26) to commit and send SMS
    Serial2.write(26);
    Serial.println("[SMS] Ctrl+Z sent! Awaiting network delivery confirmation (+CMGS / OK)...");

    // 6. Wait for GSM Network transmission response (up to 10 seconds)
    unsigned long startSend = millis();
    bool delivered = false;
    String modemOutput = "";

    while (millis() - startSend < 10000) {
      while (Serial2.available()) {
        char c = Serial2.read();
        modemOutput += c;
        if (modemOutput.indexOf("+CMGS:") >= 0 || modemOutput.indexOf("OK") >= 0) {
          delivered = true;
          break;
        }
      }
      if (delivered) break;
      delay(10);
    }

    if (delivered) {
      Serial.println("[SMS SUCCESS] SMS successfully delivered to cellular network!");
      Serial.println("[GSM Output]: " + modemOutput);
      beepBuzzer(2, 100);
    } else {
      Serial.println("[SMS WARNING] Timeout waiting for network OK. Response: " + modemOutput);
      Serial.println("[NOTE] Ensure SIM800L has sufficient power (3.7V-4.2V 2A), load balance, and antenna connected.");
    }
  } else {
    Serial.println("[SMS ERROR] Failed to receive '>' prompt from SIM800L modem!");
    Serial.println("[NOTE] Check serial baud rate (9600), wiring (16/17), SIM PIN lock, and power.");
  }
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
    // If pending is false, admin acknowledged the alert on dashboard!
    if (payload.indexOf("\"has_pending\":false") >= 0 || payload.indexOf("\"has_pending\": false") >= 0) {
      Serial.println("\n[ACKNOWLEDGED] Alert acknowledged on dashboard! Stopping device SOS alarm.");
      isDeviceAlarming = false;

      digitalWrite(LED_RED, LOW);
      digitalWrite(LED_GREEN, HIGH);
      beepBuzzer(2, 200); // 2 confirmation beeps
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
