# Complaint System

A Laravel-based complaint management system designed for students to submit and track complaints, with administrative features for managing and resolving issues.

## Features

- **Student Portal**: Submit, track, and manage complaints
- **Admin Dashboard**: Review, assign, and resolve complaints
- **Authentication System**: Secure login for students and administrators
- **Community Features**: Upvoting and commenting system
- **Status Tracking**: Real-time complaint status updates

## Requirements

- PHP >= 7.4
- Composer
- MySQL/MariaDB
- Node.js & NPM (for frontend assets)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/doopii/Complaint-System.git
cd Complaint-System
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file

7. Run database migrations:
```bash
php artisan migrate
```

8. Seed the database (optional):
```bash
php artisan db:seed
```

9. Build frontend assets:
```bash
npm run dev
```

## Usage

Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
