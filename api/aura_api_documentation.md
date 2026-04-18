# AURA Dating Application - API Structure Documentation

## Base URL
```
Production: https://api.aura-dating.com/v1
Staging: https://api.staging.aura-dating.com/v1
Development: http://localhost:8000/api/v1
```

## Authentication
All endpoints (except public ones) require Bearer Token authentication:
```
Authorization: Bearer {access_token}
```

---

## 1. AUTHENTICATION ENDPOINTS

### POST /auth/register
Register a new user with email or phone.

**Request Body:**
```json
{
  "email": "user@example.com",
  "phone": "+1234567890",
  "password": "securePassword123",
  "first_name": "John",
  "last_name": "Doe",
  "date_of_birth": "1995-06-15",
  "gender": "male"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Registration successful. Please verify your account.",
  "data": {
    "user_id": "uuid-string",
    "access_token": "eyJhbGciOiJIUzI1NiIs...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

---

### POST /auth/login
Authenticate user and get access token.

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "securePassword123"
}
```
OR
```json
{
  "phone": "+1234567890",
  "password": "securePassword123"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user_id": "uuid-string",
    "access_token": "eyJhbGciOiJIUzI1NiIs...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "is_profile_complete": true
  }
}
```

---

### POST /auth/otp/send
Send OTP for verification.

**Request Body:**
```json
{
  "identifier": "+1234567890",
  "type": "phone"
}
```
OR
```json
{
  "identifier": "user@example.com",
  "type": "email"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "otp_id": "uuid-string",
    "expires_in": 300
  }
}
```

---

### POST /auth/otp/verify
Verify OTP code.

**Request Body:**
```json
{
  "otp_id": "uuid-string",
  "otp_code": "123456"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Phone/Email verified successfully",
  "data": {
    "is_verified": true
  }
}
```

---

### POST /auth/refresh
Refresh access token.

**Request Body:**
```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIs..."
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "access_token": "new_access_token...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

---

### POST /auth/logout
Logout and invalidate token.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 2. USER PROFILE ENDPOINTS

### GET /users/me
Get current user profile.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": "uuid-string",
    "email": "user@example.com",
    "phone": "+1234567890",
    "is_verified": true,
    "is_premium": true,
    "profile": {
      "first_name": "John",
      "last_name": "Doe",
      "date_of_birth": "1995-06-15",
      "age": 30,
      "gender": "male",
      "interested_in": "female",
      "bio": "Love traveling and photography...",
      "distance": 5.2,
      "location_updated_at": "2026-03-05T10:00:00Z",
      "hobbies": ["Hiking", "Photography", "Traveling"],
      "images": [
        {
          "id": 1,
          "url": "https://cdn.aura.com/images/1.jpg",
          "thumbnail_url": "https://cdn.aura.com/thumbnails/1.jpg",
          "is_primary": true
        }
      ],
      "profile_completed": true
    }
  }
}
```

---

### PUT /users/me
Update current user profile.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "bio": "Updated bio...",
  "gender": "male",
  "interested_in": "female"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "profile": { ... }
  }
}
```

---

### PUT /users/me/location
Update user location for discovery.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "latitude": 40.7128,
  "longitude": -74.0060
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Location updated"
}
```

---

### POST /users/me/images
Upload profile image.

**Headers:** 
- Bearer Token required
- Content-Type: multipart/form-data

**Request Body:**
- `image`: file (max 5MB, jpg/png/webp)
- `is_primary`: boolean (optional)

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "url": "https://cdn.aura.com/images/uuid.jpg",
    "thumbnail_url": "https://cdn.aura.com/thumbnails/uuid.jpg",
    "is_primary": true
  }
}
```

---

### DELETE /users/me/images/{id}
Delete profile image.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Image deleted successfully"
}
```

---

### PUT /users/me/hobbies
Update user hobbies.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "hobby_ids": [1, 2, 3, 5, 8]
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "hobbies": ["Hiking", "Photography", "Traveling", "Reading", "Music"]
  }
}
```

---

### GET /users/me/discovery-settings
Get discovery preferences.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "max_distance": 50,
    "min_age": 18,
    "max_age": 45,
    "interested_in": "female"
  }
}
```

---

### PUT /users/me/discovery-settings
Update discovery preferences.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "max_distance": 30,
  "min_age": 21,
  "max_age": 35,
  "interested_in": "everyone"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Discovery settings updated"
}
```

---

## 3. DISCOVERY ENDPOINTS

### GET /discovery/nearby
Get nearby users for discovery (swiping).

**Headers:** Bearer Token required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| latitude | float | user location | Latitude coordinate |
| longitude | float | user location | Longitude coordinate |
| distance | int | 50 | Max distance in km |
| min_age | int | 18 | Minimum age filter |
| max_age | int | 100 | Maximum age filter |
| gender | string | user's preference | Filter by gender |
| page | int | 1 | Page number |
| per_page | int | 10 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": "uuid-string",
        "distance": 2.5,
        "age": 28,
        "first_name": "Sarah",
        "bio": "Love hiking and coffee...",
        "hobbies": ["Hiking", "Coffee", "Traveling"],
        "images": [
          {
            "id": 1,
            "url": "https://cdn.aura.com/images/1.jpg",
            "is_primary": true
          }
        ],
        "common_hobbies": ["Hiking", "Traveling"],
        "last_active_at": "2026-03-05T09:30:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "total": 50,
    "has_more": true
  }
}
```

