# Inventory Manager (PHP + MySQL + Bootstrap)

A small inventory management system built with plain PHP (PDO), MySQL, and Bootstrap 5.

## Features

- User **register / login / logout** with hashed passwords (`password_hash`) and session auth
- **Dashboard** with product listing, search, and summary stats (distinct products, total units, inventory value)
- **Add / edit / delete** products (SKU, name, description, price, quantity) with unique-SKU validation
- **MySQL** storage via PDO prepared statements (SQL-injection safe) and CSRF-protected forms
- **Bootstrap 5** responsive UI

## Tech stack

| Layer    | Choice                          |
|----------|---------------------------------|
| Backend  | PHP 8.2 (no framework, PDO)     |
| Database | MySQL 8.0                       |
| Frontend | Bootstrap 5 (CDN)               |
| Runtime  | Apache (`php:8.2-apache` image) |

## Project structure

```
public/            # web root (point your server here)
  index.php        # redirects to dashboard or login
  login.php register.php logout.php
  dashboard.php    # product listing + stats
  product_form.php # add & edit
  product_delete.php
  assets/css/app.css
src/
  config.php       # env-based config (+ .env fallback)
  db.php           # PDO connection
  auth.php         # auth, CSRF, helpers
  layout.php       # shared header/footer + flash messages
sql/
  schema.sql       # tables
  seed.sql         # optional sample products
Dockerfile
docker-compose.yml
```

## Run locally with Docker (recommended)

```bash
docker compose up --build
```

Then open <http://localhost:8080>. The MySQL container auto-loads `sql/schema.sql`
and `sql/seed.sql` on first start. Register a user, then manage products.

## Run on a traditional PHP host (no Docker)

1. Create a MySQL database and import `sql/schema.sql` (and optionally `sql/seed.sql`).
2. Set the document root to the `public/` directory.
3. Provide DB credentials via environment variables, or copy `.env.example` to `.env`
   in the project root and fill it in:

   ```
   DB_HOST=...   DB_PORT=3306   DB_NAME=...   DB_USER=...   DB_PASS=...
   ```

Requires PHP 8.0+ with the `pdo_mysql` extension.

## Deployment notes

This is a server-rendered PHP + MySQL app, so it needs a host with a PHP runtime
and a MySQL server. **Netlify cannot run it** — Netlify only serves static files
and JS/Go serverless functions. Use a PHP-capable host instead, for example:

- **Railway / Render** (Docker deploy of this repo + a managed MySQL plugin)
- **InfinityFree / 000webhost** (upload `public/` + `src/`, import the SQL, set env vars)
- Any VPS / shared host with PHP 8 and MySQL

The included `Dockerfile` makes container-based hosts (Railway, Render, Fly.io,
etc.) straightforward — point the platform at this repo and add a MySQL service.

## Security

- Passwords hashed with `password_hash()` / verified with `password_verify()`
- All queries use PDO prepared statements
- CSRF tokens on every state-changing form
- Output escaped via `htmlspecialchars`
