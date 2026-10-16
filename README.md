# SOciaLink

Mini social networking web app: PHP (MVC) + MySQL + HTML/CSS/JS.

## Features
Register / login / logout (`password_hash`), profile + edit + photo, posts (CRUD, optional image),
comments (CRUD), **reactions** (Like, Love, Haha, Wow, Sad), **friends** (request, accept, decline, unfriend),
**direct messages** (inbox, unread badge, live chat), newsfeed (latest first), search (people or posts).

## Setup (XAMPP / Laragon)
1. Copy the `socialink` folder into `htdocs` (XAMPP) or `www` (Laragon).
2. Start Apache and MySQL.
3. Open phpMyAdmin > **Import** > choose `sql/social_app.sql`. This creates `socialink_db` with empty tables.
4. Check `config/database.php` (defaults: host `localhost`, user `root`, empty password).
5. Visit `http://localhost/socialink/public/` and click **Create an account**.

Requires PHP 8.0+ with `pdo_mysql` and `fileinfo` enabled.

## Structure
```
app/controllers  Auth, Feed, Post, Comment, Reaction, Friend, Message, Profile, Search
app/models       User, Post, Comment, Reaction, Friend, Message  (PDO prepared statements only)
app/views        layout/, partials/, auth/, feed/, post/, comment/, profile/, friends/, messages/, search/
app/core         Controller, Model, Database, helpers
public           index.php (router), assets/, uploads/
config           config.php, database.php
sql              social_app.sql  (schema only, no data)
```

## Security
Prepared statements everywhere, `htmlspecialchars` on all output, CSRF token on every POST,
session-based auth with `session_regenerate_id`, owner checks on edit/delete, image uploads validated
by MIME type and size and stored with random names (PHP execution disabled in `public/uploads`).
