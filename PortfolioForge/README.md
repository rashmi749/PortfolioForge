# PortfolioForge 🔷

**Apple-inspired premium portfolio builder** — PHP + MySQL. Create beautiful, fully responsive portfolios with dark mode, project showcases, skill charts, timeline sections, and a built-in contact system.

---

## ⚡ Quick Setup (5 minutes)

### 1. Requirements
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.4+
- Apache with `mod_rewrite` enabled (or Nginx equivalent)

### 2. Deploy files
Place the `PortfolioForge/` folder inside your web root (`htdocs/` or `www/`).

### 3. Create the database
```sql
-- In phpMyAdmin or MySQL CLI:
SOURCE /path/to/PortfolioForge/schema.sql;
```

### 4. Configure the database
Open `config/database.php` and update:
```php
$db_user = 'your_mysql_username';
$db_pass = 'your_mysql_password';
```

### 5. Configure email (optional)
Open `config/mail.php` and set:
```php
define('MAIL_FROM_ADDRESS', 'your_gmail@gmail.com');
define('MAIL_APP_PASSWORD',  'xxxx xxxx xxxx xxxx');   // Gmail App Password
```
> Email alerts are optional — contact messages are always saved to the database.

### 6. Set uploads permissions
```bash
chmod 755 uploads/
```

### 7. Visit the site
```
http://localhost/PortfolioForge/
```

---

## 🔑 Default Admin Login
| Field    | Value                  |
|----------|------------------------|
| Username | `admin`                |
| Password | `admin123`             |
| URL      | `/PortfolioForge/admin/dashboard.php` |

> **Change the admin password immediately after first login** via Edit Profile.

---

## 📂 Project Structure
```
PortfolioForge/
├── admin/              Admin panel (users & portfolio moderation)
├── assets/
│   ├── css/style.css   Design system
│   └── js/             Theme switcher & interactions
├── config/
│   ├── database.php    DB credentials ← edit this
│   └── mail.php        Email settings ← edit this
├── dashboard/          User-facing CRUD pages
├── includes/           Shared PHP components
├── libs/PHPMailer/     Email library
├── uploads/            User-uploaded images & resumes
├── schema.sql          Database schema + admin seed
├── index.php           Landing page
├── login.php           Sign in
├── register.php        Registration
├── portfolio.php       Public portfolio viewer
├── logout.php          Session termination
└── submit_contact.php  AJAX contact handler
```

---

## ✨ Features
- Apple-inspired glassmorphic UI with light/dark mode
- User registration & authentication with CSRF protection
- Dashboard to manage projects, skills, education, experience
- Resume PDF upload
- Public portfolio page per user at `/portfolio/{username}`
- Scroll-spy navigation, animated progress bars, reveal animations
- Contact form with AJAX + email notification + database storage
- Admin panel: user management, portfolio moderation, stats

---

## 🛠 Development Notes
- All SQL uses **PDO prepared statements** (SQL injection safe)
- Passwords hashed with **bcrypt** via `password_hash()`
- File uploads validated by MIME type via `finfo`, not extension
- XSS prevented via `htmlspecialchars()` (`escape()` helper)
- Session hardened with `httponly` cookie flag

---

*Built with PHP, MySQL, Bootstrap 5, PHPMailer.*
