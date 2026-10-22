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

// Emergency Contact Phone Numbers for Ready-to-Deploy Responders & Personnel
const char *EMERGENCY_PHONE_NUMBERS[] = {
  "+639927090622", // Primary Ready-to-deploy Personnel (DITO)
  "+639563559186", // Secondary Ready-to-deploy Personnel (Globe)
  "+639187439096"  // DRRMO Officer
};
const int NUM_EMERGENCY_RECIPIENTS = 3;

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
bool offlineAlarm = false;               // Alarm raised via SMS failover: no server ack can arrive
unsigned long offlineAlarmStart = 0;
const unsigned long OFFLINE_ALARM_MS = 60000; // Stop local siren after 60s when offline
bool confirmationMode = false; // Live Dispatch Mode: Physical buttons dispatch real emergency alerts via WiFi / GSM
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
void sendSMS(String message, String targetNum = "");
void sendEmergencySMSAlert(String message);
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

  // 1. Connect to WiFi first for immediate network connection and Green indicator
  Serial.print("Connecting to WiFi (" + String(ssid) + ")");
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  int wifiAttempts = 0;
  while (WiFi.status() != WL_CONNECTED && wifiAttempts < 25) {
    delay(400);
    Serial.print(".");
    wifiAttempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[WiFi SUCCESS] WiFi Connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    // Connected State: Red OFF, Green ON immediately!
    digitalWrite(LED_RED, LOW);
    digitalWrite(LED_GREEN, HIGH);
  } else {
    Serial.println("\n[WiFi WARNING] WiFi Connection Failed! Running in Standalone GSM Mode.");
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);
  }

  // 2. Initialize SIM800L GSM Modem
  initSIM800L();

  // Ensure Green LED stays ON if WiFi connected
  if (WiFi.status() == WL_CONNECTED) {
    digitalWrite(LED_RED, LOW);
    digitalWrite(LED_GREEN, HIGH);
  }

  // 3. Initialize button states after pin modes and pull-ups settle to prevent false boot triggers
  for (int i = 0; i < NUM_BUTTONS; i++) {
    bool state = digitalRead(buttons[i].pin);
    buttons[i].lastReading = state;
    buttons[i].stableState = state;
    buttons[i].lastChangeTime = millis();
    buttons[i].triggered = (state == LOW);
  }

  // Short beep to indicate system readiness
  beepBuzzer(1, 200);
}

