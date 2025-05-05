# Modular Laravel

## Purpose

Modular Laravel is a robust boilerplate and code generator designed to help you rapidly build scalable, maintainable, and feature-rich Laravel applications using a modular architecture. The project aims to:
- **Promote clean separation of concerns** by organizing code into modules, each encapsulating its own business logic, data structures, and HTTP layer.
- **Streamline development** with powerful code generation for controllers, models, requests, resources, DTOs, actions, repositories, and more.
- **Enforce best practices** such as the use of Data Transfer Objects (DTOs) and Action classes for all business logic, eliminating service classes for a cleaner, more maintainable codebase.

## What Has Been Refactored

- **Auth, Permission, Role, and User modules** have been fully refactored to use DTOs and Action classes. All legacy service classes have been removed.
- **Controllers** are now slim and delegate all business logic to dedicated Action classes.
- **DTOs** encapsulate all data transfer and validation logic between layers.
- **Actions** encapsulate all business logic and interact with repositories.
- **Exception handling** is module-specific and cleanly separated.

## Overview

Modular Laravel is a boilerplate and code generator for quickly scaffolding feature-rich, modular Laravel applications. It provides a flexible structure for building both web and API modules, with a focus on separation of concerns and rapid development.

### Key Features
- **Modular structure:** Clean separation of code by module, each with its own controllers, models, requests, resources, repositories, etc.
- **Automatic code generation:** Use the `make:module` Artisan command to generate all boilerplate for a new module, including models, migrations, requests, resources, DTOs, actions, and more.
- **API and web support:** Generate modules for API-only or web projects, with the appropriate controllers, routes, and resources.
- **Dynamic validation and resource fields:** When specifying fields and relationships, validation rules and resource output are automatically generated for you.
- **Extensible stubs:** All generated files are based on customizable stub files, allowing you to tailor the boilerplate to your project's needs.

## How to Use

### 1. Getting Started

1. **Clone the repository and install dependencies:**
   ```bash
   git clone <your-repo-url>
   composer install
   cp .env.example .env
   php artisan key:generate
   # Configure your database in .env
   ```

2. **Generate a new module:**
   ```bash
   php artisan make:module Blog --api --migrations --model="name:string,coverImg:string,restaurant_id:foreignId" --relationships="restaurant:belongsTo,pictures:hasMany"
   ```
   This will generate a Blog module with:
   - API controllers and routes
   - Model and migration with the specified fields and relationships
   - Form requests with validation rules matching the fields
   - Resource classes that include the specified fields and relationships
   - DTOs, actions, repositories, and interfaces

### 2. Using the Refactored Modules

- **Controllers**: Only handle HTTP request/response logic. All business logic is delegated to Action classes.
- **Actions**: Encapsulate business logic for create, update, delete, show, search, etc. Always use DTOs for input.
- **DTOs**: Use the `fromArray` and `toArray` methods to safely transfer and validate data between layers.
- **Repositories/Interfaces**: Handle all data persistence and retrieval.

Example usage in a controller:
```php
$dto = CreateBlogDTO::fromArray($request->validated());
$blog = app(CreateBlogAction::class)->execute($dto);
return new BlogResource($blog);
```

### 3. Customization
- Edit stub files in `stubs/module/*` to change the default code templates for any generated file.
- Add or remove fields, relationships, or logic as needed after generation.

## Project Structure
Each module is generated under `app/Modules/{ModuleName}` and contains the following structure:

```
app/Modules/{ModuleName}
│── Config/
│── database/
│   ├── migrations/
│   ├── factories/
│── Exceptions/                # Only if --api is used
│── Helpers/
│── Http/
│   ├── Controllers/
│   │   ├── {ModuleName}Controller.php
│   │   └── Api/{ModuleName}Controller.php   # Only if --api is used
│   ├── DTOs/
│   │   └── {ModuleName}DTO.php
│   ├── Actions/
│   │   ├── Create{ModuleName}Action.php
│   │   ├── Update{ModuleName}Action.php
│   │   ├── Delete{ModuleName}Action.php
│   │   └── Show{ModuleName}Action.php
│   ├── Requests/
│   │   ├── Create{ModuleName}Request.php
│   │   ├── Update{ModuleName}Request.php
│   │   ├── Delete{ModuleName}Request.php
│   │   ├── Search{ModuleName}Request.php
│   │   └── Show{ModuleName}Request.php
│   ├── Resources/
│   │   └── {ModuleName}Resource.php         # Only if --api is used
│── Interfaces/
│   └── {ModuleName}Interface.php
│── Models/
│   └── {ModuleName}.php
│── Repositories/
│   └── {ModuleName}Repository.php
│── routes/
│   ├── api.php                # Only if --api is used
│   └── web.php
│── Traits/
└── ...
```

### Example: Blog Module (API)
```
app/Modules/Blog/
├── Http/
│   ├── Controllers/
│   │   └── Api/BlogController.php
│   ├── DTOs/BlogDTO.php
│   ├── Actions/
│   │   ├── CreateBlogAction.php
│   │   ├── UpdateBlogAction.php
│   │   ├── DeleteBlogAction.php
│   │   └── ShowBlogAction.php
│   ├── Requests/
│   │   ├── CreateBlogRequest.php
│   │   ├── UpdateBlogRequest.php
│   │   ├── DeleteBlogRequest.php
│   │   ├── SearchBlogRequest.php
│   │   └── ShowBlogRequest.php
│   ├── Resources/BlogResource.php
├── Models/Blog.php
├── database/migrations/...
├── Repositories/BlogRepository.php
├── Interfaces/BlogInterface.php
├── routes/api.php
└── ...
```

## Usage Examples
- **Basic Module:**
  ```bash
  php artisan make:module Blog
  ```
- **API Module:**
  ```bash
  php artisan make:module Blog --api
  ```
- **Module with Fields and Relationships:**
  ```bash
  php artisan make:module Blog --migrations --model="name:string,coverImg:string,restaurant_id:foreignId" --relationships="restaurant:belongsTo,pictures:hasMany"
  ```

## Dynamic Validation & Resource Generation
- **Validation:**
  - Fields specified in `--model` are automatically added to the validation rules in `Create` and `Update` request classes.
  - Foreign keys (e.g., `restaurant_id:foreignId`) get `exists` validation rules.
- **Resource Output:**
  - All fields and relationships are included in the generated Resource class for API responses.

## Customization
- You can edit stub files in `stubs/module/*` to change the default code templates for any generated file.
- Add or remove fields, relationships, or logic as needed after generation.

## Stubs Used
Key stub files used for code generation:
- `stubs/module/Http/Controllers/Controller.stub`
- `stubs/module/Http/Controllers/ApiController.stub`
- `stubs/module/Http/Resource/Resource.stub`
- `stubs/module/Http/Request/CreateRequest.stub`
- `stubs/module/Http/Request/UpdateRequest.stub`
- `stubs/module/Model.stub`
- `stubs/module/routes/api.stub`
- `stubs/module/routes/web.stub`
- `stubs/module/Repository.stub`
- `stubs/module/Interface.stub`

## Contributing
Feel free to open issues or submit PRs to improve the generator or its stubs!

## License
MIT
