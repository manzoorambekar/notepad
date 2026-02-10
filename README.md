# Notes App (LAMP)

A simple PHP + MariaDB notes app designed to run on a LAMP stack. Create and delete notes, browse them in a clean UI, and use the provided SQL backup to bootstrap the database.

## Features

- Create notes with title and content.
- List notes with preview text and timestamps.
- Delete notes with a confirmation prompt.
- Responsive, beginner-friendly CSS.

## Tech Stack

- Apache
- PHP
- MariaDB

## Project Structure

```
app/
	create.php
	delete.php
	edit.php
	style.css
config/
	db.php
database/
	notes_db_backup.sql
public/
	index.php
```

## Prerequisites

- Ubuntu/Debian Linux
- Apache 2
- PHP 8.x with `php-mysql`
- MariaDB 10.x

## Setup

### 1) Install Apache, PHP, and MariaDB

```bash
sudo apt update
sudo apt install apache2 mariadb-server php libapache2-mod-php php-mysql
sudo systemctl enable --now apache2 mariadb
```

### 2) Create the database and user

```sql
CREATE DATABASE notes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'notesapp_user'@'localhost' IDENTIFIED BY '000';
GRANT ALL PRIVILEGES ON notes_db.* TO 'notesapp_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3) Import the database schema and sample data

```bash
sudo mysql -u root -p notes_db < database/notes_db_backup.sql
```

### 4) Configure the database connection

Edit the credentials in [config/db.php](config/db.php) if needed.

The PHP files currently `include('db.php')`, so make sure `db.php` is reachable by them. You can either:

- Copy [config/db.php](config/db.php) to your web root as `db.php`, or
- Update the `include()` calls to `require __DIR__ . '/../config/db.php';`

### 5) Serve the app

Point Apache to a web root that contains the PHP pages. Two common options:

- Use `app/` as the web root (and place a copy of `public/index.php` there), or
- Use `public/` as the web root (and move `app/*` into `public/`)

Then open the app:

```
http://localhost/
```

## Notes

- `edit.php` is currently empty, so the edit flow is not implemented yet.
- If you change database credentials, update `config/db.php` (and any copied `db.php`).

## Troubleshooting

- Verify services: `sudo systemctl status apache2 mariadb`
- Check Apache errors: `sudo tail -f /var/log/apache2/error.log`
- Check MariaDB errors: `sudo tail -f /var/log/mysql/error.log`

## License

See [LICENSE](LICENSE).
