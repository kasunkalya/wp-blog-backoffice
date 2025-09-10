# WP Blog Backoffice

A Laravel + Vue 3 + Vuetify application for managing WordPress blog posts and users via API.  
Includes syncing posts from WordPress, creating/editing posts, and a responsive interface.

---

## Table of Contents

- [Features](#features)  
- [Requirements](#requirements)  
- [Installation](#installation)  
- [Configuration](#configuration)  
- [Database Setup](#database-setup)  
- [Running the Application](#running-the-application)  
- [Syncing WordPress Posts](#syncing-wordpress-posts)  
- [Folder Structure](#folder-structure)  
- [Development Notes](#development-notes)  
- [License](#license)  

---

## Features

- Fetch and sync posts from a WordPress site.  
- Create, edit, and delete posts in Laravel.  
- Priority-based ordering for posts.  
- Responsive table for mobile devices.  
- Vuetify UI components.  

---

## Requirements

- PHP >= 8.1  
- Composer  
- Node.js >= 18  
- NPM or Yarn  
- MySQL / MariaDB  
- Laravel 10  
- WordPress REST API access  

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/kasunkalya/wp-blog-backoffice.git
   cd wp-blog-backoffice


2. Install PHP dependencies:
   ```bash
   composer install

3. Install Node dependencies:
   ```bash
   npm install   


 
## Configuration

1. Copy .env.example to .env:
  ```bash
  cp .env.example .env

2. Set your database credentials in .env:
  ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=wp_blog_backoffice
    DB_USERNAME=root
    DB_PASSWORD=your_password_here

## Database Setup

1. Run migrations:
    ```bash
    php artisan migrate
2. (Optional) Seed the database:
    ```bash
    php artisan db:seed

## Running the Application

- Start the Laravel server:
    ```bash
    php artisan serve
- Start Vite (for Vue frontend):
    ```bash
    npm run dev

## Syncing WordPress Posts
- Click "Sync from WP" button in the UI.
- Posts from WordPress will be fetched and stored locally.

## Folder Structure
- app/ -> Laravel backend code
- resources/ -> Vue 3 + Vuetify frontend
- routes/ -> API and web routes
- database/ -> Migrations and seeds
- public/ -> Public assets
- composer.json -> PHP dependencies
- package.json -> Node dependencies

## Development Notes
- Use incremental Git commits.
- Follow PSR-12 coding standards for PHP.
- Vue 3 with Composition API is used for components.

