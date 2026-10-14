# Design Specification: Employee Self-Service Profile Edit (Web Version)

## 1. Overview & Understanding Summary
* **What is being built:** A self-service profile edit capability within the employee attendance web application (`/attendance`), accessed via a modal / bottom sheet on the Profile tab.
* **Why it exists:** Regular employees currently only have read-only access to their profile and cannot update their avatar, contact phone number, or password without HR/Admin assistance.
* **Who it is for:** Authenticated employees using the mobile-first web interface (`/attendance/profile`).
* **Key constraints:**
  * Only personal and security attributes are editable: **Avatar (photo)**, **Phone Number**, and **Password**.
  * HR & organizational identity fields remain locked and read-only: **Employee ID**, **NIK (KTP)**, **Work Location**, **Full Name**, and **Email**.
  * Password change requires verifying the **Current Password** before updating.
  * Image uploads adhere to existing app architecture using `FileUploadService` supporting Cloudflare R2 and database base64 fallback (max 5MB).
* **Explicit non-goals:**
  * No modification of attendance records, shifts, or locations.
  * No modification of HR-controlled identity data (Employee ID, NIK, Full Name, Email).
  * No multi-page redirection away from the unified single-page tab experience.

---

## 2. Assumptions & Non-Functional Requirements
* **Security & Authorization:**
  * Scoped strictly to the authenticated user (`Auth::user()`).
  * New password requires `min:8`, confirmation matching, and verification of `current_password`.
  * Passwords must be hashed using `Hash::make()`.
* **Performance & Scale:**
  * Modal rendered directly in Alpine.js without full-page reloads.
  * Instant client-side avatar image preview using `URL.createObjectURL()`.
* **Reliability & Resilience:**
  * Image uploads leverage `FileUploadService` to upload to Cloudflare R2 and store a base64 fallback in MySQL, preventing broken avatars in serverless/containerized deployments.
  * Old avatar cleanup from storage to avoid orphaned files.

---

## 3. Decision Log
| Decision | Options Considered | Rationale |
|---|---|---|
| **Editable Fields** | 1. Personal & Security only (Avatar, Phone, Password)<br>2. Basic info (Name, DOB, Phone, Password)<br>3. Full self-service | Prevents tampering with payroll/legal/HR records (NIK, Employee ID, Location, Name) while giving employees self-management of avatar, contact, and credentials. |
| **UI Presentation** | 1. Alpine.js Bottom Sheet / Modal in Profile tab<br>2. Inline card toggle<br>3. Standalone `/attendance/profile/edit` page | Modal preserves the fluid SPA mobile-first design, keeps layout uncluttered, and integrates smoothly into `index.blade.php`. |
| **Security Verification** | 1. Require `current_password`<br>2. Allow direct change | Industry best practice preventing account takeover if an employee leaves a session active on a shared workstation or device. |

---

## 4. Final Architecture & Technical Design

### 4.1 Routing (`routes/web.php`)
Register a protected route under `middleware(['auth', 'password.changed'])`:
```php
Route::put('/attendance/profile', [AttendanceController::class, 'updateProfile'])->name('attendance.profile.update');
```

### 4.2 Controller Logic (`AttendanceController@updateProfile`)
* **Validation Rules:**
  ```php
  $validated = $request->validate([
      'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
      'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
      'current_password' => 'required_with:password|current_password:web',
      'password' => 'nullable|string|min:8|confirmed',
  ], [
      'current_password.current_password' => 'Current password does not match.',
      'password.min' => 'New password must be at least 8 characters.',
      'password.confirmed' => 'New password confirmation does not match.',
      'avatar.max' => 'Profile picture size must not exceed 5MB.',
      'avatar.image' => 'The file must be an image.',
  ]);
  ```
* **Avatar Upload & Cleanup:**
  ```php
  if ($request->hasFile('avatar')) {
      if ($user->avatar) {
          try { Storage::disk('r2')->delete($user->avatar); } catch (\Throwable $e) {}
          try { Storage::disk('public')->delete($user->avatar); } catch (\Throwable $e) {}
      }
      $uploaded = $fileUploadService->uploadImageWithBase64($request->file('avatar'), 'avatars');
      $validated['avatar'] = $uploaded['path'];
      $validated['avatar_base64'] = $uploaded['base64'];
  }
  ```
* **Password Hashing:**
  ```php
  if (!empty($validated['password'])) {
      $validated['password'] = Hash::make($validated['password']);
  } else {
      unset($validated['password']);
  }
  unset($validated['current_password']);
  ```
* **Persistence & Redirect:**
  ```php
  $user->update($validated);
  return redirect()->route('attendance.profile')->with('success', 'Profile updated successfully!');
  ```

### 4.3 Frontend UI Component (`resources/views/attendance/index.blade.php`)
1. **Profile Tab Header:**
   * Display Phone Number in the profile card list:
     ```blade
     <div>
         <p class="text-xs text-gray-500 mb-1">Phone Number</p>
         <p class="font-medium text-gray-200">{{ $user->phone ?? 'Not set' }}</p>
     </div>
     ```
   * Add **"Edit Profile"** trigger button:
     ```blade
     <button type="button" @click="showEditProfileModal = true" class="mt-4 px-4 py-2 bg-orange-600/20 hover:bg-orange-600/30 text-orange-400 border border-orange-500/40 rounded-xl text-xs font-semibold flex items-center justify-center space-x-2 transition">
         <i class="fa-solid fa-user-pen"></i>
         <span>Edit Profil</span>
     </button>
     ```

2. **Modal / Bottom Sheet:**
   * Managed via Alpine data: `showEditProfileModal: false`, `avatarPreview: null`.
   * Auto-reopen on validation error redirect:
     ```blade
     @if($errors->any())
         x-init="showEditProfileModal = true"
     @endif
     ```
   * Form elements:
     * Hidden file input `<input type="file" name="avatar" accept="image/*">` triggered by camera badge on preview image.
     * Phone number text input with `old('phone', $user->phone)`.
     * Password change section with `current_password`, `password`, and `password_confirmation`.
     * Dismiss and Submit buttons.

---

## 5. Verification & Testing Checklist
- [ ] **TC-01: Update Phone Only** — Successfully update phone number, verify database value and UI refresh.
- [ ] **TC-02: Update Avatar** — Select JPEG/PNG image, verify live preview, upload success to R2/DB fallback, and updated avatar display.
- [ ] **TC-03: Change Password Success** — Provide valid current password and matching new password, confirm successful update and login with new credentials.
- [ ] **TC-04: Wrong Current Password** — Attempt to set new password with invalid current password, verify validation failure and error message display.
- [ ] **TC-05: Mismatched New Password** — Verify `password_confirmation` validation failure.
- [ ] **TC-06: File Size Limit** — Attempt to upload file $> 5\text{MB}$, verify friendly rejection.
