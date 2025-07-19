# 🎓 EduSkill Backend API

<div align="center">

### 🚀 RESTful API for EduSkill Web Application
*Backend service for Bina Essa Non-Formal Educational Institution Information System*

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![JWT](https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white)](https://jwt.io)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

[🌟 Features](#-key-features) • [🚀 Quick Start](#-quick-start) • [📚 API Docs](#-api-documentation) • [🤝 Contributing](#-contributing)

---

*Developed as Final Project for Software Engineering Bachelor's Degree  
Faculty of Informatics, Telkom University*

**Author:** [Arzaq Ajradika](https://github.com/Sayiki) (1302210096)

</div>

## 🌟 Key Features

<table>
<tr>
<td width="50%">

### 👥 User Management
- **Multi-role Authentication** with JWT
- **Role-based Access Control** (Admin, Head, Participant)
- Secure registration & login system

### 🎯 Training Management
- **Full CRUD Operations** for training programs
- **Mentor & Category Management**
- **Quota & Capacity Control**

</td>
<td width="50%">

### 📋 Registration System
- **Streamlined Enrollment Process**
- **Real-time Status Updates**
- **Automated Notifications**

### 📊 Content Management
- **Dynamic Content Updates**
- **News & Announcements**
- **Gallery & Banner Management**
- **Comprehensive Reporting**

</td>
</tr>
</table>

## 🛠️ Tech Stack

| Category | Technology |
|----------|------------|
| **Framework** | ![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel) |
| **Language** | ![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue?logo=php) |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?logo=mysql) |
| **Authentication** | ![JWT](https://img.shields.io/badge/JWT-Auth-black?logo=jsonwebtokens) |
| **API Testing** | ![Postman](https://img.shields.io/badge/Postman-Testing-orange?logo=postman) |

## 🚀 Quick Start

### 📋 Prerequisites

Make sure you have the following installed:
- ![PHP](https://img.shields.io/badge/PHP-≥8.1-blue?style=flat-square&logo=php) PHP 8.1 or higher
- ![Composer](https://img.shields.io/badge/Composer-Latest-brown?style=flat-square&logo=composer) Composer
- ![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?style=flat-square&logo=mysql) MySQL Server
- ![Git](https://img.shields.io/badge/Git-Latest-red?style=flat-square&logo=git) Git

### ⚡ Installation Steps

```bash
# 1️⃣ Clone the repository
git clone https://github.com/Sayiki/EduSkill_TA.git
cd EduSkillBE

# 2️⃣ Install PHP dependencies
composer install

# 3️⃣ Set up environment file
cp .env.example .env

# 4️⃣ Generate application key
php artisan key:generate

# 5️⃣ Configure your database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=eduskill
# DB_USERNAME=root
# DB_PASSWORD=

# 6️⃣ Run migrations and seed data
php artisan migrate --seed

# 7️⃣ Start the development server
php artisan serve
```

🎉 **That's it!** Your API is now running at `http://127.0.0.1:8000`

## 📚 API Documentation

### 🔐 Authentication Endpoints

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| `POST` | `/api/login` | User authentication | 🌐 Public |
| `POST` | `/api/register` | Create participant account | 🌐 Public |

### 🎓 Training Endpoints

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| `GET` | `/api/pelatihan` | Get all training programs | 🌐 Public |
| `POST` | `/api/daftar-pelatihan/{id}` | Enroll in training | 🔒 Auth Required |

> 📖 **Complete API Documentation** is available in the Final Project document with detailed request/response examples.

## 🏗️ Project Structure

```
EduSkillBE/
├── 📁 app/
│   ├── 📁 Http/Controllers/     # API Controllers
│   ├── 📁 Models/               # Eloquent Models
│   └── 📁 Middleware/           # Custom Middleware
├── 📁 database/
│   ├── 📁 migrations/           # Database Migrations
│   └── 📁 seeders/              # Data Seeders
├── 📁 routes/
│   └── 📄 api.php               # API Routes
└── 📄 README.md                 # You are here!
```

## 🚦 Project Status

| Status | Description |
|--------|-------------|
| ✅ **Completed** | Development phase finished |
| ✅ **Tested** | All functionalities tested with Postman |
| ✅ **Documented** | Comprehensive documentation available |
| 🎓 **Academic** | Submitted as Final Project |

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### 🔄 How to Contribute

1. **Fork** this repository
2. **Create** a feature branch
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. **Commit** your changes
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. **Push** to the branch
   ```bash
   git push origin feature/AmazingFeature
   ```
5. **Open** a Pull Request

### 🐛 Found a Bug?

Please open an [issue](../../issues) with:
- Bug description
- Steps to reproduce
- Expected behavior
- Screenshots (if applicable)

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

<div align="center">

### 💝 Show your support

Give a ⭐️ if this project helped you!

**Made with ❤️ for educational purposes**

[![GitHub](https://img.shields.io/badge/GitHub-Sayiki-black?style=for-the-badge&logo=github)](https://github.com/Sayiki)

</div>
