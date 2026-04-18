# Aura Dating Application - Architectural Documentation

## Overview

Aura is a modern dating application built with Flutter for the mobile client and PHP/Laravel for the backend. The application follows clean architecture principles with separation of concerns, SOLID principles, and DRY code practices.

---

## 1. System Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        MOBILE APP (Flutter)                      │
├─────────────────────────────────────────────────────────────────┤
│  Presentation Layer (UI/Widgets)                                │
│  ├── Screens (Home, Discovery, Chat, Profile)                  │
│  ├── Widgets (SwipeCard, MatchCelebration)                     │
│  └── Components (Buttons, Inputs, Cards)                        │
├─────────────────────────────────────────────────────────────────┤
│  Business Logic Layer (State Management)                        │
│  ├── Provider/Riverpod (State Management)                       │
│  ├── Models (User, Match, Message)                              │
│  └── Services (API Service, Location Service)                   │
├─────────────────────────────────────────────────────────────────┤
│  Data Layer                                                      │
│  ├── Repositories (User, Match, Message)                        │
│  ├── API Client (Dio/Http)                                     │
│  └── Local Storage (SharedPreferences)                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     BACKEND (PHP/Laravel)                        │
├─────────────────────────────────────────────────────────────────┤
│  API Layer                                                       │
│  ├── REST Controllers (Auth, User, Match, Chat)                │
│  ├── Middleware (Auth, RateLimit)                              │
│  └── API Resources/Transformers                                │
├─────────────────────────────────────────────────────────────────┤
│  Service Layer                                                   │
│  ├── MatchService (Swipe & Match Logic)                        │
│  ├── NotificationService (Push/Email)                          │
│  ├── SubscriptionService (Stripe Integration)                 │
│  └── LocationService (Geo-distance calculations)              │
├─────────────────────────────────────────────────────────────────┤
│  Data Layer                                                      │
│  ├── Eloquent Models                                            │
│  ├── Repositories                                              │
│  └── Database (MySQL)                                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      EXTERNAL SERVICES                           │
├─────────────────────────────────────────────────────────────────┤
│  • Firebase (Push Notifications, Analytics)                     │
│  • Stripe (Payment Processing)                                  │
│  • AWS S3/CDN (Image Storage)                                   │
│  • Pusher/WebSocket (Real-time Messaging)                      │
│  • Twilio (SMS OTP)                                            │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Technology Stack

### Mobile Application (Flutter)

| Component | Technology | Version |
|-----------|------------|---------|
| Framework | Flutter | 3.x |
| Language | Dart | 3.x |
| State Management | Riverpod | 2.x |
| HTTP Client | Dio | 5.x |
| Local Storage | SharedPreferences/Hive | Latest |
| Real-time | Socket.io / Firebase | Latest |
| Image Handling | cached_network_image | Latest |
| Location | geolocator | Latest |
| Maps | google_maps_flutter | Latest |

### Backend (PHP/Laravel)

| Component | Technology | Version |
|-----------|------------|---------|
| Framework | Laravel | 10.x |
| PHP | PHP | 8.2+ |
| Database | MySQL | 8.0 |
| Cache | Redis | 7.x |
| Queue | Laravel Queue | Built-in |
| Authentication | Laravel Sanctum | Latest |
| Real-time | Pusher / Laravel Reverb | Latest |
| Image Storage | Flysystem (AWS S3) | Latest |
| Payments | Stripe SDK | Latest |

---

## 3. Database Schema Design

### Entity Relationship Diagram

