# Kirana People - Forgot & Reset Password Specification

## 1. Understanding Summary
* **Feature**: Forgot Password & Reset Password system.
* **Channels**: Both Web Portal (Blade UI) and Mobile App (REST API).
* **Delivery Driver**: Initially using Laravel's `log` mailer (`MAIL_MAILER=log`) for inspection and testing.
* **Verification Method**: Hybrid notification containing both a direct Web link (token) and a 6-digit numeric OTP (for mobile apps).
* **Expiry Window**: 15 minutes.
* **Constraints**:
  * Enforce strict password complexity (minimum 8 characters, letters, mixed case, numbers, confirmed).
  * Disallow default birth date or generic passwords.
  * Prevent user enumeration on both web and API.
  * Rate-limit both request and reset attempts.

---

## 2. Decision Log

| Decision | Alternatives Considered | Rationale |
|---|---|---|
| **Scope** | Web only vs Mobile only | Unified feature for both Web Portal and Mobile REST API users. |
| **Verification Method** | Token link only vs OTP only | Hybrid approach: Web users get a clickable link; mobile users get a 6-digit OTP code in the same email. |
| **Storage Architecture** | Redis cache vs Separate OTP tables | Approach 1: Unified store leveraging `password_reset_tokens` table for reliability and durability across deployments. |
| **Expiration Window** | 60 minutes vs 30 minutes | 15 minutes for heightened security, particularly for numeric OTPs. |
| **Session Cleanup** | Retain existing tokens vs Revoke | Revoke existing Sanctum tokens upon successful reset to secure compromised accounts. |

---

## 3. Database Schema

Migration to extend `password_reset_tokens`:
```sql
ALTER TABLE password_reset_tokens ADD COLUMN otp VARCHAR(255) NULL AFTER token;
```
* `email`: string, primary key.
* `token`: string (hashed or random 64-char string for Web reset link).
* `otp`: string (hashed 6-digit numeric OTP for Mobile API).
* `created_at`: timestamp (used to verify 15-minute expiration).

---

## 4. Endpoints & Flow

### Web Portal Flow
* **`GET /forgot-password`** (`Auth\ForgotPasswordController@showLinkRequestForm`)
  * Displays email input form with Kirana People styling.
* **`POST /forgot-password`** (`Auth\ForgotPasswordController@sendResetLinkEmail`)
  * Validates email (`required|email`).
  * If user exists: generates 64-char token and random 6-digit OTP, updates `password_reset_tokens`, dispatches `ResetPasswordNotification`.
  * Returns generic success message to prevent user enumeration: *"Jika email terdaftar, instruksi reset kata sandi telah dikirimkan."*
* **`GET /reset-password/{token}`** (`Auth\ResetPasswordController@showResetForm`)
  * Displays password reset form with hidden token and email.
* **`POST /reset-password`** (`Auth\ResetPasswordController@reset`)
  * Validates `token`, `email`, `password`, `password_confirmation`.
  * Verifies 15-minute validity window.
  * Updates password, invalidates token/OTP record, revokes old sessions/tokens, redirects to `/` with success toast.

### Mobile App Flow (REST API)
* **`POST /api/v1/auth/forgot-password`** (`Api\V1\AuthController@forgotPassword`)
  * Throttled: `throttle:3,15`.
  * Body: `{ "email": "employee@example.com" }`
  * Response: `{ "status": "success", "message": "Kode OTP reset kata sandi telah dikirim ke email Anda." }`
* **`POST /api/v1/auth/reset-password`** (`Api\V1\AuthController@resetPassword`)
  * Throttled: `throttle:5,15`.
  * Body:
    ```json
    {
      "email": "employee@example.com",
      "otp": "482910",
      "password": "NewPassword123!",
      "password_confirmation": "NewPassword123!"
    }
    ```
  * Verifies OTP match and 15-minute expiration.
  * Updates password, invalidates OTP, revokes Sanctum tokens.
  * Response: `{ "status": "success", "message": "Kata sandi Anda berhasil diperbarui. Silakan masuk kembali." }`

---

## 5. Security & Rate Limiting
1. **User Enumeration**: Always return 200 OK with identical success messages.
2. **Brute Force Protection**:
   * Request throttling: max 3 requests per 15 minutes.
   * Reset attempt throttling: max 5 attempts per 15 minutes.
3. **Complexity & Integrity**:
   * Password validation using `Illuminate\Validation\Rules\Password`.
   * Check against default birthday / `"password"`.

---

## 6. Testing Strategy
* Feature tests covering:
  * Successful web link generation & reset.
  * Successful mobile OTP generation & reset via API.
  * Expired token/OTP (>15 min) rejection.
  * Invalid OTP rejection.
  * Rate-limiting enforcement.
