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

## Module Structure
The generated module follows a structured format:
```
app/Modules/{ModuleName}
│── Config/
│── database/
│   ├── migrations/
│   ├── factories/
│   ├── seeds/
│── Exeptions (if --api flag is used)
│── Helpers/
│── Http/
│   ├── Controllers/
│   │   ├── {ModuleName}Controller.php
│   │   ├── Api/{ModuleName}Controller.php (if --api flag is used)
│   ├── Requests/
│   ├── Resources/
│── Interfaces/{ModuleName}Interface.php
│── Models/{ModuleName}.php
│── Repositories/{ModuleName}Repository.php
│── Resource
│    ├── Land
│    ├── Views
│── routes
│   ├── api.php (if --api flag is used)
│   ├── web.php
│── Services/{ModuleName}Service.php
│── Traits
└── 
```

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
- `stubs/module/Http/Request/` (for all form requests)
- `stubs/module/Model.stub`
- `stubs/module/routes/api.stub` (if `--api` is used)
- `stubs/module/routes/web.stub`
- `stubs/module/Repository.stub`
- `stubs/module/Service.stub`
- `stubs/module/Interface.stub`
