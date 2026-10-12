# iOS Attendance App Porting Specification (SwiftUI)

This document provides a complete technical specification, system architecture, API contracts, and business rules for porting the **Employee Attendance (Presensi Mandiri)** module to a native iOS application.

---

## 1. Executive Summary & Scope

### In-Scope (Employee Portal Only):
1. **Authentication & Session Management**
   - Multi-identifier login: Email, Employee ID (`EMP-xxxxx`), or 16-digit NIK.
   - Initial default password: Date of Birth (`DDMMYYYY`).
   - Token-based API auth using Laravel Sanctum with Keychain secure persistence.
2. **Mandatory First-Time Onboarding**
   - Detects if user still uses default password (`must_change_password == true`).
   - Forces password change with strict complexity rules before allowing portal access.
3. **Attendance Action Flow (State Machine)**
   - Sequential daily lifecycle:
     - `START_SHIFT` (Clock In / Masuk Kerja)
     - `START_BREAK` (Start Break / Mulai Istirahat)
     - `END_BREAK` (End Break / Selesai Istirahat)
     - `END_SHIFT` (Clock Out / Pulang Kerja)
4. **Geofencing & GPS Validation**
   - Live distance calculation to assigned workplace location using Haversine formula / CoreLocation.
   - Dynamic UI lock/unlock based on assigned radius (meters).
5. **Front Camera Selfie with Face Framing**
   - Native front-camera photo capture (`AVFoundation`).
   - Oval face framing guideline overlay with headroom indicator.
   - Prevention of gallery photo uploads (anti-fraud).
6. **Attendance History & Maps Pinpoint**
   - List of previous attendance records and events.
   - Map pinpoints (`MapKit`) showing coordinates of each recorded event.
7. **Employee Profile & Logout**
   - Basic identity display (Name, Employee ID, NIK, Workplace).
   - Secure logout and token invalidation.

### Out-of-Scope (Excluded from iOS App):
- Admin & HR Web Dashboard.
- Employee master data CRUD & shift scheduling.
- Excel/PDF report exports.
- RBAC role assignment management.

---

## 2. API Contracts & Specifications

- **Base URL:** `https://your-domain.com/api/v1`
- **Default Headers:**
  ```http
  Accept: application/json
  Content-Type: application/json
  ```
- **Authenticated Headers:**
  ```http
  Authorization: Bearer <sanctum_token>
  ```

---

### A. Authentication & Onboarding

#### 1. Login
- **Endpoint:** `POST /auth/login`
- **Request Body:**
  ```json
  {
    "login": "EMP-00329",
    "password": "12072004"
  }
  ```
  *(Field `login` accepts: Email, Employee ID e.g. `EMP-00329` / `329`, or 16-digit NIK)*

- **Success Response (200 OK):**
  ```json
  {
    "meta": {
      "success": true,
      "message": "Login successful"
    },
    "data": {
      "token": "32|hK49mQ08d...",
      "token_type": "Bearer",
      "expires_in": null,
      "user": {
        "id": 329,
        "name": "Arwindo Sendy Pratama",
        "email": "arw.prt@gmail.com",
        "employee_id": "EMP-00329",
        "nik": "3325061207040003",
        "dob": "2004-07-12",
        "must_change_password": true,
        "location": {
          "id": 1,
          "name": "Pabrik Kirana Garment",
          "latitude": -6.914744,
          "longitude": 107.609810,
          "radius": 100
        }
      }
    }
  }
  ```

#### 2. Update Password (Onboarding)
- **Endpoint:** `POST /onboarding/password`
- **Headers:** `Authorization: Bearer <token>`
- **Request Body:**
  ```json
  {
    "password": "NewSecurePassword2026!",
    "password_confirmation": "NewSecurePassword2026!"
  }
  ```
- **Password Complexity Rules:**
  - Length $\ge 8$ characters
  - Mixed case: contains both lowercase (`a-z`) and uppercase (`A-Z`)
  - Numbers: contains at least 1 digit (`0-9`)
  - Cannot match default birthday (`DDMMYYYY`) or generic `"password"`
  - Confirmation match

- **Success Response (200 OK):**
  ```json
  {
    "meta": {
      "success": true,
      "message": "Password updated successfully"
    }
  }
  ```

#### 3. Logout
- **Endpoint:** `POST /auth/logout`
- **Headers:** `Authorization: Bearer <token>`
- **Response (200 OK):**
  ```json
  {
    "meta": { "success": true, "message": "Logged out successfully" }
  }
  ```

---

### B. Attendance Endpoints

