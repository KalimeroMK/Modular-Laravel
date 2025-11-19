# Sanctum SPA Authentication Guide

This guide explains how to configure and use Laravel Sanctum for Single Page Application (SPA) authentication in this modular Laravel application.

## Overview

Laravel Sanctum provides a lightweight authentication system for SPAs and mobile applications. For SPAs, Sanctum uses Laravel's built-in cookie-based session authentication services, which provides the benefits of CSRF protection, session authentication, and protection against XSS attacks.

## Configuration

### 1. Environment Variables

Add the following to your `.env` file:

```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1,yourdomain.com
SESSION_DRIVER=cookie
SESSION_DOMAIN=localhost
```

**Important:** The `SANCTUM_STATEFUL_DOMAINS` should include all domains that will make requests to your API. This includes:
- Your frontend SPA domain (e.g., `localhost:3000` for development)
- Your API domain (e.g., `localhost:8000` for development)
- Your production domains

### 2. CORS Configuration

Update `config/cors.php` to allow credentials:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],

'supports_credentials' => true,
```

### 3. Sanctum Configuration

The Sanctum configuration is already set up in `config/sanctum.php`. Key settings:

- **Stateful Domains**: Domains that receive stateful authentication cookies
- **Guard**: Authentication guard to use (default: `web`)
- **Middleware**: CSRF and cookie encryption middleware

## Frontend Setup

### 1. Install Axios (or your preferred HTTP client)

```bash
npm install axios
```

### 2. Configure Axios

Create `resources/js/axios.js`:

```javascript
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

// Request interceptor
axios.interceptors.request.use(
    (config) => {
        // Add CSRF token if available
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Response interceptor
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Handle unauthorized - redirect to login
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default axios;
```

### 3. CSRF Cookie Endpoint

Before making authenticated requests, fetch the CSRF cookie:

```javascript
import axios from './axios';

// Fetch CSRF cookie
await axios.get('/sanctum/csrf-cookie');

// Now you can make authenticated requests
const response = await axios.post('/api/v1/auth/login', {
    email: 'user@example.com',
    password: 'password'
});
```

### 4. Authentication Flow

```javascript
// Login
async function login(email, password) {
    // 1. Get CSRF cookie
    await axios.get('/sanctum/csrf-cookie');
    
    // 2. Login
    const response = await axios.post('/api/v1/auth/login', {
        email,
        password
    });
    
    return response.data;
}

// Make authenticated request
async function getUsers() {
    const response = await axios.get('/api/v1/users');
    return response.data;
}

// Logout
async function logout() {
    await axios.post('/api/v1/auth/logout');
}
```

## Backend Routes

### 1. CSRF Cookie Route

Sanctum automatically provides the `/sanctum/csrf-cookie` endpoint. No additional setup needed.

### 2. Authentication Routes

The authentication routes are already set up in `app/Modules/Auth/Infrastructure/Routes/auth.php`:

```php
Route::prefix('api/v1/auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});
```

### 3. Protected Routes

All module routes are protected with `auth:sanctum` middleware:

```php
Route::prefix('api/v1/users')->middleware(['auth:sanctum'])->group(function (): void {
    // Protected routes
});
```

## Testing SPA Authentication

### 1. Start Laravel Server

```bash
php artisan serve
```

### 2. Start Frontend Dev Server

```bash
npm run dev
# or
npm run build
```

### 3. Test Authentication Flow

1. Visit your SPA (e.g., `http://localhost:3000`)
2. Call `/sanctum/csrf-cookie` endpoint
3. Login with credentials
4. Make authenticated API requests

## Troubleshooting

### Issue: CORS Errors

**Solution:** Ensure `supports_credentials` is `true` in `config/cors.php` and your frontend sends credentials.

### Issue: 419 CSRF Token Mismatch

**Solution:** 
1. Ensure you're calling `/sanctum/csrf-cookie` before authenticated requests
2. Check that `SANCTUM_STATEFUL_DOMAINS` includes your frontend domain
3. Verify `SESSION_DOMAIN` is set correctly

### Issue: Session Not Persisting

**Solution:**
1. Check that cookies are being set in browser DevTools
2. Verify `SESSION_DRIVER` is set to `cookie`
3. Ensure `withCredentials: true` is set in your HTTP client

### Issue: 401 Unauthorized

**Solution:**
1. Verify the user is logged in (check session)
2. Ensure `auth:sanctum` middleware is applied
3. Check that cookies are being sent with requests

## Security Considerations

1. **CSRF Protection**: Sanctum automatically handles CSRF protection for stateful requests
2. **Same-Site Cookies**: Configure `config/session.php` to set appropriate `same_site` cookie attribute
3. **Domain Restrictions**: Only include trusted domains in `SANCTUM_STATEFUL_DOMAINS`
4. **HTTPS in Production**: Always use HTTPS in production for secure cookie transmission

## Additional Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Laravel Session Configuration](https://laravel.com/docs/session)
- [CORS Configuration](https://laravel.com/docs/cors)

