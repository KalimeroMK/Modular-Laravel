# Modular Laravel

`Introduction`
The following Laravel project/directory structure represents a personal boilerplate modular structure that I use most of the time when starting a new Laravel project.

I found myself creating the same structure multiple times during the past couple of months so I decided to create a boilerplate project starter.

## Core structure

The Core module contains the main interfaces, abstract classes and implementations

```
app
├── Modules
│   └── Core
│       ├── Controllers
│       |   ├── ApiController.php
|       |   └── Controller.php
│       ├── Exceptions
│       |   ├── FormRequestTableNotFoundException.php
│       |   ├── GeneralException.php
│       |   ├── GeneralIndexException.php
│       |   ├── GeneralSearchException.php
│       |   ├── GeneralStoreException.php
│       |   ├── GeneralNotFoundException.php
│       |   ├── GeneralDestroyException.php
|       |   └── GeneralUpdateException.php
│       ├── Filters
│       |   ├── QueryFilter.php
│       ├── Helpers
|       |   └── Helper.php
│       ├── Interfaces
│       |   ├── SearchInterface.php
|       |   └── RepositoryInterface.php
│       ├── Models
|       |   └── .gitkeep
│       ├── Repositories
|       |   └── Repository.php
│       ├── Requests
│       |   ├── FormRequest.php
│       |   ├── CreateFormRequest.php
│       |   ├── DeleteFormRequest.php
│       |   ├── SearchFormRequest.php
│       |   ├── UpdateFormRequest.php
|       |   └── ShowFormRequest.php
│       ├── Resources
│       |   └── .gitkeep 
│       ├── Scopes
|       |   └── .gitkeep
│       ├── Traits
│       |   ├── ApiResponses.php
│       ├── Transformers
│       |   ├── EmptyResource.php
|       |   └── EmptyResourceCollection.php
│       └── 
└── 
```
## Command Usage
```
php artisan make:module {name} [--api]
```

### Parameters:
- `{name}` - The name of the module (required).
- `--api` - Optional flag to generate API-specific controllers, resources, and routes.

## Features
- Creates a modular directory structure inside `app/Modules/{ModuleName}`.
- Generates controllers, models, repositories, services, and more.
- Optionally includes API resources and controllers when `--api` is provided.
- Updates the `RepositoryServiceProvider` to bind repositories automatically.
- Cleans up the module structure in case of failures.

## Module Structure
The generated module follows a structured format:
```
app/Modules/{ModuleName}
│── Config/
│── Http/
│   ├── Controllers/
│   │   ├── {ModuleName}Controller.php
│   │   ├── Api/{ModuleName}Controller.php (if --api flag is used)
│   ├── Requests/
│   ├── Resources/
│── Models/{ModuleName}.php
│── Repositories/{ModuleName}Repository.php
│── Services/{ModuleName}Service.php
│── routes/
│   ├── api.php (if --api flag is used)
│   ├── web.php
│── database/
│   ├── migrations/
│   ├── factories/
│── Traits/
│── Exceptions/
│── Interfaces/
```

## Implementation Details
The command follows these main steps:
1. **Checks if the module already exists** to prevent overwriting.
2. **Creates the module structure** based on whether `--api` is included.
3. **Generates required files** using stub templates.
4. **Updates the RepositoryServiceProvider** to ensure the module works seamlessly.
5. **Handles errors and cleans up** if the creation process fails.

## Example Usage
- **Basic Module Creation:**
  ```
  php artisan make:module Blog
  ```
  This will generate a `Blog` module with the default structure.

- **Module with API Support:**
  ```
  php artisan make:module Blog --api
  ```
  This includes additional API-related files such as API controllers and resources.

## Stub Files Used
The command references stub files for file generation. The key files used are:
- `stubs/module/Http/Controllers/Controller.stub`
- `stubs/module/Http/Controllers/ApiController.stub` (if `--api` is used)
- `stubs/module/Http/Resource/Resource.stub` (if `--api` is used)
- `stubs/module/Model.stub`
- `stubs/module/routes/api.stub` (if `--api` is used)
- `stubs/module/routes/web.stub`
- `stubs/module/Repository.stub`
- `stubs/module/Service.stub`

## Conclusion
`MakeModuleCommand` is a powerful tool for Laravel developers looking to implement modular architectures efficiently. With its ability to generate API-ready modules, it helps streamline development and maintainability.
