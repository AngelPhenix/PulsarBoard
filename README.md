# 🚀 PulsarBoard - Collaborative Kanban Board

> A modern collaborative task management application built with Laravel, featuring custom color-coded tags, real-time board sharing, and multi-user task tracking.

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![SQLite](https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://www.sqlite.org)
[![Live Demo](https://img.shields.io/badge/Demo-Live-green?style=for-the-badge)](https://quicktodolaravelproject-8lll.onrender.com/)

---

## 🌐 Live Demo
Test the application directly here: **[https://quicktodolaravelproject-8lll.onrender.com/](https://quicktodolaravelproject-8lll.onrender.com/)**

---

## 📸 Preview
![Home Page](https://raw.githubusercontent.com/AngelPhenix/PulsarBoard/refs/heads/main/gh-images/sc01.png)
![Job Listing](https://raw.githubusercontent.com/AngelPhenix/PulsarBoard/refs/heads/main/gh-images/sc02.png)
![Job Posting](https://raw.githubusercontent.com/AngelPhenix/PulsarBoard/refs/heads/main/gh-images/sc03.png)

---

## ✨ Main Features

- **Authentication System:** Secure user registration, login, and personalized session management.
- **Kanban Task Management:** Create, update, check off completed tasks, and delete items seamlessly.
- **Custom Color-Coded Tags:** Build and assign your own personalized tags to categorize tasks visually.
- **Board Collaboration:** Invite registered users to your boards via their email address so they can participate, add tasks, and manage tags collaboratively.

---

## 🛠️ Tech Stack

- **Backend:** PHP / Laravel
- **Database:** SQLite
- **Frontend / Assets:** Vite & Tailwind CSS
- **Hosting:** Render

---

## 📋 Todo / Roadmap

- [x] **Render Streamline:** Make Seeders and Migrations to automatically repopulate the database for easier single-use for recruiters.
- [x] **Dashboard Overview:** Display all user-owned and shared boards as clean, interactive cards on the home screen once logged in.
- [ ] **Streamlined Task Creation:** Enable direct task creation right from the board setup view.
- [ ] **Smart Focus:** Automatically focus the input field when writing a task for a smoother user experience.
- [ ] **Tag Visibility Toggle:** Provide an option to show or hide tags dynamically on the cards.
- [ ] **Advanced Filtering:** Implement filtering options directly on boards using specific tags defined by the board administrator.

---

## 🚀 You want to tweak things yourself and make it your own? (Local Development)

If you want to run this application locally for testing or development purposes, follow these steps:

### Prerequisites
Make sure you have the following tools installed on your machine:
* [Laravel Herd](https://herd.laravel.com/) (recommended local environment for PHP/Laravel)

### Installation Steps

You can set up the project either automatically using the provided script or manually.
I made a script to automate the process but you're free to open the .bat file and enter the commands yourself.

#### Automatic Setup (Recommended for Windows)
At the root of your project, simply run the setup script:
```bash
setup.bat
```
After everything's executed and installed, you can open Laravel Herd, "Add a Site", select the folder and it should be ready for use.
Don't forget to get into the folder and run the command 
```bash
npm run dev
```
Whenever you want to start coding/modifying files.

## 👤 Author

**Jérémy Mattausch**
- GitHub: [@AngelPhenix](https://github.com/AngelPhenix)
- LinkedIn: [Jérémy Mattausch](https://www.linkedin.com/in/jeremy-mattausch/)
- Year: 2026