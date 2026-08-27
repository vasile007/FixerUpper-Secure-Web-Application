# 🔐 FixerUpper — Secure E-Commerce Web Application

FixerUpper is a full-stack e-commerce web application built with **PHP and MySQL**, with a strong focus on secure web development.

The application provides a complete shopping workflow including product browsing, user registration and authentication, shopping cart management, checkout, order processing and account management.

Security controls are integrated throughout the application to protect against common web vulnerabilities including **SQL Injection, Cross-Site Scripting (XSS), Cross-Site Request Forgery (CSRF), password theft and session hijacking**.

---

## 🚀 Features

### 🛒 E-Commerce

- Product catalogue
- Shopping cart management
- User registration
- Secure login and logout
- Customer account management
- Checkout process
- Delivery information
- Order confirmation
- Order storage in MySQL
- Automatic session timeout

### 🔐 Security

The application implements multiple security controls rather than relying only on client-side validation.

#### Password Security
User passwords are protected using PHP's password hashing and verification mechanisms instead of being stored as plain text.

#### SQL Injection Protection
Database operations use **PDO prepared statements**, preventing user input from being directly interpreted as SQL commands.

#### Cross-Site Scripting (XSS) Protection
User-controlled output is escaped and sanitised before being rendered in the browser.

#### CSRF Protection
Sensitive forms use **CSRF tokens** that are generated and validated server-side before requests are processed.

#### Session Security
The application includes:

- Secure session configuration
- HttpOnly cookies
- SameSite cookie protection
- Session ID regeneration after authentication
- Automatic session timeout
- Protection against session fixation/hijacking

#### Server-Side Validation
Important input is validated on the server rather than relying exclusively on browser-side validation.

---

## 🛠️ Technology Stack

| Technology | Purpose |
|---|---|
| PHP | Backend application logic |
| MySQL | Relational database |
| PDO | Secure database access |
| HTML5 | Application structure |
| CSS3 | Styling |
| Bootstrap 5 | Responsive user interface |
| JavaScript | Client-side interaction and validation |
| Apache | Local web server |
| XAMPP | Local development environment |

---

## 🏗️ Application Architecture

```text
                  ┌─────────────────┐
                  │      User       │
                  └────────┬────────┘
                           │
                           ▼
                ┌─────────────────────┐
                │   HTML / Bootstrap  │
                │     JavaScript      │
                └─────────┬───────────┘
                          │
                          ▼
                 ┌──────────────────┐
                 │    PHP Backend   │
                 │                  │
                 │ Authentication   │
                 │ Shopping Cart    │
                 │ Checkout         │
                 │ Orders           │
                 │ Security Logic   │
                 └────────┬─────────┘
                          │
                     PDO Prepared
                     Statements
                          │
                          ▼
                 ┌──────────────────┐
                 │      MySQL       │
                 │                  │
                 │ Users            │
                 │ Products         │
                 │ Orders           │
                 └──────────────────┘
```

---

## 🛡️ Security Architecture

```text
User Request
     │
     ▼
Input Validation
     │
     ├────────────► CSRF Token Validation
     │
     ▼
Authentication / Session Validation
     │
     ▼
PHP Application Logic
     │
     ▼
PDO Prepared Statement
     │
     ▼
MySQL Database
     │
     ▼
Output Escaping
     │
     ▼
Browser
```

---

## 📁 Project Structure

```text
fixerupper-secure-ecommerce/
│
├── index.php
├── products.php
├── register.php
├── login.php
├── logout.php
├── account.php
├── cart.php
├── checkout.php
├── confirm_order.php
│
├── config.php
├── database.sql
├── .env.example
├── .gitignore
├── README.md
│
└── includes/
    ├── header.php
    └── footer.php
```

---

## ⚙️ Database Configuration

Database configuration can be supplied using environment variables:

```env
DB_HOST=127.0.0.1
DB_PORT=3307
DB_NAME=fixerupper_db
DB_USER=root
DB_PASS=
```

See `.env.example` for the expected configuration.

Do not commit production database credentials or `.env` files to the repository.

---

## 🗄️ Database Setup

1. Start Apache and MySQL.

2. Create a MySQL database:

```sql
CREATE DATABASE fixerupper_db;
```

3. Import:

```text
database.sql
```

4. Configure the database connection using the environment variables described above.

---

## ▶️ Running Locally

Clone the repository:

```bash
git clone https://github.com/vasile007/fixerupper-secure-ecommerce.git
```

Move the project into your XAMPP `htdocs` directory and start:

- Apache
- MySQL

Then open the application through your local Apache server.

---

## 🔑 Authentication Flow

```text
Registration
     │
     ▼
Server-Side Validation
     │
     ▼
Password Hashing
     │
     ▼
MySQL Database
     │
     ▼
Login
     │
     ▼
Password Verification
     │
     ▼
Session ID Regeneration
     │
     ▼
Authenticated Session
```

---

## 🛒 Shopping Flow

```text
Browse Products
      │
      ▼
Add to Cart
      │
      ▼
Review Cart
      │
      ▼
Authentication
      │
      ▼
Checkout
      │
      ▼
Delivery Details
      │
      ▼
Order Confirmation
      │
      ▼
Order Stored in MySQL
```

---

## 📸 Screenshots

Screenshots can be added here to demonstrate:

- Home page and product catalogue
- Shopping cart
- Registration and login
- Checkout
- Order confirmation
- Account page
- Security validation

---

## 🔒 Security Principles Demonstrated

This project demonstrates practical implementation of several secure development principles:

- Password hashing
- Secure authentication
- SQL Injection prevention
- XSS mitigation
- CSRF protection
- Secure session management
- Cookie security
- Server-side validation
- Session ID regeneration
- Separation of database configuration from application logic

---

## 🔮 Future Improvements

Potential improvements include:

- Multi-factor authentication
- Password recovery
- Role-based access control
- Enhanced audit logging
- HTTPS deployment
- Rate limiting
- Email verification
- Administrative dashboard
- Automated security testing
- Docker deployment
- CI/CD security scanning

---

## 👤 Author

**Vasile Bejan**

GitHub: **vasile007**

---

## ⚠️ Disclaimer

This application is a prototype created to demonstrate full-stack web development and secure coding practices.

It is not intended to process real financial transactions or store production customer data.