#### 1. Today's Attendance & Shift Status
- **Endpoint:** `GET /employee/attendance/today`
- **Headers:** `Authorization: Bearer <token>`
- **Success Response (200 OK):**
  ```json
  {
    "data": {
      "date": "2026-10-12",
      "status": "EXACT",
      "next_allowed_action": "START_SHIFT",
      "active_break": false,
      "shift": {
        "id": 1,
        "name": "Shift Pagi Garment",
        "start_time": "08:00:00",
        "end_time": "17:00:00"
      },
      "work_location": {
        "id": 1,
        "name": "Pabrik Garment Utama",
        "latitude": -6.914744,
        "longitude": 107.609810,
        "radius": 100
      },
      "events": [
        {
          "id": 105,
          "event_type": "START_SHIFT",
          "timestamp": "2026-10-12 07:54:20",
          "latitude": -6.914750,
          "longitude": 107.609812,
          "selfie_url": "https://storage.yourdomain.com/attendance-selfies/selfie_105.jpg"
        }
      ]
    }
  }
  ```

#### 2. Record Attendance Event
- **Endpoint:** `POST /employee/attendance/events`
- **Headers:**
  - `Authorization: Bearer <token>`
  - `Content-Type: multipart/form-data`
- **Multipart Form Data Parameters:**
  - `event_type`: (String) `START_SHIFT` | `START_BREAK` | `END_BREAK` | `END_SHIFT`
  - `latitude`: (Double) e.g. `-6.914744`
  - `longitude`: (Double) e.g. `107.609810`
  - `selfie`: (File) JPEG image captured from front camera (Max 10 MB)
- **Response (201 Created):**
  ```json
  {
    "meta": { "success": true, "message": "Kehadiran berhasil dicatat!" },
    "data": {
      "event_type": "START_SHIFT",
      "timestamp": "2026-10-12 07:58:10",
      "status": "EXACT"
    }
  }
  ```
- **Error Response (422 Unprocessable / Geofence Breach):**
  ```json
  {
    "meta": {
      "success": false,
      "message": "Jarak Anda 185 meter. Melebihi radius maksimal 100 meter dari lokasi kerja."
    }
  }
  ```

#### 3. Attendance History
- **Endpoint:** `GET /employee/attendance/history?page=1`
- **Headers:** `Authorization: Bearer <token>`
- **Response (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 42,
        "date": "2026-10-11",
        "status": "EXACT",
        "events": [
          {
            "event_type": "START_SHIFT",
            "timestamp": "2026-10-11 07:55:00",
            "latitude": -6.914744,
            "longitude": 107.609810
          },
          {
            "event_type": "END_SHIFT",
            "timestamp": "2026-10-11 17:02:15",
            "latitude": -6.914740,
            "longitude": 107.609815
          }
        ]
      }
    ]
  }
  ```

---

## 3. iOS Project Architecture (SwiftUI + MVVM + Clean Architecture)

```
KiranaAttendance/
├── App/
│   ├── KiranaAttendanceApp.swift     // App Lifecycle
│   └── AppCoordinator.swift          // Auth & Navigation Root Switcher
├── Core/
│   ├── Network/
│   │   ├── APIClient.swift           // async/await URLSession wrapper
│   │   ├── Endpoint.swift            // Route & HTTP method definitions
│   │   └── NetworkError.swift        // Error parsing & 401 handling
│   ├── Location/
│   │   ├── LocationService.swift     // CLLocationManager wrapper
│   │   └── GeofenceCalculator.swift  // Haversine & distance evaluation
│   ├── Security/
│   │   └── KeychainManager.swift     // Secure Bearer Token storage
│   └── Camera/
│       ├── CameraController.swift    // AVFoundation front-camera controller
│       └── FaceOverlayView.swift     // Headroom framing guide
├── Models/
│   ├── User.swift
│   ├── WorkLocation.swift
│   ├── Shift.swift
│   ├── Attendance.swift
│   └── AttendanceEvent.swift
└── Features/
    ├── Authentication/
    │   ├── Views/LoginView.swift
    │   └── ViewModels/LoginViewModel.swift
    ├── Onboarding/
    │   ├── Views/OnboardingPasswordView.swift
    │   └── ViewModels/OnboardingViewModel.swift
    ├── Attendance/
    │   ├── Views/AttendanceHomeView.swift
    │   ├── Views/CameraSelfieView.swift
    │   ├── ViewModels/AttendanceViewModel.swift
    │   └── Components/
    │       ├── LiveClockCard.swift
    │       ├── GeofenceStatusBadge.swift
    │       └── ActionButtonsView.swift
    ├── History/
    │   ├── Views/AttendanceHistoryView.swift
    │   ├── Views/EventMapDetailView.swift
    │   └── ViewModels/HistoryViewModel.swift
    └── Profile/
        ├── Views/ProfileView.swift
        └── ViewModels/ProfileViewModel.swift