---

### GET /discovery/users/{id}
Get a specific user's profile (for viewing before swiping).

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": "uuid-string",
    "distance": 2.5,
    "age": 28,
    "first_name": "Sarah",
    "bio": "Love hiking and coffee...",
    "gender": "female",
    "hobbies": ["Hiking", "Coffee", "Traveling"],
    "images": [...],
    "common_hobbies": ["Hiking", "Traveling"],
    "liked": false,
    "matched": false
  }
}
```

---

## 4. SWIPE & MATCH ENDPOINTS

### POST /swipes
Perform a swipe action.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "target_user_id": "uuid-string",
  "action": "like"  // "like", "pass", "super_like"
}
```

**Response (200) - Standard Like:**
```json
{
  "success": true,
  "data": {
    "action": "like",
    "is_match": false
  }
}
```

**Response (200) - Match Created:**
```json
{
  "success": true,
  "data": {
    "action": "like",
    "is_match": true,
    "match": {
      "id": "uuid-string",
      "user_one_id": "uuid-string",
      "user_two_id": "uuid-string",
      "created_at": "2026-03-05T10:00:00Z",
      "conversation": {
        "id": 1,
        "match_id": "uuid-string"
      }
    }
  }
}
```

---

### GET /swipes/history
Get swipe history for the current user.

**Headers:** Bearer Token required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| action | string | all | Filter: like, pass, super_like |
| page | int | 1 | Page number |
| per_page | int | 20 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "swipes": [
      {
        "id": 1,
        "target_user_id": "uuid-string",
        "action": "like",
        "created_at": "2026-03-05T10:00:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "total": 55
  }
}
```

---

## 5. MATCH ENDPOINTS

### GET /matches
Get all matches for current user.

**Headers:** Bearer Token required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | int | 1 | Page number |
| per_page | int | 20 | Items per page |
| type | string | all | Filter: all, new, messages |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "matches": [
      {
        "id": "uuid-string",
        "user": {
          "id": "uuid-string",
          "first_name": "Sarah",
          "age": 28,
          "image": {
            "url": "https://cdn.aura.com/images/1.jpg",
            "is_primary": true
          }
        },
        "last_message": {
          "id": 1,
          "content": "Hey! How are you?",
          "sender_id": "uuid-string",
          "created_at": "2026-03-05T10:00:00Z",
          "is_read": false
        },
        "unmatched": false,
        "created_at": "2026-03-04T15:30:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 3,
    "total": 25
  }
}
```

---

### GET /matches/{id}
Get specific match details.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": "uuid-string",
    "user": {
      "id": "uuid-string",
      "first_name": "Sarah",
      "last_name": "Johnson",
      "age": 28,
      "bio": "Love hiking and coffee...",
      "images": [...],
      "hobbies": ["Hiking", "Coffee", "Traveling"]
    },
    "is_premium_match": false,
    "created_at": "2026-03-04T15:30:00Z"
  }
}
```

---

### DELETE /matches/{id}
Unmatch from a user.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Successfully unmatched"
}
```

---

## 6. CHAT/MESSAGING ENDPOINTS

### GET /conversations/{id}/messages
Get messages in a conversation.

**Headers:** Bearer Token required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | int | 1 | Page number |
| per_page | int | 30 | Items per page |
| before | datetime | null | Get messages before this time |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": 1,
        "sender_id": "uuid-string",
        "receiver_id": "uuid-string",
        "content": "Hey! How are you?",
        "message_type": "text",
        "is_read": true,
        "read_at": "2026-03-05T10:01:00Z",
        "created_at": "2026-03-05T10:00:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 2,
    "total": 50,
    "unread_count": 0
  }
}
```

---

### POST /conversations/{id}/messages
Send a message.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "content": "Hey! Would you like to grab coffee sometime?",
  "message_type": "text"  // "text", "image", "gif", "audio"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "id": 51,
    "sender_id": "uuid-string",
    "receiver_id": "uuid-string",
    "content": "Hey! Would you like to grab coffee sometime?",
    "message_type": "text",
    "created_at": "2026-03-05T10:05:00Z"
  }
}
```

---

### PUT /messages/{id}/read
Mark message as read.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Message marked as read"
}
```

---

### DELETE /messages/{id}
Delete a message.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Message deleted"
}
```

