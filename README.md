# 🏥 Hospital Management System

A complete Hospital Management System built with **Laravel**, **PHP**, and **MySQL**, designed to manage hospital operations through different user roles and dedicated dashboards.

![Laravel](https://img.shields.io/badge/Laravel-13-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap)

---

## 📌 About

Hospital Management System is a web-based application built with Laravel to help manage hospital operations and organize interactions between different hospital departments.

The system provides separate roles and dashboards for:

- 👨‍💼 Admin
- 👨‍⚕️ Doctor
- 🧑‍🤝‍🧑 Patient
- 🩻 X-Ray Staff
- 🧪 Laboratory Staff

---

## 🚀 Features

### 👨‍💼 Admin

- Manage doctors
- Manage patients
- Manage X-Ray staff
- Manage laboratory staff
- Manage hospital departments
- Manage appointments
- Manage invoices
- Manage medical services
- Manage users and permissions

### 👨‍⚕️ Doctor

- Doctor Dashboard
- View appointments
- Manage patient cases
- Add medical diagnoses
- Add prescriptions
- Manage medical records
- Request X-Ray
- Request laboratory tests

### 🧑‍🤝‍🧑 Patient

- Patient Dashboard
- View appointments
- View medical records
- View prescriptions
- View invoices
- View laboratory results
- View X-Ray results

### 🩻 X-Ray

- X-Ray Dashboard
- View requested X-Rays
- Add X-Ray reports
- Manage X-Ray results

### 🧪 Laboratory

- Laboratory Dashboard
- View requested tests
- Add laboratory results
- Manage laboratory reports

---

## 🛠️ Built With

- Laravel
- PHP
- MySQL
- Blade
- Bootstrap
- JavaScript
- jQuery
- Eloquent ORM
- Laravel Authentication
- Laravel Localization

---

## 🔐 User Roles

| Role | Description |
|------|-------------|
| Admin | Manage the hospital system |
| Doctor | Manage patients and medical records |
| Patient | View appointments and medical information |
| X-Ray | Manage X-Ray requests and results |
| Laboratory | Manage laboratory requests and results |

---

## 📂 Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
└── ...

database/
├── migrations/
└── seeders/

resources/
├── views/
├── css/
└── js/

routes/
├── web.php
└── ...

public/
├── Dashboard/
└── ...

storage/
