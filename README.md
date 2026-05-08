# UMU Event Management System
**Uganda Martyrs University – CSC 2202 Web Based Systems Programming**
**End-of-Semester Project | Semester 2, 2025/2026**

---

## System Description

A full-featured web application for managing university events. Students can register, browse upcoming events, RSVP, and manage their RSVP history. Administrators can create, edit, and delete events, view attendee lists, and manage user accounts.

**Tech Stack:** PHP 7.4+, MySQL, HTML5, CSS3

---

## Features Implemented

| # | Feature |
|---|---------|
| 1 | User Registration & Login |
| 2 | Session Control & Logout |
| 3 | Data Entry via Forms |
| 4 | Input Validation with Feedback |
| 5 | Dynamic Data Display |
| 6 | Search & Filter Events |
| 7 | Edit Existing Records |
| 8 | Delete Records |
| 9 | Role-Based Access (Student / Admin) |
| 10 | Persistent MySQL Database |

---

## Setup Instructions

### Prerequisites
- XAMPP or WAMP installed
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1 – Copy Project Files
Place the entire `event_management` folder inside your web server root:
- **XAMPP:** `C:/xampp/htdocs/event_management`
- **WAMP:** `C:/wamp64/www/event_management`

### Step 2 – Import the Database
1. Start **Apache** and **MySQL** in XAMPP/WAMP
2. Open your browser and go to: `http://localhost/phpmyadmin`
3. Click **New** to create a new database named `event_management`
4. Select the new database, click the **Import** tab
5. Click **Choose File**, select `event_db.sql` from the project root
6. Click **Go** to import

### Step 3 – Configure Database Connection (if needed)
Open `includes/config.php` and update these values if your setup differs:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // your MySQL username
define('DB_PASS', '');       // your MySQL password
define('DB_NAME', 'event_management');
```

### Step 4 – Run the Application
Open your browser and go to:
```
http://localhost/event_management/
```

---

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@umu.ac.ug | admin123 |
| **Student** | student@umu.ac.ug | student123 |

> You can also register a new student account from the registration page.

---




---

## Database Structure

**users** — id, full_name, email, password (hashed), role (student/admin), reg_number, created_at

**events** — id, title, description, category, event_date, event_time, venue, capacity, created_by (FK), created_at

**rsvps** — id, user_id (FK), event_id (FK), rsvp_date | UNIQUE(user_id, event_id)

---

*Submitted for CSC 2202 End-of-Semester Examination*
