# 📋 Task Assignment System

A Laravel 11 application for task management and high-performance Excel data import with support for large files (30,000+ rows).

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🔐 **Authentication** | Secure user auth with Laravel Sanctum |
| 📁 **Task Management** | Full CRUD operations for tasks |
| 📂 **Categories** | Organize tasks by categories |
| 📊 **Excel Import** | Import large Excel files with chunked processing |
| 🌍 **Arabic Support** | Full Arabic column header support |

---

## 🛠️ Tech Stack

- **PHP**: 8.2+
- **Framework**: Laravel 11
- **Auth**: Laravel Sanctum
- **Excel**: Maatwebsite Excel 3.1
- **DTOs**: Spatie Laravel Data

---

## 🏗️ Architecture

```
✅ Dependency Injection
✅ SOLID Principles
✅ Repository Pattern
✅ Data Transfer Objects (DTOs)
✅ Form Request Validation
```

---

## 📦 Installation

```bash
# 1. Clone repository
git clone https://github.com/fcimah1/Task_assignment.git
cd Task_assignment

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Database
php artisan migrate

# 5. Run server
php artisan serve
```

---

## ⚙️ Configuration for Large Files

### Option 1: Edit `php.ini`
```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 1024M
max_execution_time = 0
```

### Option 2: Run with flags
```bash
php -d memory_limit=1024M artisan serve
```

---

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── AuthController.php        # Authentication
│   ├── TaskController.php        # Task CRUD
│   ├── CategoryController.php    # Category CRUD
│   └── ImportController.php      # Excel import
├── Imports/
│   └── ItemsTransactionsImport.php
├── Models/
│   ├── User.php
│   ├── Task.php
│   ├── Category.php
│   └── ItemsTransaction.php
└── Repositories/
```

---

## 📊 Excel Import

### Supported Formats
`.xlsx` | `.xls` | `.csv`

### Column Mapping

| Arabic | English | Field |
|--------|---------|-------|
| كود الصنف | item_code | Item code |
| وصف الصنف | item_name | Description |
| الكمية | quantity | Quantity |
| الوحده | unit | Unit |
| التاريخ | date | Date |
| الوقت | time | Time |
| مركز التكلفة | cost_center | Cost center |
| تكلفة الوحدات | unit_cost | Unit cost |
| الاجمالي 2 | total | Total |

### Performance
- ⚡ **Chunked**: 1,000 rows per batch
- ⚡ **Batch Insert**: High-performance DB writes
- ⚡ **30,000+ rows**: Tested and optimized

---

## 🔌 API Endpoints

### Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register user |
| POST | `/api/login` | Login |
| POST | `/api/logout` | Logout |

### Tasks
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | List tasks |
| POST | `/api/tasks` | Create task |
| GET | `/api/tasks/{id}` | Get task |
| PUT | `/api/tasks/{id}` | Update task |
| DELETE | `/api/tasks/{id}` | Delete task |

### Categories
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List categories |
| POST | `/api/categories` | Create category |

### Import
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/import` | Import Excel file |

---

## 📄 License

MIT License - see [LICENSE](LICENSE) for details.

---

<p align="center">
  Made with ❤️ using Laravel
</p>
