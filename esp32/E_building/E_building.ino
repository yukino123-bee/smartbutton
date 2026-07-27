#include <Arduino.h>
#include <HTTPClient.h>
#include <WiFi.h>

// --- Configuration ---
const char *ssid = "tandocADMIN 2.4ghz";
const char *password = "tandocjesse123";

// Endpoints
const char *api_url = "http://192.168.100.151:8000/api/emergency";
const char *status_url = "http://192.168.100.151:8000/api/device/status?device_id=ENG-001";

// The unique device code for this specific ESP32
const char *DEVICE_CODE = "ENG-001"; // E-Building

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
void sendSMS(String message);

void setup() {
  Serial.begin(115200);

  // Initialize GSM Serial (Hardware Serial 2)
  Serial2.begin(9600, SERIAL_8N1, SIM800L_RX, SIM800L_TX);

  // Initialize Pins
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

  // Connect to WiFi
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi connected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());

  // Connected State: Red OFF, Green ON
  digitalWrite(LED_RED, LOW);
  digitalWrite(LED_GREEN, HIGH);

  // Short beep to indicate readiness
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

    // Send Data to API
    sendPanicAlert(emergencyType);
}

void sendPanicAlert(String emergencyType) {
  bool apiSuccess = false;

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(api_url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    String payload = "{\"device_id\":\"" + String(DEVICE_CODE) +
                     "\", \"emergency_category\":\"" + emergencyType + "\"}";

    Serial.println("Sending Payload: " + payload);

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

  // Fallback to SMS if WiFi is down or API request fails
  if (!apiSuccess) {
    Serial.println("Failed to reach API! Falling back to SMS via SIM800L...");
    digitalWrite(LED_GREEN, LOW);
    digitalWrite(LED_RED, HIGH);
    beepBuzzer(3, 100);

    String smsMessage = "URGENT ALERT!\nLocation: " + String(DEVICE_CODE) +
                        "\nType: " + emergencyType;
    sendSMS(smsMessage);
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

// Helper function to handle the SIM800L SMS
void sendSMS(String message) {
  Serial.println("Sending SMS via SIM800L...");
  Serial2.println("AT+CMGF=1");
  delay(100);
  Serial2.println("AT+CMGS=\"09187439096\"");
  delay(100);
  Serial2.print(message);
  delay(100);
  Serial2.write(26);
  delay(100);
  Serial.println("SMS sent to 09187439096!");
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
