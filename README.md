
# Student Life Blog 🎓

## About
What you are seeing is a project started during an IT internship at a local firm. Simply said, it's a blog aimed at giving university students a platform to share their experiences, express their opinions, and share tips about student life. 

The focus of this repository is learning practical applications of modern web software development and project management skills. As a result, the emphasis is placed on clean architecture, security, and backend engineering behind the scenes rather than cosmetic complexity.

---

## Technical Stack
- **Framework:** Laravel 11 (PHP 8.4+)
- **Database:** MySQL 8.0
- **Web Server:** Nginx
- **Frontend:** Blade Architecture (`<x-layout.app>` components), Bootstrap 5, Vite
- **Environment:** Docker (WSL2 / Linux)

---

## Requirements
Before running the application, ensure you have the following installed on your machine:
- [Docker Engine](https://docs.docker.com/engine/install/) & Docker Compose
- [Git](https://git-scm.com/)
- *Windows Users:* WSL2 (Ubuntu recommended)

---

## How to Setup

Follow these steps to run the application locally in your Docker environment:

### 1. Clone the Repository
```bash
git clone [https://github.com/your-username/my-blog.git](https://github.com/your-username/my-blog.git)
cd my-blog
```
### 2. Environment Configuration
Copy the default environment file:

```Bash
cp .env.example .env
```
Ensure your .env file reflects the internal Docker database configuration:

Ini, TOML
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=secret

Also keep in mind the mail sanbox configuration. Personally i used Mailtrap

3. Build & Run Docker Containers
Start the custom Docker containers in detached mode:

```Bash
docker compose up -d --build
```
4. Install Dependencies & Setup Application
Run Composer installation, generate the application key, and set file permissions inside the PHP container:

```Bash
# Install PHP dependencies
docker compose exec php composer install

# Generate application encryption key
docker compose exec php php artisan key:generate

# Set folder permissions (WSL/Linux environments)
sudo chown -R $USER:$USER . && chmod -R 775 .
# (Might need full permissions in order to edit the files in vs code)
```

5. Run Database Migrations
Execute database migrations to build the database schema:

```Bash
docker compose exec php php artisan migrate
```
(Optional) Populate the database with test data:

```Bash
docker compose exec php php artisan db:seed
```
6. Access the Application
Open your browser and navigate to:
http://localhost

Useful Development Commands

Clear compiled view cache:
```Bash
docker compose exec php php artisan view:clear
```
Clear all application cache:
```Bash
docker compose exec php php artisan optimize:clear
```
Stop containers:

```Bash
docker compose down
```
Reset database (fresh migration):
```Bash
docker compose exec php php artisan migrate:fresh
```
