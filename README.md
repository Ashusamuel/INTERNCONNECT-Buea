# InternConnect Buea
Internship discovery and application management platform for students and organizations in Buea.
Built with HTML5, CSS3, PHP (PDO) and MySQL for XAMPP / LAMP stack.

## Complete 12-Stage System Overview
- **Stage 1**: Foundation & Database Setup (`config/database.php`, `test-connection.php`)
- **Stage 2**: Student Authentication (`student/register.php`, `student/login.php`)
- **Stage 3**: Student Dashboard, Profile Management & CV Upload (`student/profile.php`, `student/edit-profile.php`)
- **Stage 4**: Internship Discovery & Search (`student/internships.php`, `student/search.php`, `student/internship-details.php`)
- **Stage 5**: Eligibility Checker (`check_student_eligibility()` comparing level, profile completion & CV)
- **Stage 6**: Student Applications System (`student/apply.php`, `student/applications.php`)
- **Stage 7**: Saved / Bookmarked Internships (`student/saved.php`)
- **Stage 8**: Organization Authentication & Profile (`organization/register.php`, `organization/login.php`, `organization/profile.php`, `organization/edit-profile.php`)
- **Stage 9**: Post & Manage Internships (`organization/post-internship.php`, `organization/internships.php`, `organization/edit-internship.php`)
- **Stage 10**: Application Management for Organizations (`organization/applications.php`, `organization/application-details.php`)
- **Stage 11**: System Administrator Portal (`admin/login.php`, `admin/dashboard.php`, `admin/organizations.php`, `admin/users.php`, `admin/internships.php`)
- **Stage 12**: Navigation Integration, Syntax Verification & Final Documentation.

---

## Installation on XAMPP / LAMP
1. Copy the `internconnect` directory into `xampp/htdocs/` (or web root).
2. Start **Apache** and **MySQL**.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), create a database named `internconnect`, and import `database/internconnect.sql`.
   *(Alternative command line import: `mysql -u root -p internconnect < database/internconnect.sql`)*
4. Verify database credentials in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'internconnect');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('BASE_URL', '/internconnect');
   ```
5. Open `http://localhost/internconnect/index.php` in your browser.

## Default Access Credentials
- **Admin Account**:
  - Email: `admin@internconnect.cm`
  - Password: `Admin@123`

- **Sample Organization Account**:
  - Email: `info@techcameroon.cm`
  - Password: `Org@123`

- **Sample Student Account**:
  - Email: `student@ubuea.cm`
  - Password: `Password123!`

---

## Code Base Layout

```
internconnect/
├── index.php                 Landing page with platform overview
├── login.php                 Unified login portal chooser
├── logout.php                Session destroyer and logout handler
├── test-connection.php       Database connection check script
├── config/
│   └── database.php          PDO database connection & global constants
├── includes/
│   ├── auth.php              Session management & RBAC (require_role)
│   ├── functions.php         Core business logic & helper functions
│   ├── header.php            HTML header wrapper
│   ├── navbar.php            Role-based dynamic navigation bar
│   ├── footer.php            HTML footer wrapper
│   ├── internship-card.php   Reusable internship card component
│   └── search-form.php       Reusable search filter form
├── css/
│   └── style.css             Vanilla CSS design system
├── js/
│   └── main.js               Client-side interactions & mobile nav toggle
├── uploads/
│   └── cv/                   Secure directory for uploaded student CVs
├── database/
│   ├── internconnect.sql     Complete database schema & initial sample data
│   └── stage5_12.sql         Incremental migration script for Stages 5–12
├── student/                  Student portal pages
│   ├── register.php
│   ├── login.php
│   ├── dashboard.php
│   ├── profile.php
│   ├── edit-profile.php
│   ├── internships.php
│   ├── search.php
│   ├── internship-details.php
│   ├── apply.php
│   ├── applications.php
│   └── saved.php
├── organization/             Organization portal pages
│   ├── register.php
│   ├── login.php
│   ├── dashboard.php
│   ├── profile.php
│   ├── edit-profile.php
│   ├── internships.php
│   ├── post-internship.php
│   ├── edit-internship.php
│   ├── applications.php
│   └── application-details.php
└── admin/                    System administrator portal pages
    ├── login.php
    ├── dashboard.php
    ├── organizations.php
    ├── users.php
    └── internships.php
```

---

## Security & Architecture Highlights

1. **Prepared Statements**: All database operations use PDO prepared statements with parameter binding to eliminate SQL injection vulnerabilities.
2. **Password Security**: Standard `password_hash()` (BCRYPT) and `password_verify()` for secure credentials storage.
3. **Role-Based Access Control (RBAC)**: Enforced via `require_role('student')`, `require_role('organization')`, and `require_role('admin')` helpers.
4. **Input Sanitization**: Output escaping via `e()` (`htmlspecialchars`) to defend against XSS attacks.
5. **Database Integrity**: Transactional account creation ensuring atomic operations across related tables (`users`, `students`, `organizations`).
