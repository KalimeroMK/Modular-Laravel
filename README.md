# Modular Laravel Starter Kit

**Modular Laravel Starter Kit** is an advanced, API-first starter package for Laravel. It focuses on modularity and clean architecture, aiming to make API development fast, sustainable, and scalable with minimal manual intervention.

## 🚀 Introduction

This kit is ideal for teams and developers who want a clean, well-structured codebase with clearly separated logic, using modern patterns such as repositories, DTOs, actions, and automatic relationship mapping.

## 🎯 Goals

- ✅ Automatic generation of complete API modules
- ✅ Minimal manual configuration
- ✅ Scalable and maintainable code
- ✅ Clear separation of concerns through modules
- ✅ No web UI or Blade support – API only

## 🔧 Features

- **Modular structure**: Each module is self-contained under `app/Modules`
- **Powerful CLI Generator**: Create complete modules via `php artisan make:module`
- **Dynamic field handling**: Fillables, casts, and relationships auto-handled
- **Flexible flags**:
    - `--exceptions`: Generate exception classes
    - `--observers`: Generate observer stubs
    - `--policies`: Generate policy stubs
- **Auto-discovery**: Routes, migrations, factories, observers, and policies
- **Repository pattern**: Interface-to-implementation binding out-of-the-box
- **Fully configurable**: `config/modules.php` for structure and behaviors

## ✅ Supported Field Types

| Laravel Type        | SQL Equivalent      | Description                        |
|---------------------|---------------------|------------------------------------|
| `string`            | VARCHAR             | Short text string                  |
| `char`              | CHAR                | Fixed-length string                |
| `text`              | TEXT                | Long text                          |
| `mediumText`        | MEDIUMTEXT          | Medium-length text                 |
| `longText`          | LONGTEXT            | Very long text                     |
| `integer`           | INT                 | Standard integer                   |
| `tinyInteger`       | TINYINT             | Very small integer                 |
| `smallInteger`      | SMALLINT            | Small integer                      |
| `mediumInteger`     | MEDIUMINT           | Medium-sized integer               |
| `bigInteger`        | BIGINT              | Large integer                      |
| `unsignedBigInteger`| BIGINT UNSIGNED     | Large unsigned integer             |
| `foreign`           | INT (FK)            | Foreign key (auto handled)         |
| `float`             | FLOAT               | Floating point number              |
| `double`            | DOUBLE              | Double-precision number            |
| `decimal`           | DECIMAL(8,2)        | Fixed precision decimal            |
| `boolean`           | TINYINT(1)          | Boolean (true/false)               |
| `enum`              | ENUM(...)           | Fixed set of values                |
| `date`              | DATE                | Date only                          |
| `datetime`          | DATETIME            | Date and time                      |
| `timestamp`         | TIMESTAMP           | Timestamp                          |
| `time`              | TIME                | Time only                          |
| `year`              | YEAR                | Year only                          |
| `json`              | JSON                | Structured JSON data               |
| `array`             | JSON (casted)       | PHP array via JSON cast            |
| `uuid`              | CHAR(36)            | UUID                               |
| `ipAddress`         | VARCHAR(45)         | IPv4/IPv6                          |
| `macAddress`        | VARCHAR(17)         | MAC address                        |
| `binary`            | BLOB                | Binary large object                |

## 🔄 Automatic Relationship Sync

You can use the `SyncRelations::execute()` helper to automatically sync both `belongsToMany` and `belongsTo` relationships using your DTO:

```php
SyncRelations::execute($model, [
    'tags' => $dto->tags,         // BelongsToMany
    'brand' => $dto->brand_id,    // BelongsTo
]);
```

- For `BelongsToMany`, it performs `$relation->sync(array)`
- For `BelongsTo`, it sets the foreign key and saves the model if changed.

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

| Flag         | Description                             |
|--------------|-----------------------------------------|
| `--model`    | Define fields and types for the model   |
| `--relations`| Add Eloquent relationships              |
| `--exceptions`| Generate Exceptions                    |
| `--observers`| Generate Observers and auto-register    |
| `--policies` | Generate Policies and auto-register     |

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

- Automatically generate all modules using the same logic as `make:module`
- Parse `fields`, `relations`, and options like `observers` and `policies`
- Fill in `fillable`, `casts`, `migrations`, `factories`, and `resources`
- Avoids manual repetition by letting you define multiple modules at once


## 🧩 Planned Features

- [x] Event and Listener support
- [x] Notification scaffolding
- [x] Relationship sync logic from DTO
- [x] Sanctum authentication integration
- [x] Exception handling stubs per action
- [x] Resource, DTO, Request,Action,Controller
- [x] Feature test generation
- [x] Migration and Factory generators
- [x] Add Yaml support for module generation
- [ ] Interactive CLI wizard (make:module step-by-step)

## ✅ Requirements

- Laravel 9.x+
- PHP 8.0+

## 💡 Notes

- API-only – no Blade views or web routes.
- Ideal for headless frontends (React, Vue, etc.)

## 🤝 Contribution

- Issues and feature requests welcome.
- Pull Requests encouraged.

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
- **Host**: `127.0.0.1`
- **Port**: `3306`
- **User**: `homestead`
- **Password**: `secret`
- **Database**: `homestead`