```
┌──────────────┐       ┌─────────────────┐       ┌──────────────┐
│    users     │       │   user_profiles │       │ profile_images│
├──────────────┤       ├─────────────────┤       ├──────────────┤
│ id (PK)      │───┐   │ id (PK)         │   ┌───│ id (PK)      │
│ uuid         │   │   │ user_id (FK)    │───┘   │ user_id (FK) │
│ email        │   └───│ first_name      │       │ image_url    │
│ phone        │       │ last_name       │       │ is_primary   │
│ password     │       │ date_of_birth   │       └──────────────┘
│ is_premium   │       │ gender          │
│ role         │       │ bio             │
└──────────────┘       │ latitude        │       ┌──────────────┐
                       │ longitude       │       │    hobbies    │
       │               └─────────────────┘       ├──────────────┤
       │                     │                    │ id (PK)      │
       ▼                     ▼                    │ name         │
┌──────────────┐       ┌─────────────────┐      └──────────────┘
│ user_swipes  │       │    matches       │             │
├──────────────┤       ├─────────────────┤      ┌──────────────┐
│ id (PK)      │       │ id (PK)         │      │ user_hobbies │
│ swiper_id(FK)│───────│ uuid            │      ├──────────────┤
│ swiped_id(FK)│   ┌───│ user_one_id(FK) │      │ id (PK)      │
│ action       │   │   │ user_two_id(FK) │      │ user_id(FK)  │
│ created_at   │   │   │ is_active       │      │ hobby_id(FK) │
└──────────────┘   │   └─────────────────┘      └──────────────┘
                   │         │
                   │         ▼
                   │  ┌─────────────────┐    ┌──────────────┐
                   │  │  conversations   │    │   messages   │
                   │  ├─────────────────┤    ├──────────────┤
                   └──│ id (PK)         │    │ id (PK)      │
                      │ match_id (FK)    │────│ conv_id(FK)  │
                      │ last_message_at  │    │ sender_id(FK)│
                      └─────────────────┘    │ receiver_id  │
                                            │ content      │
                                            │ is_read      │
                                            └──────────────┘
```

### Key Design Decisions

1. **UUID as Public Identifiers**: All entities use UUID for external API references while maintaining auto-increment IDs internally for performance.

2. **Soft Deletes**: Implemented on sensitive tables (`users`, `matches`) to maintain data integrity and allow recovery.

3. **Spatial Indexing**: Location fields (`latitude`, `longitude`) are indexed for efficient geo-queries.

4. **Polymorphic Notifications**: JSON data field allows flexible notification types without schema changes.

---

## 4. API Design Principles

### RESTful Conventions

- **Resource Naming**: Use plural nouns (`/users`, `/matches`, `/messages`)
- **HTTP Methods**:
  - `GET` - Retrieve resources
  - `POST` - Create resources
  - `PUT/PATCH` - Update resources
  - `DELETE` - Remove resources
- **Status Codes**:
  - `200` - Success
  - `201` - Created
  - `400` - Bad Request
  - `401` - Unauthorized
  - `403` - Forbidden
  - `404` - Not Found
  - `422` - Validation Error
  - `500` - Server Error

### Authentication Flow

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Client    │────▶│   Login    │────▶│   Token    │
│             │     │  (Email/   │     │  (JWT or   │
│             │     │   Phone)   │     │  Sanctum)   │
└─────────────┘     └─────────────┘     └─────────────┘
                                                │
                                                ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Authenticated │◀──│  All API   │◀───│  Bearer    │
│  Requests    │    │  Endpoints │    │  Token     │
└─────────────┘     └─────────────┘     └─────────────┘
```

### Rate Limiting Strategy

| Endpoint Category | Limit | Window |
|-------------------|-------|--------|
| Authentication | 5 | per minute |
| Discovery | 60 | per minute |
| Swipes | 100 | per minute |
| Messages | 120 | per minute |
| General | 60 | per minute |

---

## 5. Flutter Architecture

### Folder Structure

```
lib/
├── main.dart
├── app.dart
├── core/
│   ├── config/
│   │   ├── app_config.dart
│   │   └── api_config.dart
│   ├── constants/
│   │   ├── app_constants.dart
│   │   └── api_constants.dart
│   ├── theme/
│   │   ├── aura_theme.dart
│   │   └── aura_colors.dart
│   ├── utils/
│   │   ├── extensions.dart
│   │   └── helpers.dart
│   └── widgets/
│       ├── loading_widget.dart
│       └── error_widget.dart
├── data/
│   ├── models/
│   │   ├── user_model.dart
│   │   ├── match_model.dart
│   │   └── message_model.dart
│   ├── repositories/
│   │   ├── auth_repository.dart
│   │   ├── user_repository.dart
│   │   └── match_repository.dart
│   └── services/
│       ├── api_service.dart
│       ├── auth_service.dart
│       ├── location_service.dart
│       └── notification_service.dart
├── features/
│   ├── auth/
│   │   ├── screens/
│   │   ├── widgets/
│   │   └── providers/
│   ├── discovery/
│   │   ├── screens/
│   │   ├── widgets/
│   │   │   └── swipe_card.dart
│   │   └── providers/
│   ├── matches/
│   │   ├── screens/
│   │   ├── widgets/
│   │   └── providers/
│   ├── chat/
│   │   ├── screens/
│   │   ├── widgets/
│   │   └── providers/
│   └── profile/
│       ├── screens/
│       ├── widgets/
│       └── providers/
└── providers/
    ├── auth_provider.dart
    ├── user_provider.dart
    ├── discovery_provider.dart
    └── chat_provider.dart
