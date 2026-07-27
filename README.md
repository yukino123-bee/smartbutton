# 🚨 Smart Button — Emergency Response System

A Smart Student Panic Button and Emergency Response System with Campus Incident Mapping, built with Laravel 13.

---

## ⚡ Quick Setup (After Cloning)

Run these commands **in order** after cloning the repo:

### 1. Install PHP Dependencies
```bash
composer install
```

### 2. Install JS Dependencies
```bash
npm install
```

### 3. Setup Environment File
```bash
cp .env.example .env          # Linux/Mac
copy .env.example .env        # Windows
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. Create the SQLite Database File
```bash
# Linux/Mac
touch database/database.sqlite

# Windows (Command Prompt)
type nul > database\database.sqlite
```

### 6. Run Migrations
```bash
php artisan migrate --force
```

### 7. Seed Default Users
```bash
php artisan db:seed
```

### 8. Start the Dev Server
```bash
composer run dev
```

The app will be available at **http://localhost:8000**

---

## 👤 Default Login Credentials

| Role    | Username | Password |
|---------|----------|----------|
| Clinic  | clinic   | password |
| NDRRMO  | ndrrmo   | password |

---

## 🛠️ Requirements

| Requirement | Version  |
|-------------|----------|
| PHP         | ^8.3     |
| Composer    | ^2.x     |
| Node.js     | ^18.x    |
| NPM         | ^9.x     |

---

## 📁 Tech Stack

- **Backend**: Laravel 13
- **Frontend**: Vite + Laravel Mix
- **Database**: SQLite (local) 
- **Realtime**: Laravel Reverb (WebSockets)
- **Auth**: Laravel Sanctum
- **Queue**: Laravel Queue (database driver)

---

## 🚀 Available Commands

```bash
composer run dev      # Start all dev servers (PHP + Queue + Vite)
composer run test     # Run test suite
composer run setup    # Full first-time setup (install + migrate + build)
```
