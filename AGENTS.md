# JHCSC Smart Panic Button System - Agent Memory & Instructions

This file serves as persistent memory and instructions for all AI assistant sessions in this project directory (`/home/jed/Desktop/smartbutton`).

---

## 1. System Overview & Active Device Configuration

- **Device Identity**: `LIB-001` (Campus Library)
- **Host Server IP**: `192.168.100.169:8000` (Laravel application on `0.0.0.0:8000`)
- **WiFi Network**: `tandocADMIN 2.4ghz` (Password: `tandocjesse123`)
- **Assigned Device IP**: `192.168.100.155`
- **Emergency SMS Recipient**: `+639187439096`
- **Carrier**: Smart / TNT (2G GSM band 900 / 1800 MHz)

---

## 2. Server API Endpoints

- **Emergency Alert**:
  `POST http://192.168.100.169:8000/api/emergency`
  - Payload: `{"device_id": "LIB-001", "emergency_category": "...", "need_clinic": true|false}`
  - Categories: `"Critical Emergency"`, `"Medical Emergency"`, `"Public Safety Emergency"`
- **Heartbeat & Status Polling**:
  `GET http://192.168.100.169:8000/api/device/status?device_id=LIB-001`
  - Returns: `{"device_code":"LIB-001","has_pending":bool,"status":"normal"|"pending"|"acknowledged","incident_id":...,"drrmo_responded":bool}`
- **DRRMO Dashboard Acknowledgment**:
  `POST http://192.168.100.169:8000/api/incidents/{id}/acknowledge`

---

## 3. Hardware Pin Mapping (ESP32 Dev Module)

All button inputs use internal pull-ups (`INPUT_PULLUP`). Triggered when pin is pulled to **GND**.

| Component | ESP32 GPIO | Description / Behavior |
| :--- | :--- | :--- |
| **Top Green LED** | `GPIO 4` | Solid ON when WiFi connected and system is idle/ready. |
| **Top Red LED** | `GPIO 2` | Active/blinking during an alert / awaiting DRRMO response. |
| **Center Buzzer** | `GPIO 5` | Pulses every 1 sec during alert until DRRMO acknowledges, then sounds 3 confirmation beeps and shuts off. |
| **Center Big Red Button** | `GPIO 14` | **Critical Emergency** (dispatches to DRRMO and Clinic). |
| **Center Small Button** | `GPIO 12` | **Medical Emergency** (beeps and listens 2.5s for side buttons; defaults to Clinic YES). |
| **Center Small Button** | `GPIO 27` | **Public Safety Emergency** (dispatches to DRRMO). |
| **Side Box Top Button** | `GPIO 32` | **Clinic Aid YES** (`need_clinic: true`). Can be pressed during Medical prompt or directly. |
| **Side Box Bottom Button** | `GPIO 33` | **Clinic Aid NO** (`need_clinic: false`). Can be pressed during Medical prompt or directly. |
| **SIM800L RX** | `GPIO 16` | Connects to SIM800L `TXD` (Serial2 at 9600 baud). |
| **SIM800L TX** | `GPIO 17` | Connects to SIM800L `RXD` (Serial2 at 9600 baud). |

---

## 4. Firmware Build & Upload Procedures

- **Target Sketch**: `esp32/Library/Library.ino`
- **FQBN**: `esp32:esp32:esp32`
- **Default Serial Port**: `/dev/ttyUSB0` (CH340 chip)
- **Compile Command**:
  ```bash
  /home/jed/.local/bin/arduino-cli compile --fqbn esp32:esp32:esp32 esp32/Library/Library.ino
  ```
- **Upload Command**:
  ```bash
  /home/jed/.local/bin/arduino-cli upload -p /dev/ttyUSB0 --fqbn esp32:esp32:esp32 esp32/Library/Library.ino
  ```
- **Serial Monitor**:
  ```bash
  python3 -c "import serial; ser=serial.Serial('/dev/ttyUSB0', 115200); [print(ser.readline().decode('utf-8', errors='replace').rstrip()) for _ in iter(int, 1)]"
  ```

---

## 5. Testing & Diagnostic Runbook

### A. Testing Without Physical Buttons
To simulate pressing a button when physical buttons are not yet wired:
- Connect a jumper wire from **GND**.
- Momentarily touch the other end to:
  - **GPIO 14** -> Triggers **Critical Emergency**.
  - **GPIO 12** -> Triggers **Medical Emergency**.
  - **GPIO 27** -> Triggers **Public Safety Emergency**.

### B. SIM800L GSM Module Diagnostics
1. **NET LED Indicator Codes**:
   - **Blinking every 1 second**: Searching for 2G network (not yet registered).
   - **Blinking every 3 seconds**: Successfully registered on Smart/TNT network (ready for SMS).
2. **Requirements for Registration**:
   - Antenna firmly connected to SIM800L (2G indoor reception).
   - Common GND connected between ESP32 and SIM800L.
   - Power supply between 3.7V - 4.2V capable of 2A burst current (recommend 1000µF capacitor across VCC/GND).
   - TNT SIM card registered under SIM Card Registration Act with active load.
3. **Serial AT Commands**:
   - `AT` (Modem ready check)
   - `AT+CPIN?` (SIM card ready check)
   - `AT+CSQ` (Signal quality 0-31)
   - `AT+CREG?` (Network registration check; `+CREG: 0,1` or `0,5` means registered)

---

## 6. Standard Session Checklist

When entering this directory for new work:
1. Verify Laravel server is running (`0.0.0.0:8000`).
2. Verify database connection using credentials in `.env`.
3. Check device heartbeat in database (`Device::where('device_code', 'LIB-001')->value('last_seen')`).
4. If testing hardware, verify `/dev/ttyUSB0` availability or WiFi ping at `192.168.100.155`.