```

### State Management (Riverpod)

```dart
// Example Provider Structure
final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(ref.read(authRepository));
});

final discoveryProvider = StateNotifierProvider<DiscoveryNotifier, DiscoveryState>((ref) {
  return DiscoveryNotifier(
    ref.read(matchRepository),
    ref.read(locationService),
  );
});

final matchesProvider = StateNotifierProvider<MatchesNotifier, MatchesState>((ref) {
  return MatchesNotifier(ref.read(matchRepository));
});
```

---

## 6. Laravel Backend Architecture

### Folder Structure

```
app/
├── Console/
│   └── Commands/
├── Events/
│   ├── NewMatch.php
│   ├── NewMessage.php
│   └── UserVerified.php
├── Exceptions/
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── MatchController.php      ← Core Match Logic
│   │   │   ├── DiscoveryController.php
│   │   │   ├── ChatController.php
│   │   │   └── AdminController.php
│   │   └── Controller.php
│   ├── Middleware/
│   │   ├── Authenticate.php
│   │   ├── CheckPremium.php
│   │   └── RateLimitRequests.php
│   └── Requests/
├── Jobs/
│   ├── SendPushNotification.php
│   └── ProcessImageUpload.php
├── Models/
│   ├── User.php
│   ├── UserProfile.php
│   ├── Match.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── UserSwipe.php
│   └── Notification.php
├── Providers/
│   ├── AppServiceProvider.php
│   └── RouteServiceProvider.php
├── Services/
│   ├── MatchService.php
│   ├── NotificationService.php
│   ├── LocationService.php
│   ├── SubscriptionService.php
│   └── ImageService.php
└── Traits/
    ├── HasLocation.php
    └── HasSwipeLogic.php
```

### Service Layer Pattern

```php
// Example: MatchService
class MatchService
{
    public function createMatch(User $userOne, User $userTwo): Match
    {
        return DB::transaction(function () use ($userOne, $userTwo) {
            $match = Match::create([...]);
            Conversation::create(['match_id' => $match->id]);
            return $match;
        });
    }

    public function checkForMatch(User $swiper, User $swiped): ?Match
    {
        $existingLike = UserSwipe::where('swiper_id', $swiped->id)
            ->where('swiped_id', $swiper->id)
            ->whereIn('action', ['like', 'super_like'])
            ->first();

        return $existingLike ? $this->createMatch($swiper, $swiped) : null;
    }
}
```

---

## 7. Security Implementation

### Authentication Security

1. **Password Hashing**: Use `bcrypt` with cost factor 12
2. **Token Management**: Short-lived access tokens (60 min), long-lived refresh tokens (30 days)
3. **OTP Implementation**:
   - 6-digit numeric codes
   - 5-minute expiration
   - Rate limited to 3 requests per minute
   - Maximum 10 attempts per code

### API Security

1. **Input Validation**: All inputs validated using Laravel Form Requests
2. **SQL Injection Prevention**: Eloquent ORM with parameter binding
3. **XSS Prevention**: Content escaping in API responses
4. **CORS Configuration**: Strict origin whitelisting
5. **Rate Limiting**: Per-user, per-endpoint rate limits

### Data Security

1. **Sensitive Data**: Encryption at rest for PII
2. **Image Handling**: Cloud storage with signed URLs
3. **Location Privacy**: Coordinates rounded to reduce precision

---

## 8. Scalability Considerations

### Horizontal Scaling

- **Load Balancer**: Distribute traffic across multiple app instances
- **Stateless Sessions**: Use Redis for session storage
- **Database Read Replicas**: Separate read/write operations
- **CDN**: Static assets served via CDN

### Caching Strategy

| Data Type | Cache Strategy | TTL |
|-----------|----------------|-----|
| User Profiles | Redis | 15 min |
| Discovery Results | In-memory | Request-scoped |
| Hobbies List | Redis | 24 hours |
| Subscription Plans | Redis | 1 hour |

### Queue Processing

- **Image Processing**: Background job for resizing/optimization
- **Push Notifications**: Queued for delivery
- **Analytics**: Batch processed hourly

---

## 9. Real-time Features

### WebSocket Implementation

```
Client ────▶ WebSocket ────▶ Pusher/Reverb
  │          Connection       Server
  │                            │
  │                            ▼
  │                    ┌──────────────┐
  │                    │  Broadcast   │
  │                    │   Events     │
  │                    └──────────────┘
  │                            │
  ◀─────────────────────────────┘
      Real-time Updates
