# Modular Laravel Starter Kit

**Modular Laravel Starter Kit** is an advanced, API-first starter package for Laravel. It focuses on modularity and clean architecture, aiming to make API development fast, sustainable, and scalable with minimal manual intervention.

## 🚀 Introduction

This kit is ideal for teams and developers who want a clean, well-structured codebase with clearly separated logic, using modern patterns such as repositories, DTOs (for input), Resources (for output), actions, and automatic relationship mapping.

## 🐳 Docker Setup (Recommended)

For the best development experience, we recommend using Docker:

### Quick Docker Setup

```bash
# Clone the repository
git clone <repository-url>
cd modular-laravel

# Run Docker setup (installs dependencies, runs migrations, seeders)
./docker-setup.sh

# Access the application
# Web: http://localhost:8080
# API Docs: http://localhost:8080/api/documentation
# Database: localhost:3301 (homestead/secret)
```

### Docker Commands

```bash
# Run tests in Docker
./docker-test.sh

# Stop containers
make docker-stop

# Restart containers
make docker-restart

# View logs
make docker-logs
```

### Manual Docker Commands

```bash
# Start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate:fresh --seed

# Run tests
docker-compose exec app php artisan test

# Access container
docker-compose exec app bash
```

## 💻 Local Development (Alternative)

If you prefer local development without Docker:

```bash
# Clone the repository
git clone <repository-url>
cd modular-laravel

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup (requires MySQL/PostgreSQL)
php artisan migrate:fresh --seed

# Start development server
php artisan serve
```

## 🛠️ Available Commands

This project includes a Makefile for common tasks:

```bash
# Show all available commands
make help

# Docker commands
make docker-setup    # Setup Docker environment
make docker-test     # Run tests in Docker
make docker-stop     # Stop containers
make docker-restart  # Restart containers
make docker-logs     # View logs

# Development commands
make test            # Run PHPUnit tests
make phpstan         # Run PHPStan static analysis
make pint            # Run Laravel Pint formatting
make migrate         # Run migrations
make seed            # Run seeders
make setup           # Quick local setup
make clean           # Clean cache

# Database optimization
php artisan db:optimize                    # Analyze database performance
php artisan db:optimize --monitor          # Monitor queries in real-time
php artisan db:optimize --connection-info  # Get connection information
```

## 🎯 Goals

-   ✅ Automatic generation of complete API modules
-   ✅ Minimal manual configuration
-   ✅ Scalable and maintainable code
-   ✅ Clear separation of concerns through modules
-   ✅ No web UI or Blade support – API only
-   ✅ Production-ready security with 2FA
-   ✅ Optimized database performance
-   ✅ Clean Architecture implementation
-   ✅ Comprehensive test coverage

## 🔧 Features

-   **Modular structure**: Each module is self-contained under `app/Modules`
-   **Powerful CLI Generator**: Create complete modules via `php artisan make:module`
-   **Dynamic field handling**: Fillables, casts, and relationships auto-handled
-   **Built-in Rate Limiting**: Auto-generated routes include Laravel throttle middleware
-   **Flexible flags**:
    -   `--exceptions`: Generate exception classes
    -   `--observers`: Generate observer stubs
    -   `--policies`: Generate policy stubs
-   **Auto-discovery**: Routes, migrations, factories, observers, and policies
-   **Repository pattern**: Interface-to-implementation binding out-of-the-box
-   **Fully configurable**: `config/modules.php` for structure and behaviors
-   **Two-Factor Authentication**: Complete 2FA support with Google Authenticator
-   **Database Optimization**: Performance indexes, query caching, and monitoring
-   **Clean Architecture**: Application/Infrastructure layer separation
-   **Comprehensive Testing**: Unit, Feature, Integration, and Performance tests

## ✅ Supported Field Types

