# Laravel Product Manager

A professional, full-featured Laravel application for managing products and categories with a modern UI, comprehensive testing, and clean architecture.

## Features

### Product Management
- Full CRUD operations (Create, Read, Update, Delete)
- Image upload support with public storage
- Many-to-many category relationships
- Product filtering by category
- Price-based sorting (ascending/descending)
- Create products via web interface or CLI

### Category Management
- Full CRUD operations
- Hierarchical category structure (parent-child relationships)
- Circular hierarchy prevention
- Cascade delete with child preservation
- Category hierarchy visualization
- Product-category association

### Testing & Quality
- 64 passing tests (196 assertions)
- Unit tests for services and repositories
- Feature tests for controllers
- Test factories for data generation
- GitHub Actions CI pipeline
- 100% test coverage for core layers

### Architecture
- Repository Pattern for data access
- Service Layer for business logic
- Thin Controllers (HTTP concerns only)
- Form Requests for validation
- Factory Pattern for test data
- Clean separation of concerns

## 🛠 Tech Stack

- **Framework**: Laravel
- **PHP**: 8.1+
- **Database**: MySQL (dev) / SQLite (testing)
- **Testing**: PHPUnit
- **CI/CD**: GitHub Actions

## Prerequisites

- PHP 8.1 or higher
- MySQL 5.7+ (for development)
- Composer
- Git

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-product-manager
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Configure database** (edit `.env`)
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Create storage link** (for product images)
   ```bash
   php artisan storage:link
   ```

8. **Start development server**
   ```bash
   php artisan serve
   ```
   Visit: `http://localhost:8000`

## Usage

### Web Interface

#### Products
- **View Products**: Navigate to Products section
- **Create Product**: Click "Add New Product" button
- **Delete Product**: Click Delete button (with confirmation)
- **Filter**: Use category filter dropdown
- **Sort**: Sort by price (ascending/descending)

#### Categories
- **View Categories**: Navigate to Categories section
- **Create Category**: Click "Add Category" button
  - Optionally select a parent category for hierarchy
- **View Hierarchy**: See category tree structure
- **Delete Category**: Children are preserved and moved to parent

### CLI Commands

#### Create Product
```bash
php artisan product:create "Product Name" "Description" 29.99
```

#### Create Product with Categories
```bash
php artisan product:create "Laptop" "High performance laptop" 999.99 --categories=1,2,3
```

#### Create Product with Image
```bash
php artisan product:create "Product" "Description" 49.99 --image=/path/to/image.jpg
```

#### Create with All Options
```bash
php artisan product:create "Item" "Desc" 99.99 --categories=1,2 --image=/path/to/img.jpg
```

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Unit tests only
php artisan test tests/Unit

# Feature tests only
php artisan test tests/Feature

# Specific test file
php artisan test tests/Unit/ProductServiceTest.php
```

### Run with Coverage
```bash
php artisan test --coverage
```

## Database Schema

### Products Table
| Column | Type | Notes |
|--------|------|-------|
| id | BigInt | Primary key |
| name | String(255) | Product name |
| description | Text | Product description |
| price | Decimal(8,2) | Product price |
| image | String(255) | Optional image path |
| timestamps | - | created_at, updated_at |

### Categories Table
| Column | Type | Notes |
|--------|------|-------|
| id | BigInt | Primary key |
| name | String(255) | Category name |
| parent_id | BigInt | Optional parent category |
| timestamps | - | created_at, updated_at |

### category_product Pivot Table
| Column | Type | Notes |
|--------|------|-------|
| product_id | BigInt | Foreign key to products |
| category_id | BigInt | Foreign key to categories |
| Primary Key | - | Composite (product_id, category_id) |

## Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── CreateProductCommand.php    # CLI command for product creation
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php       # Product CRUD operations
│   │   └── CategoryController.php      # Category CRUD operations
│   └── Requests/
│       ├── CreateProductRequest.php    # Product validation
│       └── CreateCategoryRequest.php   # Category validation
├── Models/
│   ├── Product.php                     # Product model
│   └── Category.php                    # Category model
├── Repositories/
│   ├── ProductRepository.php           # Product data access
│   └── CategoryRepository.php          # Category data access
└── Services/
    ├── ProductService.php              # Product business logic
    └── CategoryService.php             # Category business logic

database/
├── migrations/                         # Database migrations
└── factories/                          # Test data factories
    ├── ProductFactory.php
    └── CategoryFactory.php

resources/views/
├── layouts/
│   └── app.blade.php                   # Main layout (Bootstrap 5.3)
├── products/
│   ├── index.blade.php                 # Products listing
│   ├── create.blade.php                # Create product form
│   ├── edit.blade.php                  # Edit product form
│   └── show.blade.php                  # Product details
└── categories/
    ├── index.blade.php                 # Categories listing
    ├── create.blade.php                # Create category form
    ├── edit.blade.php                  # Edit category form
    ├── show.blade.php                  # Category details
    └── partials/
        └── hierarchy.blade.php         # Category tree display

tests/
├── Unit/
│   ├── ProductServiceTest.php          # Product service tests (5 tests)
│   ├── ProductRepositoryTest.php       # Product repository tests (9 tests)
│   ├── CategoryServiceTest.php         # Category service tests (13 tests)
│   ├── CategoryRepositoryTest.php      # Category repository tests (7 tests)
│   └── CreateProductCommandTest.php    # CLI command tests (11 tests)
└── Feature/
    ├── CategoryControllerTest.php      # Category controller tests (18 tests)
    └── ExampleTest.php                 # Basic feature test (1 test)

routes/
└── web.php                             # Web routes configuration
```

## Git Workflow

Development organized by features in separate branches:

- `feature/services` - Core business logic (repositories, services, models)
- `feature/web-ui` - Web controllers and views
- `feature/cli-command` - Command-line interface
- `feature/category-management` - Category CRUD and hierarchy
- `feature/ui-improvements` - Modern UI redesign
- `main` 

## CI Pipeline

**GitHub Actions**: Automated testing on every push and pull request

### Workflow Configuration (`.github/workflows/tests.yml`)
- **Trigger**: Push to any branch & Pull requests
- **PHP Version**: 8.2
- **Database**: SQLite in-memory
- **Extensions**: pdo_sqlite, sqlite3
- **Coverage**: Generated on pull requests with minimum 80%

### Test Results
```
64 tests passing
196 assertions
0 failures
```
