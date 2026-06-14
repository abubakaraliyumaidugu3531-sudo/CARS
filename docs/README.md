# Course Advisory & Recommendation System

## 📌 Description
This is a web-based system designed to assist students in selecting appropriate courses based on their academic records, prerequisites, and program requirements.

## 🚀 Tech Stack
- Frontend: HTML, Tailwind CSS, JavaScript
- Backend: PHP
- Database: MySQL

## 📁 Project Structure
- frontend/ → UI files
- backend/ → PHP logic
- database/ → SQL schema
- assets/ → images/icons
- docs/ → documentation

## ⚙️ Setup Instructions

### 1. Install Requirements
- XAMPP / WAMP
- PHP 8+
- MySQL

### 2. Clone Repository
git clone <your-repo-url>

### 3. Move Project
Place project inside:
htdocs/ (XAMPP)

### 4. Setup Database
- Open phpMyAdmin (you do NOT need to create the database first)
- Click Import, choose `database/schema.sql`, then Go
  - This single file creates the `course_advisory_system` database, all tables,
    and demo data. It is safe to re-run (it resets the data).
- Demo logins (password `password123`): `admin@cars.test`, `advisor@cars.test`, `jane@cars.test`

### 5. Run Project
Start Apache & MySQL

Open browser:
http://localhost/course-advisory-system/

## 👨‍💻 Author
Your Name