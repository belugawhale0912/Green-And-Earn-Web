<p align="center">
  <a href="assets/Page%20Title.png">
    <img src="assets/Page%20Title.png" alt="Green&Earn Logo" width="200">
  </a>
</p>

# 🌱 Green&Earn

*A gamified eco-quest platform where members, partners, moderators, and administrators turn sustainable actions into real rewards.*

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-Semantic-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-Vanilla-1572B6?style=for-the-badge&logo=css3&logoColor=white)

---

## 📖 Overview

**Green&Earn** is a full-stack web platform that turns eco-friendly actions into a rewarding adventure. By bridging the gap between sustainable choices and real-world benefits, it empowers individuals and communities to make a collective impact on the planet while earning rewards from their favorite local and global partners.

The platform includes role-based experiences for **members**, **partners**, **moderators**, and **admins**, covering the full loop from completing eco-tasks and submitting proof to verification and reward redemption.

---

## Features

| Area | Description |
|------|-------------|
| 🎮 **Eco-Task Quests** | Browse a curated list of sustainable tasks, from recycling to tree planting, and start your journey |
| 📸 **Proof-of-Impact** | Submit photos or documentation as proof of completed tasks to earn points |
| 🎁 **Reward Marketplace** | Redeem points for exclusive rewards from partners like BlaBla Mart, Riot Games, and Steam |
| 👥 **Social & Leaderboard** | Add friends, track their progress, and climb the Global Leaderboard |
| 🏅 **Tiered Achievements** | Unlock unique badges as you reach sustainability milestones |
| 🛒 **Point Awarding** | Partners award points directly to customers for eco-conscious purchases |
| 🏷️ **Reward Management** | Partners create and manage custom rewards to drive brand loyalty and environmental awareness |
| 📊 **Insight Dashboard** | Partners track how many eco-warriors are engaging with their brand |
| ✅ **Verification System** | Moderators review and approve member submissions to keep the platform honest |
| 🛠️ **Activity Oversight** | Create new challenges and manage the global task catalog |
| 👤 **User Management** | Admin controls for maintaining a healthy and active community |

---

## 🛠️ Tech Stack

- **Frontend:** Semantic HTML5, CSS3 (Vanilla), and JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** XAMPP / WAMP environments

---

## Getting Started

### Prerequisites

- A web server stack such as XAMPP or WAMP, supporting PHP `8.x` and MySQL
- A recent version of Chrome, Firefox, or Edge

### Installation

```bash
# Clone the repository into your server's web root (e.g. htdocs for XAMPP)
git clone https://github.com/belugawhale0912/Green-And-Earn-Web.git
cd Green-And-Earn-Web
```

Then start Apache and MySQL from your XAMPP/WAMP control panel.

### Database Setup

1. Open your database management tool (e.g. phpMyAdmin).
2. Create a new database named `green_and_earn`.
3. Import the `green_and_earn.sql` file from the project root to recreate the tables and seed data.

### Configuration

Make sure the database connection settings in the PHP files match your local environment (host, username, password, and database name).

### Run the App

Open the landing page in your browser:

```
http://localhost/Green-And-Earn-Web/php/pages/Landingpage.php
```

> If you rename the project folder, update the URL to match.

---

## 📁 Project Structure

```
Green-And-Earn-Web/
├── assets/                # Images and static assets
├── css/                   # Stylesheets
├── js/                    # Client-side scripts
├── lib/                   # Shared libraries
├── php/                   # Server-side logic and pages
├── uploads/               # User-submitted proof and media
├── green_and_earn.sql     # Database schema and seed script
├── userAccounts.txt       # Demo account details
├── favicon.ico            # Site icon
└── README.md
```

---

## 📸 Project Preview

- **Landing Page:** A vibrant introduction to your eco-journey
- **Member Dashboard:** Your central hub for tasks, points, and tracking
- **Leaderboards:** Real-time rankings of the top contributors to our planet

---

## ⚠️ Notes

- This project is intended for academic and demonstration purposes (APU Diploma Sem 4).
- Demo credentials should not be reused in a production environment.
- The `green_and_earn` database must be imported before login and rewards features will work locally.

---

## 🙏 Acknowledgements

- XAMPP / WAMP for the local PHP and MySQL environment
- Our partners' reward ideas, which inspired the marketplace
- Everyone who helped build and refine Green&Earn

---

Made with 💚 for APU Diploma Sem 4 · 2026
