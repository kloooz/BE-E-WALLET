# E-Wallet API Documentation

Base URL: `http://localhost:8000/api`

---

## Authentication

### 1. Register
Endpoint to register a new user and automatically create their wallet.

- **URL:** `/register`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "username": "johndoe",
      "email": "johndoe@example.com",
      "phone": "081234567890",
      "password": "password123",
      "password_confirmation": "password123",
      "pin": "123456"
  }
  ```
- **Response (201 Created):**
  ```json
  {
      "success": true,
      "message": "User registered successfully",
      "data": {
          "user": { ... },
          "token": "1|abcdef..."
      }
  }
  ```

### 2. Login
Endpoint to authenticate a user and get an access token.

- **URL:** `/login`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "email": "johndoe@example.com",
      "password": "password123"
  }
  ```
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Login successful",
      "data": {
          "user": { ... },
          "token": "2|abcdef..."
      }
  }
  ```

### 3. Forgot Password
Endpoint to generate a password reset token.

- **URL:** `/forgot-password`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "email": "johndoe@example.com"
  }
  ```
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Password reset email sent",
      "data": null
  }
  ```

### 4. Reset Password
Endpoint to reset the user's password using the generated token.

- **URL:** `/reset-password`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "email": "johndoe@example.com",
      "token": "random_60_char_token",
      "password": "newpassword123",
      "password_confirmation": "newpassword123"
  }
  ```
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Password reset successfully",
      "data": null
  }
  ```

---

## Public (Webhook) Endpoints

### 5. Midtrans Webhook
Endpoint to receive server-to-server notifications from Midtrans regarding payment status updates.

- **URL:** `/webhook/midtrans`
- **Method:** `POST`
- **Body (JSON):** *(Sent by Midtrans)*
  ```json
  {
      "order_id": "TOP_1711234567_1234",
      "transaction_status": "settlement",
      "gross_amount": "100000.00",
      ...
  }
  ```
- **Response (200 OK):**
  ```json
  {
      "message": "Webhook processed successfully"
  }
  ```

---

## Protected Endpoints
> Note: All subsequent endpoints require an `Authorization` header with a valid bearer token:
> `Authorization: Bearer <your_token>`

### 6. Get User Profile
Endpoint to retrieve the authenticated user's profile information.

- **URL:** `/profile`
- **Method:** `GET`
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "User profile retrieved successfully",
      "data": {
          "user": {
              "id": 1,
              "username": "johndoe",
              "email": "johndoe@example.com",
              "phone": "081234567890",
              "email_verified_at": null,
              "created_at": "2024-03-02T10:00:00.000000Z",
              "updated_at": "2024-03-02T10:00:00.000000Z"
          }
      }
  }
  ```

### 6. Check Balance
Endpoint to retrieve the current wallet balance.

- **URL:** `/balance`
- **Method:** `GET`
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Balance retrieved",
      "data": {
          "balance": 500000
      }
  }
  ```

### 7. Top Up Balance (Midtrans Integration)
Endpoint to initiate a top-up transaction and generate a Midtrans Snap token.

- **URL:** `/topup`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "amount": 100000
  }
  ```
- **Response (201 Created):**
  ```json
  {
      "success": true,
      "message": "Top up initiated",
      "data": {
          "transaction": {
              "id": 1,
              "user_id": 1,
              "type": "topup",
              "amount": 100000,
              "status": "pending",
              "snap_token": "token_string_here...",
              "reference_id": "TOP_1711234567_1234",
              "description": "Top up balance via Midtrans"
          },
          "snap_token": "token_string_here..."
      }
  }
  ```
  *(Note: Actual balance will be added when Midtrans sends the success webhook)*

### 8. Transfer Balance
Endpoint to transfer balance to another user's wallet.

- **URL:** `/transfer`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "identifier": "janedoe@example.com",
      "amount": 50000
  }
  ```
- **Response (201 Created):**
  ```json
  {
      "success": true,
      "message": "Transfer successful",
      "data": {
          "transaction": { ... }
      }
  }
  ```

### 9. Transaction History
Endpoint to retrieve the current user's transaction history (top-ups, transfers, payments).

- **URL:** `/transactions`
- **Method:** `GET`
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Transactions retrieved",
      "data": [
          {
              "id": 1,
              "type": "topup",
              "amount": 100000,
              "status": "success",
              "description": "Top up balance via Midtrans",
              "created_at": "2024-03-02T10:00:00.000000Z"
          },
          {
              "id": 2,
              "type": "payment",
              "amount": 15000,
              "status": "success",
              "description": "Payment to Warteg Berkah",
              "created_at": "2024-03-02T10:05:00.000000Z"
          }
      ]
  }
  ```

### 10. Generate Dummy QR
Endpoint to generate a dummy QR Code string for testing the Scan QR Payment feature.

- **URL:** `/dummy-qr`
- **Method:** `GET`
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Dummy QR generated",
      "data": {
          "qr_string": "base64_encoded_string_here...",
          "decoded_data": {
              "merchant_id": "MCH-12345",
              "merchant_name": "Warteg Berkah",
              "amount": 15000,
              "transaction_id": "QR_1711234567123"
          }
      }
  }
  ```

### 11. Scan QR Payment
Endpoint to process a payment using a scanned QR code string.

- **URL:** `/scan-qr`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "qr_code": "base64_encoded_string_here...",
      "pin": "123456"
  }
  ```
- **Response (200 OK):**
  *(Deducts wallet balance and triggers "Payment Successful" email)*
  ```json
  {
      "success": true,
      "message": "Payment successful",
      "data": {
          "transaction": {
              "id": 3,
              "user_id": 1,
              "type": "payment",
              "amount": 15000,
              "status": "success",
              "reference_id": "QR_1711234567123",
              "description": "Payment to Warteg Berkah"
          }
      }
  }
  ```

---