---

## 7. NOTIFICATIONS ENDPOINTS

### GET /notifications
Get user notifications.

**Headers:** Bearer Token required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | int | 1 | Page number |
| per_page | int | 20 | Items per page |
| unread_only | boolean | false | Only unread notifications |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": 1,
        "type": "match",
        "title": "It's a Match!",
        "body": "You and Sarah liked each other!",
        "data": {
          "match_id": "uuid-string"
        },
        "is_read": false,
        "created_at": "2026-03-05T10:00:00Z"
      }
    ],
    "unread_count": 3,
    "current_page": 1,
    "last_page": 1,
    "total": 3
  }
}
```

---

### PUT /notifications/{id}/read
Mark notification as read.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

---

### PUT /notifications/read-all
Mark all notifications as read.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "All notifications marked as read"
}
```

---

## 8. REPORT & SAFETY ENDPOINTS

### POST /reports
Report a user.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "reported_user_id": "uuid-string",
  "reason": "inappropriate_content",
  "description": "Their profile contains offensive content..."
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Report submitted successfully. We'll review it shortly."
}
```

---

### POST /users/{id}/block
Block a user.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "User blocked successfully"
}
```

---

### DELETE /users/{id}/block
Unblock a user.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "User unblocked successfully"
}
```

---

### GET /users/me/blocked
Get blocked users list.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "blocked_users": [
      {
        "id": "uuid-string",
        "first_name": "John",
        "blocked_at": "2026-03-01T10:00:00Z"
      }
    ]
  }
}
```

---

## 9. SUBSCRIPTION ENDPOINTS

### GET /subscriptions/plans
Get available subscription plans.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "plans": [
      {
        "id": "free",
        "name": "Free",
        "price": 0,
        "features": ["Basic swiping", "Limited likes per day"],
        "limits": {
          "daily_likes": 50,
          "super_likes": 1,
          "rewind": false,
          "unlimited_likes": false,
          "see_who_likes_you": false,
          "passport": false
        }
      },
      {
        "id": "gold",
        "name": "Aura Gold",
        "price": 9.99,
        "features": ["Unlimited likes", "5 Super Likes/day", "See who likes you"],
        "limits": {
          "daily_likes": -1,
          "super_likes": 5,
          "rewind": false,
          "unlimited_likes": true,
          "see_who_likes_you": true,
          "passport": false
        }
      },
      {
        "id": "platinum",
        "name": "Aura Platinum",
        "price": 14.99,
        "features": ["All Gold features", "Unlimited Super Likes", "Passport feature", "1 Free Rewind/day"],
        "limits": {
          "daily_likes": -1,
          "super_likes": -1,
          "rewind": true,
          "unlimited_likes": true,
          "see_who_likes_you": true,
          "passport": true
        }
      }
    ]
  }
}
```

---

### POST /subscriptions
Create/update subscription.

**Headers:** Bearer Token required

**Request Body:**
```json
{
  "plan_id": "gold",
  "payment_method_id": "pm_card_visa_xxxx"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Subscription activated",
  "data": {
    "subscription_id": "uuid-string",
    "plan": "gold",
    "expires_at": "2026-04-05T10:00:00Z",
    "auto_renew": true
  }
}
```

---

### DELETE /subscriptions
Cancel subscription.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "message": "Subscription cancelled. You can use premium features until expiry."
}
```

---

### GET /subscriptions/me
Get current subscription status.

**Headers:** Bearer Token required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "plan": "gold",
    "is_active": true,
    "started_at": "2026-03-05T10:00:00Z",
    "expires_at": "2026-04-05T10:00:00Z",
    "auto_renew": true,
    "features": {
      "daily_likes": -1,
      "super_likes": 5,
      "unlimited_likes": true,
      "see_who_likes_you": true,
      "passport": false,
      "rewind": false
    }
  }
}
```

---

## 10. ADMIN ENDPOINTS

### GET /admin/dashboard
Get admin dashboard statistics.

**Headers:** Bearer Token (Admin role required)

**Response (200):**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total_users": 50000,
      "active_users": 25000,
      "new_users_today": 150,
      "total_matches": 100000,
      "matches_today": 500,
      "total_messages": 1000000,
      "messages_today": 25000,
      "premium_users": 5000,
      "revenue_monthly": 50000
    },
    "charts": {
      "users_per_day": [...],
      "matches_per_day": [...],
      "revenue_per_day": [...]
    }
  }
}
```

---

### GET /admin/users
Get all users (paginated).

