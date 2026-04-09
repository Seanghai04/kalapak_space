<div align="center">

<img src="https://res.cloudinary.com/di1hdlb8k/image/upload/v1775702762/Logo_vmyexl.png" alt="Kalapak Logo" width="120" />

# Kalapak Code Team Website

**A modern full-stack team portfolio & CMS platform**

[![Vue.js](https://img.shields.io/badge/Vue.js-3.4-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org/)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)

**"ជីវិតដែលរស់នៅដើម្បីអ្នកដទៃ ទើបជាជីវិតដែលមានតម្លៃ"**  
*"A life lived for others is the only life worth living."*

[Features](#-features) · [Tech Stack](#-tech-stack) · [Getting Started](#-getting-started) · [Architecture](#-architecture) · [API Reference](#-api-reference) · [Team](#-team)

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🌐 Public Website
- **Home** — Hero section with animated starfield & galaxy background
- **Projects** — Portfolio showcase with tag filtering & slug-based URLs
- **Blog** — Full CMS with categories, syntax-highlighted code blocks & comments
- **Team** — Member profiles with role badges & social links
- **Join Us** — Application form with status tracking
- **Contact** — Message submission with admin inbox

</td>
<td width="50%">

### 🛡️ Admin Dashboard
- **Analytics** — Real-time dashboard with Chart.js statistics
- **Content Management** — Full CRUD for projects, blog posts & categories
- **Rich Text Editor** — Tiptap editor with image upload, video embed & code blocks
- **Media Library** — Upload, browse & manage media files
- **User Management** — Role-based access control (Admin / Member)
- **Activity Logs** — Complete audit trail of all admin actions

</td>
</tr>
<tr>
<td>

### 🎨 Design & UX
- **Dark / Light Mode** — Smooth theme toggle with persistence
- **Space Theme** — Animated starfield, galaxy effects & glassmorphism
- **Fully Responsive** — Mobile-first design across all pages
- **Animations** — AOS scroll animations, GSAP transitions & custom keyframes
- **Toast Notifications** — Store-driven auto-dismiss notification system

</td>
<td>

### 🔐 Authentication & Security
- **Sanctum Token Auth** — Secure SPA authentication
- **Role-Based Access** — Admin & Member route guards
- **Password Reset** — Complete forgot/reset password flow
- **Profile Management** — Avatar upload & password change
- **Real-Time Notifications** — 30-second polling for unread count

</td>
</tr>
</table>

---

## 🛠 Tech Stack

### Frontend

| Technology | Version | Purpose |
|:-----------|:--------|:--------|
| [Vue.js](https://vuejs.org/) | 3.4 | Reactive UI framework (Composition API) |
| [Vite](https://vitejs.dev/) | 5.x | Lightning-fast build tool & dev server |
| [Tailwind CSS](https://tailwindcss.com/) | 3.4 | Utility-first CSS framework |
| [Pinia](https://pinia.vuejs.org/) | 2.x | State management (auth, theme, notifications, UI) |
| [Vue Router](https://router.vuejs.org/) | 4.x | Client-side routing with guards |
| [Tiptap](https://tiptap.dev/) | 3.22 | Rich text editor (20+ extensions) |
| [Chart.js](https://www.chartjs.org/) | 4.x | Dashboard analytics charts |
| [GSAP](https://gsap.com/) | 3.12 | High-performance animations |
| [AOS](https://michalsnik.github.io/aos/) | 2.3 | Scroll reveal animations |
| [Axios](https://axios-http.com/) | 1.6 | HTTP client with interceptors |

### Backend

| Technology | Version | Purpose |
|:-----------|:--------|:--------|
| [Laravel](https://laravel.com/) | 11.x | PHP API framework |
| [PHP](https://www.php.net/) | 8.3 | Server-side runtime |
| [Sanctum](https://laravel.com/docs/sanctum) | 4.x | Token-based SPA authentication |
| [PostgreSQL](https://www.postgresql.org/) | 16 | Relational database |
| [Eloquent ORM](https://laravel.com/docs/eloquent) | — | Database abstraction (13 models) |

### DevOps

| Technology | Purpose |
|:-----------|:--------|
| [Docker](https://www.docker.com/) | Containerized development & deployment |
| [Docker Compose](https://docs.docker.com/compose/) | Multi-container orchestration (4 services) |
| [pgAdmin 4](https://www.pgadmin.org/) | Database GUI management |

---

## 🚀 Getting Started

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- [Git](https://git-scm.com/)

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/your-username/kalapak-website.git
cd kalapak-website
```

**2. Configure environment**

```bash
cp backend/.env.example backend/.env
```

**3. Start all services**

```bash
docker-compose up -d
```

**4. Initialize the backend**

```bash
docker exec -it kalapak_backend bash

# Inside the container:
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
exit
```

**5. Access the application**

| Service | URL | Credentials |
|:--------|:----|:------------|
| 🌐 Frontend | [`localhost:3000`](http://localhost:3000) | — |
| ⚡ Backend API | [`localhost:8000/api`](http://localhost:8000/api) | — |
| 🗄️ pgAdmin | [`localhost:5050`](http://localhost:5050) | `admin@kalapak.dev` / `admin123` |
| 🔑 Admin Panel | [`localhost:3000/auth/login`](http://localhost:3000/auth/login) | `admin@kalapak.dev` / `password` |

### Useful Commands

```bash
# Stop all services
docker-compose down

# Rebuild after dependency changes
docker-compose build --no-cache frontend
docker-compose up -d -V frontend

# View logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Run artisan commands
docker exec -it kalapak_backend php artisan migrate:fresh --seed

# Access database shell
docker exec -it kalapak_postgres psql -U kalapak_user -d kalapak_db
```

---

## 🏗 Architecture

### Container Architecture

```
┌──────────────────────────────────────────────────────────────┐
│                    Docker Compose Network                     │
│                     (kalapak_network)                         │
│                                                              │
│  ┌─────────────┐  ┌──────────────┐  ┌─────────────────────┐ │
│  │   Frontend   │  │   Backend    │  │     PostgreSQL       │ │
│  │  Vue 3 +     │  │  Laravel 11  │  │     16-alpine        │ │
│  │  Vite        │──│  PHP 8.3 FPM │──│                     │ │
│  │  :3000       │  │  :8000       │  │  :5432              │ │
│  │  Node 20     │  │  Sanctum     │  │  kalapak_db         │ │
│  └─────────────┘  └──────────────┘  └─────────────────────┘ │
│                                       │                      │
│                    ┌──────────────┐    │                      │
│                    │   pgAdmin 4  │────┘                      │
│                    │   :5050      │                           │
│                    └──────────────┘                           │
└──────────────────────────────────────────────────────────────┘
```

### Project Structure

```
kalapak-website/
├── docker-compose.yml          # Container orchestration
│
├── frontend/                   # Vue 3 SPA
│   ├── src/
│   │   ├── assets/             # Styles, fonts, images
│   │   ├── components/         # 12 reusable Vue components
│   │   │   ├── common/         # ContentEditor, Pagination, Toast...
│   │   │   ├── layout/         # AppNavbar, AppFooter
│   │   │   └── admin/          # AdminSearchModal
│   │   ├── layouts/            # Public, Auth, Admin layouts
│   │   ├── router/             # 30 routes with auth guards
│   │   ├── services/           # Axios API layer (4 namespaces)
│   │   ├── stores/             # Pinia stores (auth, theme, UI, notifications)
│   │   └── views/              # Page components
│   │       ├── public/         # 8 public pages
│   │       ├── auth/           # Login, Register, Password Reset
│   │       ├── member/         # Profile, Settings
│   │       └── admin/          # 15 admin pages
│   ├── Dockerfile
│   └── package.json
│
├── backend/                    # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/    # API controllers
│   │   │   ├── Middleware/      # Auth & role middleware
│   │   │   ├── Requests/       # Form validation
│   │   │   └── Resources/      # JSON API resources
│   │   ├── Models/             # 13 Eloquent models
│   │   ├── Notifications/      # Email & push notifications
│   │   └── Providers/
│   ├── database/
│   │   ├── migrations/         # 14+ database migrations
│   │   └── seeders/            # Sample data seeders
│   ├── routes/
│   │   └── api.php             # 60+ RESTful API routes
│   ├── Dockerfile
│   └── composer.json
│
└── pgadmin/
    └── servers.json            # Pre-configured DB connection
```

---

## 📡 API Reference

### Public Endpoints

```
POST   /api/auth/login              # User login
POST   /api/auth/register           # User registration
POST   /api/auth/forgot-password    # Request password reset
POST   /api/auth/reset-password     # Reset password with token

GET    /api/projects                # List all projects
GET    /api/projects/{slug}         # Get project by slug
GET    /api/blog/posts              # List blog posts
GET    /api/blog/posts/{slug}       # Get blog post by slug
GET    /api/blog/categories         # List blog categories
GET    /api/team                    # List team members
GET    /api/tags                    # List all tags
GET    /api/settings/public         # Public site settings

POST   /api/contact                 # Send contact message
POST   /api/applications            # Submit application
```

### Authenticated Endpoints

```
POST   /api/auth/logout             # Logout (revoke token)
GET    /api/auth/me                 # Current user profile

GET    /api/member/profile          # Get member profile
PUT    /api/member/profile          # Update member profile
PUT    /api/member/password         # Change password
POST   /api/member/avatar           # Upload avatar
```

### Admin Endpoints

```
GET    /api/admin/dashboard/stats       # Dashboard statistics
GET    /api/admin/dashboard/activity    # Recent activity feed

       /api/admin/users                 # Users CRUD
       /api/admin/projects              # Projects CRUD
       /api/admin/blog/posts            # Blog Posts CRUD
       /api/admin/blog/categories       # Categories CRUD
       /api/admin/team                  # Team Members CRUD
       /api/admin/tags                  # Tags CRUD
       /api/admin/roles                 # Roles CRUD

GET    /api/admin/applications          # List applications
PUT    /api/admin/applications/{id}     # Update application status

GET    /api/admin/messages              # List messages
PUT    /api/admin/messages/{id}/read    # Mark message as read

POST   /api/admin/media/upload          # Upload media file
GET    /api/admin/media                 # List media files
DELETE /api/admin/media/{id}            # Delete media file

GET    /api/admin/settings              # Get all settings
PUT    /api/admin/settings              # Update settings
GET    /api/admin/activity-logs         # View audit logs
GET    /api/admin/search                # Global admin search
```

---

## 🎨 Design System

### Color Palette

| Color | Hex | Usage |
|:------|:----|:------|
| 🟣 Violet | `#7b2fff` | Primary brand color |
| 🔵 Cyan | `#00d4ff` | Accent & highlights |
| ⚫ Deep Space | `#020024` | Dark mode background |
| 🔵 Nebula | `#2a2570` | Dark mode surfaces |

### Typography

| Font | Usage |
|:-----|:------|
| **Plus Jakarta Sans** | Body text, headings, UI |
| **Fira Code** | Code blocks, monospace |

### Theme Features

- **Glassmorphism** — Semi-transparent surfaces with backdrop blur
- **Gradient Brand** — Violet → Cyan gradient across CTAs & accents
- **Glow Effects** — Custom box-shadow utilities for neon-style highlights
- **6 Custom Animations** — `float`, `glow-pulse`, `fade-in`, `slide-up`, `slide-down`, `spin-slow`

---

## 🗃 Database Schema

```
13 tables:

users ──────────┐
roles ──────────┤
team_members    │
projects ───────┼── tags (many-to-many via project_tag)
blog_posts ─────┼── blog_categories
comments ───────┘
applications
messages
media
settings
activity_logs
```

---

## 🤝 Contributing

Contributions are welcome! Here's how to get started:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Development Guidelines

- Follow [Vue.js Style Guide](https://vuejs.org/style-guide/) for frontend code
- Follow [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices) for backend code
- Write meaningful commit messages
- Keep PRs focused and concise

---

## 📄 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

## 👥 Team

<table>
<tr>
<td align="center">
<b>Khat Vanna</b><br>
<sub>Founder & Full-Stack Developer</sub>
</td>
<td align="center">
<b>Rom Chamraeun</b><br>
<sub>Co-Founder & Full-Stack Developer</sub>
</td>
<td align="center">
<b>Phuem Norng</b><br>
<sub>Co-Founder & Full-Stack Developer</sub>
</td>
<td align="center">
<b>Pheun Seanghai</b><br>
<sub>Co-Founder & Full-Stack Developer</sub>
</td>
</tr>
</table>

---

<div align="center">

**Built with ❤️ by Kalapak Code Team**

Phnom Penh, Cambodia 🇰🇭

[![Made with Vue](https://img.shields.io/badge/Made_with-Vue.js-4FC08D?style=flat-square&logo=vuedotjs)](https://vuejs.org/)
[![Powered by Laravel](https://img.shields.io/badge/Powered_by-Laravel-FF2D20?style=flat-square&logo=laravel)](https://laravel.com/)
[![Runs on Docker](https://img.shields.io/badge/Runs_on-Docker-2496ED?style=flat-square&logo=docker)](https://www.docker.com/)

⭐ Star this repo if you find it useful!

</div>