# E-Wallet API Documentation

Base URL: `http://localhost:8000/api`

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
      "password_confirmation": "password123"
  }
  ```
- **Response (201 Created):**
  ```json
  {
      "success": true,
      "message": "Registration successful",
      "data": {
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
      "message": "Password reset token generated",
      "data": {
          "token": "random_60_char_token",
          "message": "Reset token generated (Usually sent to email)"
      }
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
      "message": "Password has been successfully reset",
      "data": []
  }
  ```

---

## Protected Endpoints
> Note: All subsequent endpoints require an `Authorization` header with a valid bare token:
> `Authorization: Bearer <your_token>`

### 5. Check Balance
Endpoint to retrieve the current wallet balance.

- **URL:** `/balance`
- **Method:** `GET`
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Balance retrieved successfully",
      "data": {
          "balance": 500000
      }
  }
  ```

### 6. Top Up Balance
Endpoint to add balance to the user's wallet.

- **URL:** `/topup`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "amount": 100000
  }
  ```
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Top up successful",
      "data": {
          "balance": 600000
      }
  }
  ```

### 7. Transfer Balance
Endpoint to transfer balance to another user's wallet.

- **URL:** `/transfer`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
      "to_username": "janedoe",
      "amount": 50000
  }
  ```
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Transfer successful",
      "data": {
          "balance": 550000,
          "transferred": 50000,
          "to": "janedoe"
      }
  }
  ```

### 8. Transaction History
Endpoint to retrieve the current user's transaction history (both top-ups and transfers).

- **URL:** `/transactions`
- **Method:** `GET`
- **Response (200 OK):**
  ```json
  {
      "success": true,
      "message": "Transactions retrieved successfully",
      "data": [
          {
              "id": 1,
              "wallet_id": 1,
              "type": "topup",
              "amount": "100000.00",
              "description": "Top Up",
              "created_at": "2024-03-02T10:00:00.000000Z"
          },
          {
              "id": 2,
              "wallet_id": 1,
              "type": "transfer",
              "amount": "-50000.00",
              "description": "Transfer to janedoe",
              "created_at": "2024-03-02T10:05:00.000000Z"
          }
      ]
  }
  ```

## Dummy Data for Testing
For testing, a seed has been provided via `php artisan db:seed`:
- **Username:** `testuser`
- **Email:** `test@example.com`
- **Password:** `password`
- **Wallet Balance:** `0`
