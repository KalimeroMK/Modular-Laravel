# Modular Laravel Starter Kit

**Modular Laravel Starter Kit** is an advanced, API-first starter package for Laravel. It focuses on modularity and clean architecture, aiming to make API development fast, sustainable, and scalable with minimal manual intervention.

## 🚀 Introduction

This kit is ideal for teams and developers who want a clean, well-structured codebase with clearly separated logic, using modern patterns such as repositories, DTOs, actions, and automatic relationship mapping.

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
# Web: http://localhost
# API Docs: http://localhost/api/documentation
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
```

## 🎯 Goals

-   ✅ Automatic generation of complete API modules
-   ✅ Minimal manual configuration
-   ✅ Scalable and maintainable code
-   ✅ Clear separation of concerns through modules
-   ✅ No web UI or Blade support – API only

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

```bash
php artisan make:module Product \
  --model="name:string,price:float,stock:int,is_active:bool,category_id:int" \
  --relations="category:belongsTo:Category,reviews:hasMany:Review" \
  --exceptions \
  --observers \
  --policies
```

### 2. Flags

| Flag           | Description                           |
| -------------- | ------------------------------------- |
| `--model`      | Define fields and types for the model |
| `--relations`  | Add Eloquent relationships            |
| `--exceptions` | Generate Exceptions                   |
| `--observers`  | Generate Observers and auto-register  |
| `--policies`   | Generate Policies and auto-register   |

### 3. Structure

```
app/Modules/Example/
├── Models/
├── Repositories/
├── Interfaces/
├── Http/
│   ├── Controllers/
│   ├── Actions/
│   ├── Requests/
│   ├── Resources/
│   └── DTOs/
├── Exceptions/
├── Observers/
├── Policies/
├── routes/
│   └── api.php
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

## 📦 Module Generation via YAML

In addition to the `php artisan make:module` command, you can now generate multiple modules at once using a YAML configuration file.

### 🔧 Usage

1. Create a `modules.yaml` file in the root of your project:

```yaml
modules:
    Product:
        fields:
            name: string
            price: float
            is_active: boolean
        relations:
            belongsToMany: [Category]
            user: belongsTo:User
        observers: true
        policies: true

    Category:
        fields:
            name: string
        relations:
            belongsToMany: [Product]
```

2. Run the command:

```bash
php artisan modules:build-from-yaml
```

This will:

> 📌 Note: Pivot migrations are automatically generated **only** when using `modules:build-from-yaml` and when both related modules define a `belongsToMany` relationship to each other.

-   Automatically generate all modules using the same logic as `make:module`
-   Parse `fields`, `relations`, and options like `observers` and `policies`
-   Fill in `fillable`, `casts`, `migrations`, `factories`, and `resources`
-   Avoids manual repetition by letting you define multiple modules at once

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
-   [ ] Interactive CLI wizard (make:module step-by-step)

## ✅ Requirements

-   PHP 8.3+

## 💡 Notes

-   API-only – no Blade views or web routes.
-   Ideal for headless frontends (React, Vue, etc.)

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
-   **Port**: `3306`
-   **User**: `homestead`
-   **Password**: `secret`
-   **Database**: `homestead`
