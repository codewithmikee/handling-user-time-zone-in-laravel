# Laravel API Starter Template

A robust Laravel API starter template with built-in authentication, standardized JSON responses, and extendable controllers using reusable traits. Ideal for quickly bootstrapping secure and maintainable RESTful APIs.

**Author**: Mikiyas Birhanu  
**GitHub**: [@codewithmikee](https://github.com/codewithmikee)  
**Repo**: [github.com/codewithmikee/laravel-backend-starter-template](https://github.com/codewithmikee/laravel-backend-starter-template)

# API Documentation

API documentation and collections (Postman, Swagger/OpenAPI) are stored in the `docs/` folder at the project root.

---

## 📦 API Collections

- **Postman Collection:**
  - File: `docs/postman_collection.json`
  - Import this file into Postman to test all API endpoints quickly.
  - Includes example requests for registration, login, and profile fetch.

- **Swagger/OpenAPI Spec:**
  - File: `docs/swagger.yaml`
  - Use with Swagger UI, Redoc, or compatible tools for interactive API docs and code generation.
  - Describes all endpoints, request/response formats, and authentication requirements.

---

## ✨ Features
- **Sanctum Authentication**: Ready-to-use JWT-like token-based auth.
- **Standardized Responses**: Consistent JSON success/error formats via traits.
- **Pre-configured Error Handling**: Automatic exceptions for:
  - Validation (422)
  - Authorization (403)
  - Rate Limiting (429)
  - Model/Route Not Found (404)
- **Extendable Base Controllers**: Simplify CRUD operations with:
  - `BaseApiController` (General APIs)
  - `ProtectedApiController` (Auth-required endpoints)
- **Reusable Controller Traits**: 
  - `HandlesApiResponse`: Standardizes API responses
  - `HandlesValidation`: Centralizes validation logic
  - `HandlesAuth`: Authenticated user and authorization helpers
- **Middleware**: Ensures all responses are JSON-formatted.

---

## 🚀 Quick Start

### 1. Clone & Setup
```bash
git clone https://github.com/codewithmikee/laravel-backend-starter-template.git
cd laravel-backend-starter-template
cp .env.example .env
composer install
php artisan key:generate
```

### 2. Configure Database
Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Sanctum Setup
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

---

## 🔧 Usage

### Authentication Endpoints
**Register**  
`POST /api/auth/register`
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

**Login**  
`POST /api/auth/login`
```json
{
  "email": "john@example.com",
  "password": "secret123",
  "device_name": "iPhone"
}
```

**Profile (Protected)**  
`GET /api/profile`  
*Header:* `Authorization: Bearer <token>`

---

## 🛠 Extending Controllers & Traits
### 1. Create a Protected Controller
```php
use App\Http\Controllers\Api\ProtectedApiController;

class UserController extends ProtectedApiController
{
    public function index()
    {
        return $this->handleRequest(
            fn() => User::all(),
            $this->request,
            'Users fetched successfully'
        );
    }
}
```

### 2. Use Traits for Custom Logic
```php
use App\Http\Controllers\Concerns\HandlesApiResponse;

class CustomController extends Controller
{
    use HandlesApiResponse;
    // ...
}
```

### 3. Custom Error Responses
Throw errors directly in controllers:
```php
$this->respondError('Resource not found', 404);
```

---

## 📜 Response Format
**Success**  
```json
{
  "success": true,
  "message": "Profile fetched successfully",
  "data": { "name": "John", "email": "john@example.com" },
  "errors": null
}
```

**Error**  
```json
{
  "success": false,
  "message": "Unauthorized",
  "data": null,
  "errors": {"authorization": "Unauthenticated"}
}
```

---

## 📌 Best Practices
- Use `BaseApiController` for general endpoints.
- Extend `ProtectedApiController` for auth-required routes.
- Utilize `validateRequest()` in controllers for validation.
- Use controller traits for reusable logic.
- Environment-specific errors: Full details in `local/staging`, generic in `production`.

---

**Happy Coding!** 🚀  
*Maintained by [Mikiyas Birhanu](https://github.com/codewithmikee)*
```