| Laravel Type         | SQL Equivalent  | Description                |
| -------------------- | --------------- | -------------------------- |
| `string`             | VARCHAR         | Short text string          |
| `char`               | CHAR            | Fixed-length string        |
| `text`               | TEXT            | Long text                  |
| `mediumText`         | MEDIUMTEXT      | Medium-length text         |
| `longText`           | LONGTEXT        | Very long text             |
| `integer`            | INT             | Standard integer           |
| `tinyInteger`        | TINYINT         | Very small integer         |
| `smallInteger`       | SMALLINT        | Small integer              |
| `mediumInteger`      | MEDIUMINT       | Medium-sized integer       |
| `bigInteger`         | BIGINT          | Large integer              |
| `unsignedBigInteger` | BIGINT UNSIGNED | Large unsigned integer     |
| `foreign`            | INT (FK)        | Foreign key (auto handled) |
| `float`              | FLOAT           | Floating point number      |
| `double`             | DOUBLE          | Double-precision number    |
| `decimal`            | DECIMAL(8,2)    | Fixed precision decimal    |
| `boolean`            | TINYINT(1)      | Boolean (true/false)       |
| `enum`               | ENUM(...)       | Fixed set of values        |
| `date`               | DATE            | Date only                  |
| `datetime`           | DATETIME        | Date and time              |
| `timestamp`          | TIMESTAMP       | Timestamp                  |
| `time`               | TIME            | Time only                  |
| `year`               | YEAR            | Year only                  |
| `json`               | JSON            | Structured JSON data       |
| `array`              | JSON (casted)   | PHP array via JSON cast    |
| `uuid`               | CHAR(36)        | UUID                       |
| `ipAddress`          | VARCHAR(45)     | IPv4/IPv6                  |
| `macAddress`         | VARCHAR(17)     | MAC address                |
| `binary`             | BLOB            | Binary large object        |

## 🏗️ Clean Architecture Structure

This starter kit follows **Clean Architecture** principles with clear separation between Application and Infrastructure layers:

### 📁 Module Structure

Each module is organized into two main layers:

#### 🎯 Application Layer (`Application/`)

Contains business logic and use cases:

