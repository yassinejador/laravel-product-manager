# Laravel Product Manager

A clean, well-designed Laravel application for managing products and categories with comprehensive automated tests.

## Features

- Product management (CRUD operations)
- Category management with hierarchical support
- Many-to-many product-category relationships
- Product creation via web interface and CLI
- Image upload support
- Product filtering by category and price sorting
- Comprehensive unit and feature tests
- GitHub Actions CI pipeline

## Architecture

- **Repository Pattern**: Data access layer abstraction
- **Service Layer**: Shared business logic between web and CLI
- **Thin Controllers**: Focus on HTTP concerns only
- **Blade Templates**: Server-side rendering with minimal CSS
- **Factory Pattern**: Test data generation

## Prerequisites

- PHP 8.1+
- MySQL 5.7+ (development) or SQLite (testing)
- Composer

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Create environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate app key:
   ```bash
   php artisan key:generate
   ```

5. Configure database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Create storage link:
   ```bash
   php artisan storage:link
   ```

## Usage

### Web Interface

Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the product listing page.

### CLI Commands

Create a product via CLI:
```bash
php artisan product:create "Product Name" "Product Description" 29.99
```

Create a product with categories:
```bash
php artisan product:create "Smart Watch" "Wearable device" 199.99 --categories=1,2
```

Create a product with image:
```bash
php artisan product:create "Product" "Description" 49.99 --image=/path/to/image.jpg
```

## Testing

Run all tests:
```bash
php artisan test
```

Run specific test file:
```bash
php artisan test tests/Unit/ProductServiceTest.php
```

Run tests with coverage:
```bash
php artisan test --coverage
```

**Note**: Tests use SQLite in-memory database for speed. Local development uses MySQL.

## Database Schema

### Products Table
- `id`: Primary key
- `name`: Product name (max 255 chars)
- `description`: Product description
- `price`: Product price (decimal)
- `image`: Optional image file path

### Categories Table
- `id`: Primary key
- `name`: Category name
- `parent_id`: Optional self-reference for hierarchy

### category_product Pivot Table
- `product_id`: Foreign key to products
- `category_id`: Foreign key to categories

## Project Structure

```
app/
├── Console/Commands/CreateProductCommand.php
├── Http/Controllers/ProductController.php
├── Http/Requests/CreateProductRequest.php
├── Models/Product.php
├── Models/Category.php
├── Repositories/ProductRepository.php
├── Repositories/CategoryRepository.php
└── Services/ProductService.php

database/
├── migrations/
└── factories/

resources/views/products/
├── index.blade.php
├── create.blade.php
├── show.blade.php
└── edit.blade.php

tests/
├── Unit/
└── Feature/
```

## CI

GitHub Actions automatically runs tests on:
- Push to any branch
- Pull requests

Tests run with SQLite in-memory database for speed.

## Git Workflow

Development follows feature branch workflow:
- `feature/services` - Repository, Service, and test layers
- `feature/web-ui` - Web controllers, views, and form requests
- `feature/cli-command` - CLI command implementation
- `main`