```

---

## 4. Key Implementation Logic for iOS

### A. Geofence & Distance Validation
```swift
import Foundation
import CoreLocation

struct GeofenceCalculator {
    /// Calculate distance in meters between user position and assigned workplace
    static func distance(from user: CLLocationCoordinate2D, to office: CLLocationCoordinate2D) -> Double {
        let userLoc = CLLocation(latitude: user.latitude, longitude: user.longitude)
        let officeLoc = CLLocation(latitude: office.latitude, longitude: office.longitude)
        return userLoc.distance(from: officeLoc)
    }

    /// Evaluates if user coordinate is within the permitted radius
    static func isAllowed(user: CLLocationCoordinate2D, office: CLLocationCoordinate2D, maxRadiusMeters: Double) -> (isWithin: Bool, distance: Double) {
        let dist = distance(from: user, to: office)
        return (dist <= maxRadiusMeters, dist)
    }
}
```

### B. Attendance State Machine
```swift
enum AttendanceAction: String, CaseIterable {
    case startShift = "START_SHIFT"
    case startBreak = "START_BREAK"
    case endBreak   = "END_BREAK"
    case endShift   = "END_SHIFT"
}

enum AttendancePhase {
    case notStarted          // Can only START_SHIFT
    case working             // Can START_BREAK or END_SHIFT
    case onBreak             // Can only END_BREAK
    case completed           // Shift ended for today
    
    init(events: [AttendanceEvent]) {
        guard let last = events.last?.eventType else {
            self = .notStarted
            return
        }
        switch last {
        case .startShift, .endBreak:
            self = .working
        case .startBreak:
            self = .onBreak
        case .endShift:
            self = .completed
        }
    }
}
```

### C. Password Strength Validator (Matching Backend Rules)
```swift
struct PasswordPolicy {
    static func validate(password: String, confirmation: String, defaultPassword: String) -> (isValid: Bool, errors: [String]) {
        var errors: [String] = []
        
        if password.count < 8 {
            errors.append("Minimal 8 karakter")
        }
        if password.rangeOfCharacter(from: .uppercaseLetters) == nil ||
           password.rangeOfCharacter(from: .lowercaseLetters) == nil {
            errors.append("Harus mengandung huruf besar & kecil (A-Z, a-z)")
        }
        if password.rangeOfCharacter(from: .decimalDigits) == nil {
            errors.append("Harus mengandung minimal 1 angka (0-9)")
        }
        if password == defaultPassword || password.lowercased() == "password" {
            errors.append("Tidak boleh sama dengan password default / tanggal lahir")
        }
        if password != confirmation {
            errors.append("Konfirmasi kata sandi tidak cocok")
        }
        
        return (errors.isEmpty, errors)
    }
}
```

### D. Native Camera Selfie with Oval Framing
- Use `AVCaptureSession` configured with `.builtInWideAngleCamera` and position `.front`.
- Display a semi-transparent dark mask with a central oval cut-out (`UIBezierPath(ovalIn:)`) to guide proper face alignment.
- Compress captured frame to JPEG with `compressionQuality: 0.75` before uploading via multipart form.
- Disallow `PhotosPicker` to prevent fraudulent check-in photos.

---

## 5. Security & Device Integrity

1. **Anti-Mock Location Check:**
   - In iOS 15+, inspect `CLLocation.isProducedByAccessory` and `CLLocation.isMockLocation` (in debug/simulator test).
   - Require `horizontalAccuracy < 30.0` meters before enabling the submission button.
2. **Keychain Storage:**
   - Store access tokens in Apple Keychain with `kSecAttrAccessibleAfterFirstUnlockThisDeviceOnly`.
3. **Session Invalidation:**
   - Automatically intercept HTTP 401 or HTTP 403 (`requires_onboarding: true`) in `APIClient` to trigger coordinate navigation.

---

## 6. iOS Permissions Config (`Info.plist`)

```xml
<key>NSCameraUsageDescription</key>
<string>Aplikasi memerlukan kamera depan untuk verifikasi foto selfie saat melakukan presensi.</string>
<key>NSLocationWhenInUseUsageDescription</key>
<string>Aplikasi memerlukan lokasi GPS untuk memvalidasi radius kerja (geofencing) presensi.</string>
```