**Headers:** Bearer Token (Admin/Moderator role required)

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | int | 1 | Page number |
| per_page | int | 20 | Items per page |
| search | string | null | Search by name/email/phone |
| is_verified | boolean | null | Filter verified users |
| is_premium | boolean | null | Filter premium users |
| is_active | boolean | null | Filter active users |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": "uuid-string",
        "email": "user@example.com",
        "phone": "+1234567890",
        "is_verified": true,
        "is_premium": true,
        "is_active": true,
        "created_at": "2026-01-15T10:00:00Z",
        "profile": {
          "first_name": "John",
          "age": 30,
          "gender": "male"
        }
      }
    ],
    "current_page": 1,
    "last_page": 100,
    "total": 2000
  }
}
```

---

### PUT /admin/users/{id}/verify
Verify a user profile.

**Headers:** Bearer Token (Admin role required)

**Response (200):**
```json
{
  "success": true,
  "message": "User verified successfully"
}
```

---

### PUT /admin/users/{id}/block
Block a user account.

**Headers:** Bearer Token (Admin role required)

**Request Body:**
```json
{
  "reason": "Violation of terms of service",
  "permanent": true
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "User blocked successfully"
}
```

---

### GET /admin/reports
Get all reports.

**Headers:** Bearer Token (Admin/Moderator role required)

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| page | int | 1 | Page number |
| per_page | int | 20 | Items per page |
| status | string | pending | Filter: pending, reviewed, action_taken, dismissed |

**Response (200):**
```json
{
  "success": true,
  "data": {
    "reports": [
      {
        "id": 1,
        "reporter": {
          "id": "uuid-string",
          "first_name": "John"
        },
        "reported_user": {
          "id": "uuid-string",
          "first_name": "Jane"
        },
        "reason": "inappropriate_content",
        "description": "Profile contains inappropriate images",
        "status": "pending",
        "created_at": "2026-03-05T10:00:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "total": 100
  }
}
```

---

### PUT /admin/reports/{id}/review
Review and action a report.

**Headers:** Bearer Token (Admin role required)

**Request Body:**
```json
{
  "status": "action_taken",
  "action": "blocked_user",
  "admin_notes": "User blocked for violating community guidelines"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Report reviewed successfully"
}
```

---

## 11. WEBSOCKET EVENTS

### Connection
```
WebSocket URL: wss://api.aura-dating.com/ws
Headers: Authorization: Bearer {access_token}
```

### Events

#### Incoming Events:

**new_message**
```json
{
  "type": "new_message",
  "data": {
    "id": 51,
    "conversation_id": 1,
    "sender_id": "uuid-string",
    "content": "Hey!",
    "message_type": "text",
    "created_at": "2026-03-05T10:05:00Z"
  }
}
```

**new_match**
```json
{
  "type": "new_match",
  "data": {
    "match_id": "uuid-string",
    "user": {
      "id": "uuid-string",
      "first_name": "Sarah",
      "image": {...}
    }
  }
}
```

**message_read**
```json
{
  "type": "message_read",
  "data": {
    "message_id": 50,
    "read_at": "2026-03-05T10:06:00Z"
  }
}
```

**typing_indicator**
```json
{
  "type": "typing_indicator",
  "data": {
    "conversation_id": 1,
    "user_id": "uuid-string",
    "is_typing": true
  }
}
```

#### Outgoing Events:

**send_message**
```json
{
  "type": "send_message",
  "data": {
    "conversation_id": 1,
    "content": "Hello!",
    "message_type": "text"
  }
}
```

**typing_start**
```json
{
  "type": "typing_start",
  "data": {
    "conversation_id": 1
  }
}
```

**typing_stop**
```json
{
  "type": "typing_stop",
  "data": {
    "conversation_id": 1
  }
}
```

---

## ERROR RESPONSE FORMAT

All error responses follow this format:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."]
    }
  }
}
```

### Common Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| UNAUTHORIZED | 401 | Invalid or expired token |
| FORBIDDEN | 403 | Access denied |
| NOT_FOUND | 404 | Resource not found |
| VALIDATION_ERROR | 422 | Request validation failed |
| SERVER_ERROR | 500 | Internal server error |
| RATE_LIMIT_EXCEEDED | 429 | Too many requests |
| USER_BLOCKED | 403 | Account has been blocked |
| USER_NOT_VERIFIED | 403 | Email/Phone not verified |
| MATCH_NOT_FOUND | 404 | Match does not exist |
| CANNOT_MESSAGE | 403 | Cannot message this user |

---

## RATE LIMITS

| Endpoint | Limit |
|----------|-------|
| /auth/login | 5 requests/minute |
| /auth/otp/send | 3 requests/minute |
| /discovery/nearby | 60 requests/minute |
| /swipes | 100 requests/minute |
| /messages | 120 requests/minute |

---

## API VERSIONING

The API uses URL versioning. Current version is `v1`. 

All breaking changes will be introduced in new versions (v2, v3, etc.) while maintaining backward compatibility for existing versions for at least 12 months.

---

*Documentation Version: 1.0*
*Last Updated: 2026-03-05*
