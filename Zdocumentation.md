# Event Booking System API Documentation

Base URL: [http://localhost:8000](http://localhost:8000)
Authentication: Bearer Token (Sanctum) required for protected routes

---

## 1. Authentication

### 1.1 Register

Endpoint: POST /api/register
Description: Create a new user account.
Request Body (JSON):
{
"name": "John Doe",
"email": "[john@example.com](mailto:john@example.com)",
"password": "password",
"password_confirmation": "password",
"phone": "0650052037",
"role": "customer"
}
Response Example:
{
"message": "User registered successfully",
"user": {
"id": 1,
"name": "John Doe",
"email": "[john@example.com](mailto:john@example.com)",
"role": "customer"
},
"access_token": "your_access_token_here",
"token_type": "Bearer"
}

### 1.2 Login

Endpoint: POST /api/login
Request Body (JSON):
{
"email": "[john@example.com](mailto:john@example.com)",
"password": "password"
}
Response Example:
{
"message": "Login successful",
"user": {
"id": 1,
"name": "John Doe",
"email": "[john@example.com](mailto:john@example.com)",
"role": "customer"
},
"access_token": "your_access_token_here",
"token_type": "Bearer"
}

### 1.3 Logout

Endpoint: POST /api/logout
Headers: Authorization: Bearer {{auth_token}}
Response Example:
{
"message": "Logged out successfully"
}

### 1.4 Get Current User

Endpoint: GET /api/me
Headers: Authorization: Bearer {{auth_token}}
Response Example:
{
"id": 1,
"name": "John Doe",
"email": "[john@example.com](mailto:john@example.com)",
"role": "customer"
}

---

## 2. Events

### 2.1 Get All Events

Endpoint: GET /api/events
Description: Retrieve paginated list of all events.
Response Example:
{
"message": "Events retrieved successfully",
"data": [
{
"id": 1,
"title": "Summer Music Festival",
"date": "2024-07-15 18:00:00",
"location": "Central Park, New York"
}
]
}

### 2.2 Get Single Event

Endpoint: GET /api/events/{id}
Response Example:
{
"message": "Event retrieved successfully",
"data": {
"id": 1,
"title": "Summer Music Festival",
"description": "Amazing music festival with top artists",
"date": "2024-07-15 18:00:00",
"location": "Central Park, New York",
"created_by": 2
}
}

### 2.3 Create Event (Organizer Only)

Endpoint: POST /api/events
Headers: Authorization: Bearer {{organizer_token}}
Request Body (JSON):
{
"title": "Summer Music Festival",
"description": "Amazing music festival with top artists",
"date": "2024-07-15 18:00:00",
"location": "Central Park, New York"
}
Response Example:
{
"message": "Event created successfully",
"data": {
"id": 1,
"title": "Summer Music Festival",
"date": "2024-07-15 18:00:00",
"location": "Central Park, New York"
}
}

### 2.4 Update Event (Organizer Only)

Endpoint: PUT /api/events/{id}
Headers: Authorization: Bearer {{organizer_token}}
Request Body (JSON):
{
"title": "Updated Event Title",
"date": "2024-07-16 18:00:00"
}
Response Example:
{
"message": "Event updated successfully",
"data": {
"id": 1,
"title": "Updated Event Title",
"date": "2024-07-16 18:00:00",
"location": "Central Park, New York"
}
}

### 2.5 Delete Event (Organizer Only)

Endpoint: DELETE /api/events/{id}
Headers: Authorization: Bearer {{organizer_token}}
Response Example:
{
"message": "Event deleted successfully"
}

---

## 3. Tickets (Organizer Only)

### 3.1 Create Ticket

Endpoint: POST /api/events/{event}/tickets
Headers: Authorization: Bearer {{organizer_token}}
Request Body (JSON):
{
"type": "VIP",
"price": 100,
"quantity": 50
}
Response Example:
{
"message": "Ticket created successfully",
"data": {
"id": 1,
"type": "VIP",
"price": 100,
"quantity": 50,
"event_id": 1
}
}

### 3.2 Update Ticket

Endpoint: PUT /api/tickets/{id}
Headers: Authorization: Bearer {{organizer_token}}

### 3.3 Delete Ticket

Endpoint: DELETE /api/tickets/{id}
Headers: Authorization: Bearer {{organizer_token}}

---

## 4. Bookings (Customer Only)

### 4.1 Get My Bookings

Endpoint: GET /api/bookings
Headers: Authorization: Bearer {{customer_token}}

### 4.2 Create Booking

Endpoint: POST /api/tickets/{ticket}/bookings
Headers: Authorization: Bearer {{customer_token}}
Middleware: prevent.duplicate.booking
Request Body (JSON):
{
"quantity": 2
}

### 4.3 Cancel Booking

Endpoint: PUT /api/bookings/{booking}/cancel
Headers: Authorization: Bearer {{customer_token}}

---

## 5. Payments (Customer Only)

### 5.1 Create Payment

Endpoint: POST /api/bookings/{booking}/payment
Headers: Authorization: Bearer {{customer_token}}

### 5.2 Get Payment

Endpoint: GET /api/payments/{id}
Headers: Authorization: Bearer {{customer_token}}

---

### Variables

* {{base_url}} = [http://localhost:8000](http://localhost:8000)
* {{auth_token}} = Access token for authenticated user
* {{organizer_token}} = Access token for organizer
* {{customer_token}} = Access token for customer
