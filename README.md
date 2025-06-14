# Modular Laravel Starter Kit

## Overview
This starter kit provides a robust, API-first modular architecture for Laravel projects. It automates the scaffolding of complete API modules (models, migrations, factories, controllers, actions, DTOs, exceptions, requests, resources, observers, policies, etc.) with dynamic field handling and minimal manual intervention.

## Features
- Modular structure: Each module is self-contained under `app/Modules`.
- Automated module generation via `php artisan make:module` command.
- Dynamic field, fillable, casts, and relationship support.
- Conditional generation of:
  - Exception classes (`--exceptions`)
  - Observers (`--observers`)
  - Policies (`--policies`)
- Eloquent relationship methods via `--relations` flag.
- Auto-registration of routes, helpers, migrations, factories, observers, and policies.
- Repository pattern with auto-binding in service provider.
- No web UI or blade views—REST API only.
- Clean, maintainable, and extendable codebase.

## Usage

### 1. Generate a New Module

You can use either style for defining foreign keys in the --model option:

- **Recommended:** Use Laravel-style (`user_id:int`)
- **Legacy/Advanced:** Use explicit foreign key syntax (`user_id:foreign:users:id`)

Both will work, but the recommended/cleanest approach is to use the standard naming convention:

```bash
php artisan make:module Example \
  --model="title:string,price:float,stock:int,is_active:bool,attributes:array,user_id:int" \
  --relations="user:belongsTo:User,orders:hasMany:Order" \
  --exceptions \
  --observers \
  --policies
```

- `user_id:int` is enough for the generator to treat it as a foreign key and add proper validation.
- You may still use `user_id:foreign:users:id` for maximum explicitness, but it is not required.

### 2. Flags
- Omit any flag to skip generating that component.
- You can use both `--model` and `--relations` for full model/relationship support.

### 3. Structure
Each module will have:
- `Models/`, `Repositories/`, `Interfaces/`, `Http/Controllers/`, `Http/Actions/`, `Http/Requests/`, `Http/Resources/`, `Http/DTOs/`, `Exceptions/`, `Observers/`, `Policies/`, `database/migrations/`, `database/factories/`, `routes/`

### 4. Auto-Discovery
- Observers and policies are auto-registered by the `ModularServiceProvider` if their files exist.
- No manual registration needed.

### 5. Configuration
- All module paths and settings are configurable via `config/modules.php`.

## Validation Rules for Foreign Keys (Update June 2025)

When you define a field as a foreign key (e.g. `user_id:int` or `restaurant_id:int`), the generated FormRequest validation will automatically include:

```php
'user_id' => ['required', 'integer', 'exists:users,id'], // foreign key, integer
```

- The table name (`users`) is inferred from the field name (`user_id`).
- The rule ensures the value is an integer and exists in the referenced table.
- This is fully automatic for any field ending with `_id` and of integer type.

## Example: Generating a Module with a Foreign Key

You can generate a module with a foreign key using a simple command:

```bash
php artisan make:module Klime \
  --model="title:string,price:float,stock:int,is_active:bool,attributes:array,user_id:int" \
  --relations="user:belongsTo:User" \
  --observers \
  --policies
```

- `user_id:int` will be automatically treated as a foreign key.
- The generated request class will include:

```php
'user_id' => ['required', 'integer', 'exists:users,id'], // foreign key, integer
```

No need to use `foreign:users:id` syntax—just use the standard Laravel naming convention for foreign keys.

## Extending
- Add new stubs to `stubs/module/` and extend the command as needed.
- Future support for events, notifications, and more can be added similarly.

## Requirements
- Laravel 9+
- PHP 8.0+

## Notes
- This kit is API-only—no blade views or web UI logic.
- Designed for rapid, maintainable API development.

---

For questions or contributions, open an issue or PR!
