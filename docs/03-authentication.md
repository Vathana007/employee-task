01 - What is Laravel Sanctum?
Answer: Laravel Sanctum is Laravel's lightweight authentication package.
        It provides:
        - API Tokens
        - SPA Authentication
        - Token abilities
        - Token revocation
---
Step 1 — Install Sanctum
- composer require laravel/sanctum
<!-- Publish Sanctum Configuration -->
- php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

Step 2 — Run Migration
- php artisan migrate

Step 3 — Update User Model
<!-- Add to user model -->
use Laravel\Sanctum\HasApiTokens;
class {
    use HasApiTokens
}

Step 4 — Configure API Authentication
<!-- Open bootstrap/app -->
Make sure API middleware is enabled.

Step 5 — Authentication Routes

Step 6 — Create Controller
- php artisan make:controller AuthController

Step 7 — Register API
- Validation
- Create user
- Hash password
- Return token

Step 8 — Login
How Auth::attempt() Works?
- Laravel automatically:
1. Finds the user by email.
2. Reads the hashed password from the database.
3. Compares it with the provided password.
4. Returns true if they match.

Step 9 — Profile    