void loop() {
  unsigned long now = millis();

  // Serial Passthrough and Interactive Command Support
  static String serialCmdBuffer = "";
  while (Serial.available()) {
    char c = Serial.read();
    if (c == '\r' || c == '\n') {
      if (serialCmdBuffer.length() > 0) {
        serialCmdBuffer.trim();
        if (serialCmdBuffer.equalsIgnoreCase("TEST_SMS")) {
          Serial.println("\n>>> [MANUAL TEST] Triggering test SMS via SIM800L...");
          sendSMS("TEST ALERT from JHCSC Smart Panic Button (" + String(DEVICE_CODE) + ")!\nSIM800L GSM Module Functional Verification.");
        } else if (serialCmdBuffer.equalsIgnoreCase("GSM_STATUS")) {
          Serial.println("\n>>> [GSM STATUS CHECK]");
          sendATCommand("AT", "OK", 1000);
          sendATCommand("AT+CPIN?", "OK", 1000);
          sendATCommand("AT+CSQ", "OK", 1000);
          sendATCommand("AT+CBC", "OK", 1000);
          sendATCommand("AT+CREG?", "OK", 1000);
          sendATCommand("AT+COPS?", "OK", 2000);
          sendATCommand("AT+CSCA?", "OK", 1000);
        } else if (serialCmdBuffer.equalsIgnoreCase("TEST_LEDS")) {
          Serial.println("\n>>> [LED TEST] Testing LEDs...");
          Serial.println("  1. Driving Green LED (GPIO 4) HIGH for 2s...");
          digitalWrite(LED_RED, LOW);
          digitalWrite(LED_GREEN, HIGH);
          delay(2000);
          Serial.println("  2. Driving Red LED (GPIO 2) HIGH for 2s...");
          digitalWrite(LED_GREEN, LOW);
          digitalWrite(LED_RED, HIGH);
          delay(2000);
          Serial.println("  3. Driving Green LED (GPIO 4) LOW for 2s (in case module is active-LOW)...");
          digitalWrite(LED_RED, HIGH);
          digitalWrite(LED_GREEN, LOW);
          delay(2000);
          Serial.println("  [LED TEST] Complete. Restoring normal state.");
          digitalWrite(LED_GREEN, HIGH);
          digitalWrite(LED_RED, LOW);
        } else if (serialCmdBuffer.equalsIgnoreCase("PIN_STATUS")) {
          Serial.println("\n>>> [PIN STATUS]");
          Serial.println("  Mode: " + String(confirmationMode ? "CONFIRMATION MODE (Alerts & Siren Muted)" : "LIVE DISPATCH MODE"));
          Serial.println("  WiFi Status: " + String(WiFi.status() == WL_CONNECTED ? "CONNECTED" : "DISCONNECTED"));
          Serial.println("  isDeviceAlarming: " + String(isDeviceAlarming ? "TRUE (ALARM ACTIVE)" : "FALSE (IDLE)"));
          Serial.println("  LED_GREEN (GPIO 4): " + String(digitalRead(LED_GREEN) == HIGH ? "DRIVEN HIGH (3.3V)" : "DRIVEN LOW (0V)"));
          Serial.println("  LED_RED (GPIO 2): " + String(digitalRead(LED_RED) == HIGH ? "DRIVEN HIGH (3.3V)" : "DRIVEN LOW (0V)"));
          Serial.println("  BTN_CRITICAL (GPIO 14): " + String(digitalRead(BTN_CRITICAL) == LOW ? "PRESSED (LOW/GND)" : "IDLE (HIGH/3.3V)"));
          Serial.println("  BTN_MEDICAL (GPIO 12): " + String(digitalRead(BTN_MEDICAL) == LOW ? "PRESSED (LOW/GND)" : "IDLE (HIGH/3.3V)"));
          Serial.println("  BTN_SUSPICIOUS (GPIO 27): " + String(digitalRead(BTN_SUSPICIOUS) == LOW ? "PRESSED (LOW/GND)" : "IDLE (HIGH/3.3V)"));
          Serial.println("  BTN_CLINIC_YES (GPIO 32): " + String(digitalRead(BTN_CLINIC_YES) == LOW ? "PRESSED (LOW/GND)" : "IDLE (HIGH/3.3V)"));
          Serial.println("  BTN_CLINIC_NO (GPIO 33): " + String(digitalRead(BTN_CLINIC_NO) == LOW ? "PRESSED (LOW/GND)" : "IDLE (HIGH/3.3V)"));
        } else if (serialCmdBuffer.equalsIgnoreCase("CONFIRMATION_MODE") || serialCmdBuffer.equalsIgnoreCase("TEST_MODE")) {
          confirmationMode = true;
          Serial.println("[MODE] Confirmation Mode ENABLED: Alerts, sirens, and SMS muted during testing.");
        } else if (serialCmdBuffer.equalsIgnoreCase("LIVE_MODE") || serialCmdBuffer.equalsIgnoreCase("ALERT_MODE")) {
          confirmationMode = false;
          Serial.println("[MODE] Live Mode ENABLED: Emergency dispatch, alarms, and SMS are now ACTIVE.");
        } else if (serialCmdBuffer.equalsIgnoreCase("GREEN_HIGH")) {
          digitalWrite(LED_GREEN, HIGH);
          Serial.println("[LED] GPIO 4 (Green LED) set to HIGH");
        } else if (serialCmdBuffer.equalsIgnoreCase("GREEN_LOW")) {
          digitalWrite(LED_GREEN, LOW);
          Serial.println("[LED] GPIO 4 (Green LED) set to LOW");
        } else if (serialCmdBuffer.equalsIgnoreCase("RED_HIGH")) {
          digitalWrite(LED_RED, HIGH);
          Serial.println("[LED] GPIO 2 (Red LED) set to HIGH");
        } else if (serialCmdBuffer.equalsIgnoreCase("RED_LOW")) {
          digitalWrite(LED_RED, LOW);
          Serial.println("[LED] GPIO 2 (Red LED) set to LOW");
        } else {
          // Forward raw AT command directly to SIM800L
          Serial2.println(serialCmdBuffer);
        }
        serialCmdBuffer = "";
      }
    } else {
      serialCmdBuffer += c;
    }
  }

  // Echo any incoming bytes from SIM800L back to USB Serial Monitor
  while (Serial2.available()) {
    char c = Serial2.read();
    Serial.write(c);
  }

  // If the device is currently alarming (alert sent, awaiting DRRMO acknowledgment)
  if (isDeviceAlarming) {
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);

    // Offline alarms can't be acknowledged by the server, so end them after a fixed time
    if (offlineAlarm && (now - offlineAlarmStart >= OFFLINE_ALARM_MS)) {
      Serial.println("\n[OFFLINE ALARM TIMEOUT] No server link; stopping local siren.");
      isDeviceAlarming = false;
      offlineAlarm = false;
      digitalWrite(BUZZER, LOW);
      return;
    }

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
    // Idle Ready State: Solid Green ON when WiFi connected, Red OFF
    if (WiFi.status() == WL_CONNECTED) {
      digitalWrite(LED_GREEN, HIGH);
      digitalWrite(LED_RED, LOW);
    } else {
      digitalWrite(LED_GREEN, LOW);
      digitalWrite(LED_RED, HIGH);
    }

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
          } else if (btn.pin == BTN_CLINIC_YES || btn.pin == BTN_CLINIC_NO) {
            // Dormant when idle: Only active when Medical Alert is triggered first!
            Serial.println("\nℹ️ [CLINIC BUTTON INACTIVE] Press Center Medical Button (GPIO 12) first to initiate a Medical Emergency.");
            beepBuzzer(1, 30); // Very short gentle chirp to indicate button is received but dormant
          } else if (btn.pin == BTN_MEDICAL) {
            // Medical center button pressed: prompt for clinic aid selection for 5 seconds
            Serial.println("\n🚨 [MEDICAL ALERT TRIGGERED] Awaiting Clinic Aid Selection:");
            Serial.println("   -> Press Side Top (GPIO 32) if Clinic Assistance is REQUIRED");
            Serial.println("   -> Press Side Bottom (GPIO 33) if Clinic Assistance is NOT NEEDED (First Aid Only)");
            Serial.println("   (Device will listen for 5 seconds; defaults to Clinic YES if no button pressed)");

            // Visual and audio prompt: 2 alert beeps
            beepBuzzer(2, 90);

            bool needClinic = true; // default
            bool buttonSelected = false;
            unsigned long waitStart = millis();

            while (millis() - waitStart < 5000) {
              // Rapid pulse red indicator during selection window
              if ((millis() - waitStart) % 500 < 250) {
                digitalWrite(LED_RED, HIGH);
                digitalWrite(LED_GREEN, LOW);
              } else {
                digitalWrite(LED_RED, LOW);
                digitalWrite(LED_GREEN, HIGH);
              }

              // Check Clinic NO Button (GPIO 33)
              if (digitalRead(BTN_CLINIC_NO) == LOW) {
                delay(30); // debounce
                if (digitalRead(BTN_CLINIC_NO) == LOW) {
                  needClinic = false;
                  buttonSelected = true;
                  Serial.println("\n✅ [CLINIC SELECTION]: GPIO 33 Pressed -> NO Clinic Assistance Needed (DRRMO / First Aid Only).");
                  beepBuzzer(2, 70);
                  break;
                }
              }

              // Check Clinic YES Button (GPIO 32)
              if (digitalRead(BTN_CLINIC_YES) == LOW) {
                delay(30); // debounce
                if (digitalRead(BTN_CLINIC_YES) == LOW) {
                  needClinic = true;
                  buttonSelected = true;
                  Serial.println("\n🚨 [CLINIC SELECTION]: GPIO 32 Pressed -> Clinic Assistance is REQUIRED!");
                  beepBuzzer(1, 200);
                  break;
                }
              }

              delay(15);
            }

            if (!buttonSelected) {
              Serial.println("\n⏱️ [CLINIC SELECTION TIMEOUT]: No selection received in 5s. Defaulting to Clinic Aid: REQUIRED.");
            }

            // Restore solid Green before dispatch
            digitalWrite(LED_GREEN, HIGH);
            digitalWrite(LED_RED, LOW);

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
    if (confirmationMode) {
      Serial.println("\n🔔 [BUTTON CONFIRMED - NO ALERT DISPATCHED]");
      Serial.println("   Detected Event: " + emergencyType);
      if (emergencyType == "Medical Emergency") {
        Serial.println("   Clinic Aid Flag: " + String(needClinic ? "YES" : "NO"));
      }
      Serial.println("   (Device is in Confirmation Mode. Live alerts, alarms, and SMS are suppressed.)");
      beepBuzzer(1, 50); // Single gentle 50ms click
      return;
    }

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

  // 1. Primary Path: Send via Web API if WiFi is connected
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(api_url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.setTimeout(4000); // 4-second timeout to avoid long blocking

    String payload = "{\"device_id\":\"" + String(DEVICE_CODE) +
                     "\", \"emergency_category\":\"" + emergencyType +
                     "\", \"need_clinic\":" + (needClinic ? "true" : "false") + "}";

    Serial.println("[API] Sending Payload to Server: " + payload);

    int httpResponseCode = http.POST(payload);

    if (httpResponseCode >= 200 && httpResponseCode < 300) {
      Serial.println("[API SUCCESS] Alert received by DRRMO Server! HTTP Code: " + String(httpResponseCode));
      Serial.println("[API Response]: " + http.getString());
      beepBuzzer(1, 400);
      apiSuccess = true;

      // Enable active SOS alarming until DRRMO responds
      isDeviceAlarming = true;
      lastStatusCheckTime = millis();
    } else {
      Serial.println("[API ERROR] Server request failed! HTTP Code: " + String(httpResponseCode));
    }

    http.end();
  } else {
    Serial.println("[API WARNING] WiFi is DISCONNECTED!");
  }

  // 2. Offline Fallback: If WiFi is disconnected OR API failed, send SMS via SIM800L to ready-to-deploy personnel
  if (!apiSuccess) {
    Serial.println("\n🚨 [GSM FAILOVER ACTIVATED] WiFi/Internet unavailable! Dispatching Emergency SMS via SIM800L to ready-to-deploy personnel...");
    
    // Enable local alarming so people nearby know the alarm was triggered
    isDeviceAlarming = true;
    offlineAlarm = true;
    offlineAlarmStart = millis();

    String smsMessage = "JHCSC PANIC ALERT (OFFLINE)!\n"
                        "Device: " + String(DEVICE_CODE) + " (Campus Library)\n"
                        "Category: " + emergencyType;
    if (emergencyType == "Medical Emergency") {
      smsMessage += "\nClinic Aid: " + String(needClinic ? "REQUIRED" : "NOT REQUIRED");
    }
    smsMessage += "\nNotice: WiFi/Server DOWN! Immediate response required!";

    // Send SMS alert to all configured ready-to-deploy personnel
    sendEmergencySMSAlert(smsMessage);
  } else {
    Serial.println("[GSM] Alert already delivered via Web API. SMS suppressed to conserve load.");
  }
}