-   **Actions/** - Business use cases and operations (return Eloquent models)
-   **DTOs/** - Data Transfer Objects for data manipulation (input from requests)
-   **Services/** - Business services and interfaces
-   **Interfaces/** - Contracts for external dependencies

#### 🔧 Infrastructure Layer (`Infrastructure/`)

Contains external concerns and implementations:

-   **Models/** - Eloquent models and database entities
-   **Repositories/** - Data access implementations
-   **Http/** - Web layer (Controllers, Requests, Resources)
    -   **Resources/** - API response transformation (output to clients)
-   **Providers/** - Service providers for dependency injection
-   **Routes/** - API route definitions

### 🔄 Dependency Flow

```
Controllers → Actions → Services → Repositories → Models
     ↓           ↓         ↓           ↓
  Resources  Business   Business    Database
  (Output)   Logic      Services    Access
     ↑
   DTOs (Input)
```

-   **Controllers** handle HTTP requests, use DTOs for input, and return Resources for output
-   **Actions** contain business logic, accept DTOs, and return Eloquent models
-   **Services** implement business rules and use Repositories
-   **Repositories** abstract data access and work with Models
-   **Models** represent database entities and relationships
-   **DTOs** are used only for data manipulation (input from requests)
-   **Resources** are used only for API responses (output to clients)

### 📦 Data Flow Architecture

The application follows a clear separation between input and output:

**Input Flow (Request → Action):**
```
HTTP Request → FormRequest → DTO → Action → Model
```

**Output Flow (Action → Response):**
```
Action → Model → Resource → JSON Response
```

**Key Principles:**
-   **DTOs** are used **only** for data manipulation (validated input from requests)
-   **Resources** are used **only** for API responses (formatted output to clients)
-   **Actions** return **Eloquent models**, not DTOs or Resources
-   **Controllers** transform models to Resources for API responses

## 🔄 Automatic Relationship Sync

You can use the `SyncRelations::execute()` helper to automatically sync both `belongsToMany` and `belongsTo` relationships using your DTO:

```php
SyncRelations::execute($model, [
    'tags' => $dto->tags,         // BelongsToMany
    'brand' => $dto->brand_id,    // BelongsTo
]);
```

-   For `BelongsToMany`, it performs `$relation->sync(array)`
-   For `BelongsTo`, it sets the foreign key and saves the model if changed.

## ⚙️ Usage

### 1. Generate a New Module

#### Interactive Mode (Wizard)

Run the command without arguments to start an interactive wizard:

```bash
php artisan make:module
```

The wizard will guide you through:

-   Module name
-   Model fields
-   Relationships
-   Additional features (exceptions, observers, policies, events, enums, notifications)

#### Non-Interactive Mode (Flags)

```bash
php artisan make:module Product \
  --model="name:string,price:float,stock:int,is_active:bool,category_id:int" \
  --relations="category:belongsTo:Category,reviews:hasMany:Review" \
  --exceptions \
  --observers \
  --policies \
  --events \
  --enum \
  --notifications
```

### 2. Flags

| Flag              | Description                           |
| ----------------- | ------------------------------------- |
| `--model`         | Define fields and types for the model |
| `--relations`     | Add Eloquent relationships            |
| `--exceptions`    | Generate Exceptions                   |
| `--observers`     | Generate Observers and auto-register  |
| `--policies`      | Generate Policies and auto-register   |
| `--events`        | Generate Events and Listeners         |
| `--enum`          | Generate Enum class                   |
| `--notifications` | Generate Notification classes         |

### 3. Structure

```
app/Modules/Example/
├── Application/
│   ├── Actions/
│   ├── DTOs/
│   ├── Services/
│   └── Interfaces/
├── Infrastructure/
│   ├── Models/
│   ├── Repositories/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Providers/
│   └── Routes/
│       └── api.php
├── Exceptions/
├── Observers/
├── Policies/
└── database/
    ├── migrations/
    └── factories/
```

### 4. Auto-Registration

Observers and Policies are auto-registered if files exist.

### 5. Validation for Foreign Keys

If a field ends in `_id`, the generated FormRequest will contain:

```php
'user_id' => ['required', 'integer', 'exists:users,id'],
```

## 🔗 Polymorphic Relationships

The module generator now supports **polymorphic relationships** for flexible data modeling.

### 📋 Polymorphic Relationship Types

| Type          | Description                           | Usage                                    |
| ------------- | ------------------------------------- | ---------------------------------------- |
| `morphTo`     | Polymorphic belongs-to relationship   | `owner:morphTo`                          |
| `morphOne`    | Polymorphic one-to-one relationship   | `profile:morphOne:Profile:ownable`       |
| `morphMany`   | Polymorphic one-to-many relationship  | `comments:morphMany:Comment:commentable` |
| `morphToMany` | Polymorphic many-to-many relationship | `tags:morphToMany:Tag:taggable`          |

### 💡 Module Generation Examples

#### 1. Comments that can belong to different models

```bash
# Generate a Comment model that can be attached to any model
php artisan make:module Comment \
  --model="content:text,author_name:string" \
  --relations="commentable:morphTo,user:belongsTo:User"
```

This will generate a model with `commentable_type` and `commentable_id` fields for the polymorphic relationship.

#### 2. Product with polymorphic relationships

```bash
# Generate Product model with comments and tags
php artisan make:module Product \
  --model="name:string,price:float,description:text" \
  --relations="comments:morphMany:Comment:commentable,tags:morphToMany:Tag:taggable"
```

#### 3. Tags that can be applied to different models

```bash
# Generate Tag model for polymorphic many-to-many relationship
php artisan make:module Tag \
  --model="name:string,slug:string,color:string" \
  --relations="products:morphedByMany:Product:taggable,posts:morphedByMany:Post:taggable"
```

#### 4. Images/attachments that can belong to different entities

```bash
# Generate Attachment model
php artisan make:module Attachment \
  --model="filename:string,path:string,size:integer,mime_type:string" \
  --relations="attachable:morphTo,user:belongsTo:User"
```

### 🎯 YAML Configuration for Polymorphic Relationships

```yaml
modules:
    # Comment that can be attached to any model
    Comment:
        fields:
            content: text
            author_name: string
            rating: integer
        relations:
            commentable: morphTo
            user: belongsTo:User
        observers: true

    # Product with polymorphic relationships
    Product:
        fields:
            name: string
            price: float
            description: text
            is_active: boolean
        relations:
            # Standard relationships
            category: belongsTo:Category
            # Polymorphic relationships
            comments: morphMany:Comment:commentable
            tags: morphToMany:Tag:taggable
            attachments: morphMany:Attachment:attachable
        policies: true

    # Tags for polymorphic many-to-many
    Tag:
        fields:
            name: string
            slug: string
            color: string
        relations:
            # Can be applied to different models
            products: morphedByMany:Product:taggable
            posts: morphedByMany:Post:taggable

    # Attachments that can belong to different models
    Attachment:
        fields:
            filename: string
            path: string
            size: integer
            mime_type: string
        relations:
            attachable: morphTo
            user: belongsTo:User
```

### 🔧 Automatic Syncing of Polymorphic Relationships

The `SyncRelations` class supports automatic syncing of polymorphic relationships:

```php
use App\Modules\Core\Support\Relations\SyncRelations;

// In your controller or action
SyncRelations::execute($model, [
    'tags' => $dto->tag_ids,           // MorphToMany - sync with IDs
    'commentable' => $product,         // MorphTo - with model instance
    'owner' => [                       // MorphTo - with type and id
        'type' => 'App\\Models\\User',
        'id' => 123
    ],
    'category' => $dto->category_id,   // Standard belongsTo relationship
]);
```

**Supported Relationship Types:**

-   **`MorphToMany`**: Uses `sync()` for polymorphic many-to-many
-   **`MorphTo`**: Automatically sets `type` and `id` fields
    -   Accepts model instances: `$user`
    -   Accepts arrays: `['type' => 'App\\Models\\User', 'id' => 123]`
    -   Accepts `null` to clear the relationship

### 🌟 Benefits of Polymorphic Relationships

1. **Flexibility** - One model can connect to different types
2. **DRY Principle** - Avoid duplicating tables for similar relationships
3. **Scalability** - Easy to add new models without changing existing ones
4. **Elegance** - Cleaner solution for complex relationships

### 📚 Practical Usage Examples

**Comment System:**

```php
// Comment on a product
$comment->commentable()->associate($product);

// Comment on a blog post
$comment->commentable()->associate($blogPost);

// Get comments for a product
$productComments = $product->comments;
```

**Tagging System:**

```php
// Add tags to a product
$product->tags()->attach([1, 2, 3]);

// Get all products with a specific tag
$taggedProducts = $tag->products;
```

## 🚀 Eager Loading & N+1 Prevention

The starter kit **automatically prevents N+1 queries** in development using Laravel's native features:

```php
// Enabled in AppServiceProvider (development only)
Model::preventLazyLoading(!$this->app->isProduction());
```

### How it works:

```php
// ❌ This throws exception in development
$product = Product::find(1);
echo $product->category->name; // LazyLoadingViolationException

// ✅ Correct - eager load relationships
$product = Product::with('category')->find(1);
echo $product->category->name; // Works!
```

### Default Eager Loading in Models:

```php
class Product extends Model
{
    // Always load these relationships
    protected $with = ['category', 'brand'];
}
```

### In Repositories:

```php
// All methods support eager loading
$products = $repository->all(['category', 'images']);
$product = $repository->find($id, ['category', 'reviews']);
$products = $repository->paginate(15, ['category', 'brand']);
```

## 📦 Module Generation via YAML

In addition to the `php artisan make:module` command, you can now generate multiple modules at once using a YAML configuration file.

### 🔧 Usage

1. Create a `modules.yaml` file in the root of your project:

```yaml
# YAML comments are fully supported
modules:
    # Product module with all features
    Product:
        fields:
            name: string
            price: float
            is_active: boolean
            status: enum
            metadata: json
            published_at: datetime
        relations:
            belongsToMany: [Category, Tag]
            user: belongsTo:User
            comments: morphMany:Comment:commentable
        observers: true
        policies: true
        exceptions: true
        events: true
        enum: true
        notifications: false

    # Category module
    Category:
        fields:
            name: string
            slug: string
        relations:
            belongsToMany: [Product]

    # Comment module with polymorphic relations
    Comment:
        fields:
            body: text
            commentable_type: string
            commentable_id: int
        relations:
            commentable: morphTo
            author: belongsTo:User
        policies: true
        exceptions: true
```

2. Run the command:

```bash
php artisan modules:build-from-yaml
```

This will:

> 📌 Note: Pivot migrations are automatically generated **only** when using `modules:build-from-yaml` and when both related modules define a `belongsToMany` relationship to each other.

-   Automatically generate all modules using the same logic as `make:module`
-   Parse `fields`, `relations`, and all available options
-   Fill in `fillable`, `casts`, `migrations`, `factories`, and `resources`
-   Avoids manual repetition by letting you define multiple modules at once
-   **Rollback support**: If a module fails to generate, it will automatically rollback all changes
-   **Statistics**: Display detailed statistics about generated files and modules
-   **Comment support**: YAML comments are fully supported and ignored during parsing

### 📋 Supported YAML Options

| Option          | Description                         | Example                                                                                        |
| --------------- | ----------------------------------- | ---------------------------------------------------------------------------------------------- |
| `fields`        | Model fields with types             | `name: string`, `price: float`, `status: enum`                                                 |
| `relations`     | Eloquent relationships              | `belongsToMany: [Category]`, `user: belongsTo:User`, `comments: morphMany:Comment:commentable` |
| `observers`     | Generate observer classes           | `observers: true`                                                                              |
| `policies`      | Generate policy classes             | `policies: true`                                                                               |
| `exceptions`    | Generate exception classes          | `exceptions: true`                                                                             |
| `events`        | Generate event and listener classes | `events: true`                                                                                 |
| `enum`          | Generate enum class                 | `enum: true`                                                                                   |
| `notifications` | Generate notification classes       | `notifications: true`                                                                          |

### 🛡️ Rollback Mechanism

The YAML module generation includes a comprehensive rollback mechanism:

-   **Automatic Rollback**: If a module fails to generate, all changes are automatically rolled back
-   **File Tracking**: All generated files are tracked for easy rollback
-   **Modified Files**: Modified files (like `RepositoryServiceProvider`) are backed up and restored on failure
-   **Clean State**: Ensures your codebase remains in a clean state even if generation fails

### 📊 Generation Statistics

After generating modules, you'll see detailed statistics:

```
📊 Generation Statistics:
┌─────────────────────┬───────┐
│ Metric              │ Value │
├─────────────────────┼───────┤
│ Modules Generated   │   3   │
│ Total Files         │  45   │
│ Successful          │   3   │
│ Failed              │   0   │
└─────────────────────┴───────┘

📁 Files by Module:
┌──────────┬───────┐
│ Module   │ Files │
├──────────┼───────┤
│ Product  │  15   │
│ Category │  12   │
│ Comment  │  18   │
└──────────┴───────┘
```

### 💬 YAML Comments Support

YAML comments are fully supported and ignored during parsing:

```yaml
# This is a comment
modules:
    # Module comment
    Product:
        fields:
            name: string # Inline comment
            price: float
        # Relations comment
        relations:
            belongsToMany: [Category]
```

### 🔗 Relationship Types

**Standard Relationships:**

-   `belongsTo`: `user: belongsTo:User`
-   `hasMany`: `orders: hasMany:Order`
-   `hasOne`: `profile: hasOne:Profile`
-   `belongsToMany`: `belongsToMany: [Category, Tag]`

**Polymorphic Relationships:**

-   `morphTo`: `commentable: morphTo`
-   `morphMany`: `comments: morphMany:Comment:commentable`
-   `morphOne`: `image: morphOne:Image:imageable`
-   `morphToMany`: `tags: morphToMany:Tag:taggable`

## 🔐 Two-Factor Authentication (2FA)

The starter kit includes comprehensive Two-Factor Authentication support using Google Authenticator (TOTP).

### 🚀 2FA Features

-   **TOTP Support** - Time-based One-Time Password using Google Authenticator
-   **QR Code Generation** - Automatic QR code for easy setup
-   **Recovery Codes** - 8 single-use recovery codes for account recovery
-   **Secure Storage** - Encrypted secret keys and recovery codes
-   **API Endpoints** - Complete REST API for 2FA management

### 📋 2FA API Endpoints

| Method   | Endpoint                          | Description                 | Rate Limit |
| -------- | --------------------------------- | --------------------------- | ---------- |
| `GET`    | `/api/v1/auth/2fa/status`         | Get 2FA status              | 120/min    |
| `POST`   | `/api/v1/auth/2fa/setup`          | Generate secret & QR code   | 3/hour     |
| `POST`   | `/api/v1/auth/2fa/verify`         | Verify code & enable 2FA    | 10/min     |
| `DELETE` | `/api/v1/auth/2fa/disable`        | Disable 2FA                 | 3/hour     |
| `POST`   | `/api/v1/auth/2fa/recovery-codes` | Generate new recovery codes | 3/hour     |

### 💡 Usage Examples

#### Setup 2FA

```bash
# Get 2FA setup data
curl -X POST http://localhost:8080/api/v1/auth/2fa/setup \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

#### Verify 2FA Code

```bash
# Verify with TOTP code
curl -X POST http://localhost:8080/api/v1/auth/2fa/verify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"code": "123456"}'

# Verify with recovery code
curl -X POST http://localhost:8080/api/v1/auth/2fa/verify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"recovery_code": "abcd1234ef"}'
```

## 🗄️ Database Optimization

The starter kit includes comprehensive database optimization features for production-ready performance.

### ⚡ Database Optimization Features

-   **Performance Indexes** - Automatically added to all database tables
-   **Query Caching** - Intelligent caching with TTL support
-   **Query Monitoring** - Real-time query performance tracking
-   **Slow Query Detection** - Automatic identification of performance bottlenecks
-   **Batch Operations** - Optimized bulk insert/update operations
-   **Cursor Pagination** - Efficient pagination for large datasets
-   **Database Analysis** - Table size and performance analysis

### 🛠️ Database Optimization Commands

```bash
# Analyze database tables and performance
php artisan db:optimize

# Monitor queries in real-time (30 seconds)
php artisan db:optimize --monitor --duration=30

# Get database connection information
php artisan db:optimize --connection-info

# Analyze specific table
php artisan db:optimize --table=users
```

### 📊 Database Indexes Added

**Users Table:**

-   `email_verified_at` - Email verification queries
-   `created_at` - User creation date queries
-   `updated_at` - User update date queries

**Sessions Table:**

-   `ip_address` - IP-based session queries
-   `user_id + last_activity` - User session activity queries

**Personal Access Tokens:**

-   `last_used_at` - Token usage queries
-   `expires_at` - Token expiration queries
-   `last_used_at + expires_at` - Token cleanup queries

**Permission Tables:**

-   `guard_name` - Guard-based permission queries
-   `created_at` - Permission creation queries
-   `updated_at` - Permission update queries

**2FA Columns:**

-   `two_factor_confirmed_at` - 2FA status queries

### 🔧 Query Optimization Features

-   **Conditional Eager Loading** - Prevents N+1 query problems
-   **Cache Pattern Invalidation** - Automatic cache cleanup
-   **Query Performance Monitoring** - Track slow queries
-   **Database Connection Optimization** - Optimized connection settings
-   **Batch Insert/Update** - Efficient bulk operations

## 🧩 Planned Features

-   [x] Event and Listener support
-   [x] Notification scaffolding
-   [x] Relationship sync logic from DTO
-   [x] Sanctum authentication integration
-   [x] Exception handling stubs per action
-   [x] Resource, DTO, Request,Action,Controller
-   [x] Feature test generation
-   [x] Migration and Factory generators
-   [x] Add Yaml support for module generation
-   [x] Two-Factor Authentication (2FA) support
-   [x] Interactive CLI Wizard for module generation
-   [x] Cross-module Event/Listener communication
-   [x] Sanctum SPA Authentication documentation
-   [x] Database optimization and performance monitoring
-   [x] Rollback mechanism for module generation
-   [x] Generation statistics and reporting
-   [x] YAML comments support
-   [x] Comprehensive test coverage (Integration, E2E, Snapshot tests)

## 📚 Additional Documentation

### Interactive CLI Wizard

The `make:module` command now supports an interactive wizard mode. Simply run:

```bash
php artisan make:module
```

The wizard will guide you through all module creation steps with helpful prompts.

### Event/Listener Communication

Learn how to implement cross-module communication using Events and Listeners:

📖 [Event/Listener Example Guide](docs/EVENT_LISTENER_EXAMPLE.md)

### Sanctum SPA Authentication

Complete guide for setting up Sanctum with Single Page Applications:

📖 [Sanctum SPA Authentication Guide](docs/SANCTUM_SPA_AUTHENTICATION.md)

## ✅ Requirements

-   PHP 8.4+
-   Laravel 12+
-   MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
-   Composer 2.0+
-   Docker & Docker Compose (for Docker setup)

## 🚀 PHP 8.4 Optimizations

This starter kit is fully optimized for **PHP 8.4** with modern features:

-   **Strict Type Declarations** - All files use `declare(strict_types=1)`
-   **Final Classes** - Policy, Observer, Event, Listener, and Notification classes are final by default
-   **Readonly Properties** - Event and Notification classes use readonly properties
-   **Latest PDO Constants** - Uses `\Pdo\Mysql::ATTR_SSL_CA` instead of deprecated `PDO::MYSQL_ATTR_SSL_CA`
-   **Constructor Property Promotion** - Simplified constructor syntax throughout
-   **Modern Type Hints** - Full type coverage with PHPDoc annotations
-   **Rector Integration** - Automatic code quality and PHP 8.4 compliance checks

## 💡 Notes

-   API-only – no Blade views or web routes.
-   Ideal for headless frontends (React, Vue, etc.)
-   Production-ready with comprehensive security and performance optimizations
-   Clean Architecture ensures maintainable and testable code
-   Database optimization provides enterprise-level performance
-   Two-Factor Authentication enhances security for sensitive applications
-   **PHP 8.4 Ready** - Fully compatible with latest PHP features and best practices

## 🤝 Contribution

-   Issues and feature requests welcome.
-   Pull Requests encouraged.

---

## 🐳 Docker Support

This starter kit includes full support for Docker. You can spin up the app, database, and web server with a single command.

### ✅ Getting Started

1. **Build and start containers**:

    ```bash
    docker compose up -d --build
    ```

2. **Stop containers**:

    ```bash
    docker compose down
    ```

3. **Access Laravel container (for running artisan/test/composer)**:

    ```bash
    docker exec -it app_module bash
    ```

4. **Run migrations**:

    ```bash
    docker exec -it app_module php artisan migrate
    ```

5. **Run tests**:

    ```bash
    docker exec -it app_module php artisan test
    ```

6. **MySQL connection (host machine)**:

-   **Host**: `127.0.0.1`
-   **Port**: `3301`
-   **User**: `homestead`
-   **Password**: `secret`
-   **Database**: `homestead`

7. **Access the application**:

-   **Web**: `http://localhost:8080`
-   **API Documentation**: `http://localhost:8080/api/documentation`
-   **Health Check**: `http://localhost:8080/api/health`
