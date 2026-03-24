# 🛒 E-Commerce Platform (Laravel + Filament)

A full-featured E-Commerce platform built with **Laravel**, designed with scalability, performance, and modern architecture in mind.
The project includes a powerful admin dashboard using **Filament**, advanced order management, real-time tracking, and AI-powered features.

---

## 🚀 Features

### 👤 Authentication & Users

* User Registration & Login
* Social Login using **Laravel Socialite** (Google / Facebook)
* Role & Permission system (Admin / Users / Managers)
* Secure authentication using Laravel best practices

---

### 🧑‍💼 Admin Dashboard (Filament)

* Built with **Filament Admin Panel**
* Manage:

  * Products
  * Categories
  * Orders
  * Users
* Multi-role access control داخل الـ dashboard
* Clean and responsive UI

---

### 🛍️ Products & Variants

* Product management system
* Support for:

  * Product Variants (size, color, etc.)
  * Pricing & discounts
* Organized product structure

---

### 🛒 Cart & Checkout

* Add to cart (Guest & Auth users)
* Smart cart handling (session + user merge)
* Checkout flow ready for payment integration

---

### 📦 Orders System

* Advanced Order Management System
* Order statuses:

  * Pending
  * Confirmed
  * Shipped
  * Delivered
  * Cancelled
* Database Transactions لضمان سلامة البيانات
* Automatic creation of sales records

---

### 📍 Order Tracking

* Real-time order tracking system
* Supports live updates (can be integrated with WebSockets / Pusher)
* Track order lifecycle بسهولة

---

### 🤖 AI Integration

* Integrated with **Laravel AI SDK**
* AI Agent capable of:

  * Tracking orders
  * Searching products
  * Fetching product variants
* Uses tool-based architecture for dynamic responses

---

### ⚙️ Technical Highlights

* Built with Laravel (MVC Architecture)
* Clean code structure with Services & Jobs
* Queue system for background processing
* API-ready architecture
* Optimized database queries using Eloquent

---

## 🛠️ Tech Stack

* **Backend:** Laravel
* **Admin Panel:** Filament
* **Authentication:** Laravel Breeze + Socialite
* **Database:** MySQL
* **AI Integration:** Laravel AI SDK
* **Queue System:** Laravel Queues
* **Realtime (optional):** Pusher / WebSockets

---

## 📂 Project Structure Highlights

* `app/Models` → Business logic models
* `app/Services` → Custom services (AI, Orders, etc.)
* `app/Jobs` → Background jobs
* `app/Filament` → Dashboard resources
* `database/migrations` → Database structure

---

## 🔒 Security

* CSRF Protection
* Authentication Guards
* Role-based authorization
* Secure session handling

---

## 📈 Future Improvements

* Payment Gateway Integration (Paymob / Stripe)
* Notifications system (Email / Firebase)
* Advanced analytics dashboard
* Multi-language support

---

## 🧑‍💻 Author

Developed by **Ahmed Abdelhamid**
Full Stack Laravel Developer 🚀

---

## 📄 License

This project is open-source and available under the MIT License.

---