// Robust Helper function to execute AT commands and log real-time serial feedback
String sendATCommand(String command, String expectedResponse, unsigned long timeoutMs) {
  while (Serial2.available()) Serial2.read(); // flush serial buffer
  delay(50);

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
    delay(5);
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
  for (int i = 0; i < 5; i++) {
    String res = sendATCommand("AT", "OK", 1000);
    if (res.indexOf("OK") >= 0) {
      modemReady = true;
      break;
    }
    delay(300);
  }

  // If 9600 didn't work, try 115200 and switch baud rate to 9600
  if (!modemReady) {
    Serial.println("[SIM800L] Retrying modem sync at 115200 baud...");
    Serial2.begin(115200, SERIAL_8N1, SIM800L_RX, SIM800L_TX);
    delay(300);

    for (int i = 0; i < 5; i++) {
      String res = sendATCommand("AT", "OK", 1000);
      if (res.indexOf("OK") >= 0) {
        Serial.println("[SIM800L] Responded at 115200! Setting baud rate to 9600...");
        sendATCommand("AT+IPR=9600", "OK", 1000);
        delay(300);
        Serial2.begin(9600, SERIAL_8N1, SIM800L_RX, SIM800L_TX);
        modemReady = true;
        break;
      }
      delay(300);
    }
  }

  if (modemReady) {
    Serial.println("\n[SIM800L SUCCESS] Modem Online! Configuring GSM Parameters...");
    
    // Enable verbose error reporting (+CMEE=2) for human-readable error descriptions
    sendATCommand("AT+CMEE=2", "OK", 1000);
    delay(150);

    // Set Text Mode for SMS
    sendATCommand("AT+CMGF=1", "OK", 1000);
    delay(150);

    // Set Character Set to GSM 7-bit
    sendATCommand("AT+CSCS=\"GSM\"", "OK", 1000);
    delay(150);

    // Check SIM card readiness
    Serial.println("[SIM800L] Checking SIM card readiness...");
    for (int i = 0; i < 5; i++) {
      String cpinRes = sendATCommand("AT+CPIN?", "OK", 1500);
      if (cpinRes.indexOf("READY") >= 0) {
        Serial.println("  [SIM Card Status]: READY!");
        break;
      }
      delay(1000);
    }

    // Check Signal Quality (AT+CSQ)
    sendATCommand("AT+CSQ", "OK", 1500);
    delay(150);

    // Check Battery / Supply Voltage (AT+CBC)
    sendATCommand("AT+CBC", "OK", 1500);
    delay(150);

    // Check Network Registration (AT+CREG?)
    Serial.println("[SIM800L] Checking cellular network registration status...");
    String cregRes = sendATCommand("AT+CREG?", "OK", 1000);
    if (cregRes.indexOf("+CREG: 0,1") >= 0 || cregRes.indexOf("+CREG: 0,5") >= 0 ||
        cregRes.indexOf("+CREG: 1,1") >= 0 || cregRes.indexOf("+CREG: 1,5") >= 0) {
      Serial.println("  [Cellular Network]: Registered on GSM Network!");
    } else {
      Serial.println("  [Cellular Network Status]: Searching / Standby (" + cregRes + ")");
    }

    // Check Operator Name (AT+COPS?)
    sendATCommand("AT+COPS?", "OK", 2000);
    delay(150);

    // Check SMS Center (SMSC)
    sendATCommand("AT+CSCA?", "OK", 1500);

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

// Send SMS alerts to all designated ready-to-deploy personnel
void sendEmergencySMSAlert(String message) {
  for (int i = 0; i < NUM_EMERGENCY_RECIPIENTS; i++) {
    Serial.println("\n[GSM DISPATCH " + String(i + 1) + "/" + String(NUM_EMERGENCY_RECIPIENTS) + "] -> " + String(EMERGENCY_PHONE_NUMBERS[i]));
    sendSMS(message, EMERGENCY_PHONE_NUMBERS[i]);
    delay(500);
  }
}

// Helper function to handle SIM800L SMS with full prompt & delivery checks
void sendSMS(String message, String targetNum) {
  if (targetNum.length() == 0) {
    targetNum = EMERGENCY_PHONE_NUMBERS[0];
  }

  Serial.println("\n==========================================");
  Serial.println("[SMS TRANSMISSION START]");
  Serial.println("[Recipient]: " + targetNum);
  Serial.println("[Message Payload]:\n" + message);
  Serial.println("==========================================");

  // 1. Ensure Text Mode
  sendATCommand("AT+CMGF=1", "OK", 1000);
  delay(100);

  // Clear serial input buffer
  while (Serial2.available()) Serial2.read();

  // 2. Format Phone Number to International Standard (+63...)
  targetNum.trim();
  targetNum.replace(" ", "");
  targetNum.replace("-", "");
  if (targetNum.startsWith("0")) {
    targetNum = "+63" + targetNum.substring(1);
  }

  Serial.println("[SMS] Formatted International Phone Number: " + targetNum);

  // 3. Issue AT+CMGS Command
  String cmgsCmd = "AT+CMGS=\"" + targetNum + "\"";
  Serial.println("[SMS] Sending AT Command: " + cmgsCmd);
  Serial2.println(cmgsCmd);

  // 4. Wait for '>' prompt character from SIM800L (up to 5 seconds)
  unsigned long startPrompt = millis();
  bool promptReady = false;
  String promptBuffer = "";

  while (millis() - startPrompt < 5000) {
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
    delay(200);

    // 5. Send Message Content
    Serial2.print(message);
    delay(300);

    // 6. Send Ctrl+Z (ASCII 26) to commit and send SMS
    Serial2.write(26);
    Serial.println("\n[SMS] Ctrl+Z (0x1A) sent! Waiting for GSM network delivery confirmation (+CMGS / OK)...");

    // 7. Wait for GSM Network transmission response (up to 20 seconds)
    unsigned long startSend = millis();
    bool delivered = false;
    String modemOutput = "";

    while (millis() - startSend < 20000) {
      while (Serial2.available()) {
        char c = Serial2.read();
        modemOutput += c;
        Serial.write(c);
        if (modemOutput.indexOf("+CMGS:") >= 0 || modemOutput.indexOf("\nOK") >= 0) {
          delivered = true;
          break;
        }
        if (modemOutput.indexOf("ERROR") >= 0) {
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
      beepBuzzer(3, 80);
    }
  } else {
    Serial.println("\n[SMS ERROR ❌] Modem failed to send '>' prompt!");
    Serial.println("[Modem Output Log]: " + promptBuffer);
    beepBuzzer(3, 80);
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
      offlineAlarm = false;

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