```

### Events Emitted

| Event | Data | Recipients |
|-------|------|------------|
| `new_match` | Match details, user info | Matched user |
| `new_message` | Message content | Recipient |
| `message_read` | Message ID, read timestamp | Sender |
| `typing_start` | Conversation ID | Recipient |
| `user_online` | User ID | All matches |

---

## 10. Premium Features

### Subscription Tiers

| Feature | Free | Gold | Platinum |
|---------|------|------|----------|
| Daily Likes | 50 | Unlimited | Unlimited |
| Super Likes/Day | 1 | 5 | Unlimited |
| Rewind | ❌ | ❌ | ✅ (1/day) |
| See Who Likes You | ❌ | ✅ | ✅ |
| Passport (Location) | ❌ | ❌ | ✅ |
| Unlimited Swipes | ❌ | ✅ | ✅ |
| Hide Age | ❌ | ❌ | ✅ |
| Price | $0 | $9.99/mo | $14.99/mo |

---

## 11. Admin Panel Features

### Dashboard Metrics

- **User Analytics**: Total, active, new users
- **Match Analytics**: Total matches, match rate
- **Revenue Analytics**: MRR, ARR, churn rate
- **Engagement Metrics**: Daily active users, messages sent

### Moderation Tools

- **User Management**: View, verify, block, delete
- **Report Queue**: Review and action user reports
- **Content Moderation**: Image approval queue
- **Analytics Export**: CSV/Excel export capabilities

---

## 12. Deployment Architecture

### Production Environment

```
┌─────────────────────────────────────────────────────────────────┐
│                        LOAD BALANCER                             │
│                      (AWS ALB / Nginx)                           │
└─────────────────────────────────────────────────────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌───────────────┐     ┌───────────────┐     ┌───────────────┐
│  App Server 1 │     │  App Server 2 │     │  App Server 3 │
│   (Laravel)  │     │   (Laravel)  │     │   (Laravel)  │
└───────────────┘     └───────────────┘     └───────────────┘
        │                     │                     │
        └─────────────────────┼─────────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
┌───────────────┐     ┌───────────────┐     ┌───────────────┐
│  Redis Cache │     │    MySQL     │     │     S3       │
│              │     │   Primary    │     │   Storage    │
└───────────────┘     └───────────────┘     └───────────────┘
```

### CI/CD Pipeline

```
Git Push → CI/CD (GitHub Actions)
  ├── Lint & Static Analysis
  ├── Unit Tests
  ├── Integration Tests
  ├── Build Docker Image
  ├── Security Scan
  └── Deploy to Staging/Production
```

---

## 13. Error Handling

### Global Exception Handler

```php
// Laravel Exception Handler
class Handler extends ExceptionHandler
{
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => class_basename($exception),
                    'message' => $exception->getMessage(),
                ],
            ], $this->getStatusCode($exception));
        }
        
        return parent::render($request, $exception);
    }
}
```

### Flutter Error Handling

```dart
// Dio Interceptor for Error Handling
class ErrorInterceptor extends Interceptor {
  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    final error = ApiException.fromDioError(err);
    
    if (error.type == ApiExceptionType.unauthorized) {
      // Handle token refresh or logout
    }
    
    handler.next(err);
  }
}
```

---

## 14. Testing Strategy

### Unit Tests (Backend)

- Model tests
- Service layer tests
- Utility function tests
- API endpoint tests

### Widget Tests (Flutter)

- Individual widget rendering
- User interaction flows
- State management tests

### Integration Tests

- Complete user flows
- API integration
- Database transactions

---

## 15. Monitoring & Analytics

### Application Monitoring

- **Error Tracking**: Sentry integration
- **Performance**: New Relic / Scout
- **Logging**: Centralized logging (ELK Stack)

### Business Analytics

- **User Events**: Firebase Analytics
- **Custom Events**: In-app actions tracking
- **Dashboards**: Custom admin analytics

---

*Document Version: 1.0*
*Last Updated: 2026-03-05*
*Authors: Aura Development Team*
