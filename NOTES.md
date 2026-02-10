# MariaDB & Apache Setup Guide

## MariaDB Setup

### Install
```bash
sudo apt update
sudo apt install mariadb-server
```

### Start & Enable
```bash
sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo systemctl status mariadb
```

### Secure Installation
```bash
sudo mysql_secure_installation
```

### Access MySQL
```bash
sudo mysql -u root -p
mysql -u username -p database_name
```

### Database Management
```sql
-- Show databases
SHOW DATABASES;

-- Create database
CREATE DATABASE your_db_name;
CREATE DATABASE your_db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use database
USE your_db_name;

-- Show tables
SHOW TABLES;
SHOW TABLE STATUS;

-- Drop database
DROP DATABASE your_db_name;
```

### User Management
```sql
-- Create user
CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';

-- Grant privileges
GRANT ALL PRIVILEGES ON your_db_name.* TO 'username'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON your_db_name.* TO 'username'@'localhost';

-- Show grants
SHOW GRANTS FOR 'username'@'localhost';

-- Revoke privileges
REVOKE ALL PRIVILEGES ON your_db_name.* FROM 'username'@'localhost';

-- Change password
ALTER USER 'username'@'localhost' IDENTIFIED BY 'new_password';

-- Drop user
DROP USER 'username'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;
EXIT;
```

### Table Management
```sql
-- Create table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Show table structure
DESCRIBE users;
SHOW CREATE TABLE users;

-- Add column
ALTER TABLE users ADD COLUMN phone VARCHAR(15);

-- Modify column
ALTER TABLE users MODIFY COLUMN name VARCHAR(150);

-- Drop column
ALTER TABLE users DROP COLUMN phone;

-- Rename table
RENAME TABLE users TO accounts;

-- Drop table
DROP TABLE users;
```

### Data Operations
```sql
-- Insert data
INSERT INTO users (name, email) VALUES ('John', 'john@example.com');

-- Select data
SELECT * FROM users;
SELECT id, name FROM users WHERE id = 1;

-- Update data
UPDATE users SET name = 'Jane' WHERE id = 1;

-- Delete data
DELETE FROM users WHERE id = 1;

-- Count rows
SELECT COUNT(*) FROM users;

-- Export query result
SELECT * FROM users INTO OUTFILE '/tmp/users.csv' FIELDS TERMINATED BY ',';
```

### Backup & Restore
```bash
-- Backup database
sudo mysqldump -u root -p your_db_name > backup.sql
sudo mysqldump -u root -p --all-databases > full_backup.sql

-- Restore database
sudo mysql -u root -p your_db_name < backup.sql
sudo mysql -u root -p < full_backup.sql

-- Backup with specific tables
sudo mysqldump -u root -p your_db_name table1 table2 > tables_backup.sql

-- Compress backup
sudo mysqldump -u root -p your_db_name | gzip > backup.sql.gz
```

### Import Database
```bash
sudo mysql -u root -p your_db_name < notes_db_backup.sql
```

### Troubleshooting
```bash
-- Check service status
sudo systemctl status mariadb

-- View error log
sudo tail -f /var/log/mysql/error.log

-- Restart service
sudo systemctl restart mariadb

-- Check listening ports
sudo netstat -tlnp | grep mysql
sudo ss -tlnp | grep mysql
```

### Key Paths
- Config: `/etc/mysql/mariadb.conf.d/50-server.cnf`
- Data: `/var/lib/mysql/`
- Logs: `/var/log/mysql/error.log`
- Socket: `/var/run/mysqld/mysqld.sock`

---

## Apache Setup

### Install
```bash
sudo apt install apache2
sudo apt install php libapache2-mod-php php-mysql
sudo apt install php-curl php-json php-xml php-mbstring
```

### Start & Enable
```bash
sudo systemctl start apache2
sudo systemctl enable apache2
sudo systemctl status apache2
```

### Enable Required Modules
```bash
sudo a2enmod rewrite
sudo a2enmod php
sudo a2enmod ssl
sudo a2enmod headers
sudo a2enmod proxy
sudo systemctl restart apache2
```

### Disable Modules
```bash
sudo a2dismod rewrite
sudo a2dismod php
sudo systemctl restart apache2
```

### List Enabled/Disabled Modules
```bash
apache2ctl -M
ls /etc/apache2/mods-enabled/
ls /etc/apache2/mods-available/
```

### Document Root
- Path: `/var/www/html/`
- Copy project files here or create symlink:
```bash
sudo ln -s /home/mambekar/00_Local/01_Learn/MiniProject_03 /var/www/html/project
```

### Create Custom Apache Config Per App

#### Method 1: Create Config File in sites-available
```bash
sudo nano /etc/apache2/sites-available/project1.conf
```

**Sample config:**
```apache
<VirtualHost *:80>
    ServerName project1.local
    ServerAlias www.project1.local
    DocumentRoot /var/www/project1
    
    <Directory /var/www/project1>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/project1_error.log
    CustomLog ${APACHE_LOG_DIR}/project1_access.log combined
    
    # Rewrite rules
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.php [L]
    </IfModule>
</VirtualHost>
```

#### Enable/Disable Sites
```bash
-- Enable site
sudo a2ensite project1.conf

-- Disable site
sudo a2dissite project1.conf

-- List enabled sites
ls /etc/apache2/sites-enabled/

-- Reload Apache
sudo systemctl reload apache2

-- Test config
sudo apache2ctl -t
sudo apache2ctl -S
```

#### Method 2: Create Conf.d Files
```bash
sudo nano /etc/apache2/conf-available/project1.conf
```

Then enable:
```bash
sudo a2enconf project1
sudo systemctl reload apache2
```

### Configure .htaccess
Edit: `/etc/apache2/sites-available/000-default.conf` or your custom config

Add inside `<Directory>`:
```apache
AllowOverride All
```

**Sample .htaccess:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?path=$1 [QSA,L]
</IfModule>

# Deny access to sensitive files
<FilesMatch "\.env$|\.git|\.htaccess$">
    Deny from all
</FilesMatch>

# Enable gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache control
<FilesMatch "\.(jpg|jpeg|png|gif|ico|css|js)$">
    Header set Cache-Control "max-age=2592000, public"
</FilesMatch>
```

### Apache Directory Structure
```bash
# Create project directories
sudo mkdir -p /var/www/project1
sudo mkdir -p /var/www/project2

# Set permissions
sudo chown -R www-data:www-data /var/www/project1
sudo chmod -R 755 /var/www/project1
sudo chmod -R 644 /var/www/project1/*
sudo find /var/www/project1 -type d -exec chmod 755 {} \;
```

### Virtual Hosts with Local Domain
Add to `/etc/hosts`:
```
127.0.0.1 project1.local
127.0.0.1 project2.local
```

### Key Paths
- Config: `/etc/apache2/apache2.conf`
- Sites: `/etc/apache2/sites-available/`
- Sites Enabled: `/etc/apache2/sites-enabled/`
- Conf: `/etc/apache2/conf-available/`
- Modules: `/etc/apache2/mods-available/`
- Logs: `/var/log/apache2/`
- Document Root: `/var/www/html/`

### Verify & Troubleshoot
```bash
-- Test configuration
sudo apache2ctl -t
sudo apache2ctl -S

-- Check Apache version
apache2ctl -v

-- View enabled modules
apache2ctl -M

-- Check PHP version
php -v

-- View error log
sudo tail -f /var/log/apache2/error.log

-- View access log
sudo tail -f /var/log/apache2/access.log

-- Restart Apache
sudo systemctl restart apache2
sudo systemctl reload apache2

-- Check listening ports
sudo netstat -tlnp | grep apache2
sudo ss -tlnp | grep apache2
```

---

## PHP Configuration & Commands

### PHP Configuration
```bash
-- Find PHP config file
php -i | grep "Loaded Configuration File"

-- Edit php.ini
sudo nano /etc/php/8.1/apache2/php.ini

-- Common settings to adjust
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
display_errors = On (development only)
```

### Restart PHP
```bash
sudo systemctl restart apache2
```

### Useful PHP Commands
```bash
-- Check installed PHP version
php -v

-- Check installed extensions
php -m

-- Test PHP syntax
php -l filename.php

-- Run PHP script from CLI
php script.php

-- Check specific ini setting
php -i | grep extension_dir
php -i | grep "Loaded Configuration"
```

### PHP Development Utilities
```bash
-- Install Composer (PHP package manager)
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
composer --version

-- Create new Composer project
composer create-project project/name
composer install
composer update
```

---

## Useful System & Web Development Commands

### File & Directory Permissions
```bash
-- Change ownership
sudo chown -R user:group /path

-- Change permissions
sudo chmod -R 755 /path
sudo chmod -R 644 /path/*

-- Change recursively
sudo find /path -type f -exec chmod 644 {} \;
sudo find /path -type d -exec chmod 755 {} \;

-- View permissions
ls -la /path
```

### Process Management
```bash
-- Check running processes
ps aux | grep apache2
ps aux | grep mysql
ps aux | grep php

-- Kill process
sudo kill -9 PID
sudo pkill -f process_name

-- Monitor resource usage
top
htop (install if needed: sudo apt install htop)
```

### Network Commands
```bash
-- Check open ports
sudo netstat -tlnp
sudo ss -tlnp
sudo lsof -i :80
sudo lsof -i :3306

-- DNS lookup
nslookup example.com
dig example.com

-- Check connectivity
ping example.com
curl http://localhost
curl -I http://localhost (headers only)
```

### Log Monitoring
```bash
-- View recent logs
tail -n 50 /var/log/apache2/error.log
tail -f /var/log/apache2/error.log (follow mode)

-- Search logs
grep "error" /var/log/apache2/error.log
grep -i "warning" /var/log/apache2/access.log

-- Log statistics
wc -l /var/log/apache2/access.log
```

### Git & Version Control
```bash
-- Initialize repository
git init
git clone <repo-url>

-- Basic workflow
git add .
git commit -m "message"
git push origin main
git pull

-- Check status
git status
git log
```

### System Maintenance
```bash
-- Update packages
sudo apt update
sudo apt upgrade
sudo apt autoremove

-- Check disk space
df -h
du -sh /path

-- Cleanup
sudo apt clean
sudo apt autoclean
sudo journalctl --vacuum=time:7d

-- Systemctl commands
sudo systemctl list-units --type service
sudo systemctl list-unit-files
```

### Database Utilities
```bash
-- mysqldump (backup)
mysqldump -u user -p database > backup.sql
mysqldump -u user -p --all-databases > full_backup.sql

-- mysqladmin
mysqladmin -u root -p status
mysqladmin -u root -p processlist
mysqladmin -u root -p flush-logs
```

### File Operations
```bash
-- Search files
find /path -name "*.php"
find /path -type f -name "*.log"
grep -r "text" /path

-- Compress/Extract
tar -czf archive.tar.gz /path
tar -xzf archive.tar.gz
zip -r archive.zip /path
unzip archive.zip

-- Copy
cp -r /source /destination
scp file user@server:/path
```

### Text Editors
```bash
-- Nano
nano filename

-- Vi/Vim
vim filename

-- View file
cat filename
less filename
head -n 20 filename
tail -n 20 filename
```

### Security
```bash
-- Check file permissions
stat /path/file
getfacl /path/file

-- Set umask
umask 022

-- Generate SSH key
ssh-keygen -t rsa -b 4096

-- Test SSL certificate
openssl s_client -connect example.com:443
```

---

## Quick Test
1. Create test file: `/var/www/html/test.php`
```php
<?php phpinfo(); ?>
```
2. Visit: `http://localhost/test.php`

---

## Troubleshooting Quick Reference

| Issue | Command |
|-------|---------|
| Apache won't start | `sudo apache2ctl -t` then check `/var/log/apache2/error.log` |
| MySQL won't connect | `sudo systemctl restart mariadb` & check socket at `/var/run/mysqld/mysqld.sock` |
| Permission denied errors | `sudo chown -R www-data:www-data /var/www` |
| Port already in use | `sudo lsof -i :80` or `sudo ss -tlnp \| grep 80` |
| PHP not executing | Check `AllowOverride All` in Apache config & `.htaccess` |
| Database connection fails in PHP | Verify user, password, host in PHP connection string |
| Large file upload fails | Increase `upload_max_filesize` & `post_max_size` in php.ini |



## Linux Admin Command Practice Checklist (Debian)

### 1) Shell & Help
```bash
whoami
hostname
hostnamectl
date
timedatectl
uname -a
uptime
man <command>
apropos <keyword>
help
history
which <command>
whereis <command>
type <command>
```

### 2) Filesystem & Permissions
```bash
pwd
ls -lah
cd /path
tree /path
find /path -type f -name "*.log"
locate <filename>
du -sh /path
df -h
lsblk
blkid
mount
mount /dev/sdXn /mnt
umount /mnt
fdisk -l
parted -l
mkfs.ext4 /dev/sdXn
fsck /dev/sdXn
ln -s /source /target
chmod 755 /path
chown user:group /path
chgrp group /path
umask 022
getfacl /path
setfacl -m u:user:rwx /path
```

### 3) Text Processing
```bash
cat file
less file
head -n 50 file
tail -n 50 file
tail -f /var/log/syslog
wc -l file
sort file
uniq -c file
cut -d":" -f1 /etc/passwd
paste file1 file2
awk '{print $1}' file
sed -n '1,10p' file
grep -R "error" /var/log
xargs -0 < /path
```

### 4) Archiving & Backup
```bash
tar -czf archive.tar.gz /path
tar -xzf archive.tar.gz
gzip file
gunzip file.gz
zip -r archive.zip /path
unzip archive.zip
rsync -avh /source /dest
rsync -avh --delete /source /dest
dd if=/dev/sdX of=/path/backup.img bs=4M status=progress
```

### 5) Package Management (Debian)
```bash
sudo apt update
sudo apt upgrade
sudo apt install <pkg>
sudo apt remove <pkg>
sudo apt purge <pkg>
sudo apt autoremove
sudo apt clean
apt-cache search <keyword>
apt-cache show <pkg>
dpkg -l | grep <pkg>
sudo dpkg -i package.deb
sudo dpkg -r <pkg>
sudo apt-mark hold <pkg>
sudo apt-mark unhold <pkg>
```

### 6) Services & Logs
```bash
sudo systemctl status <service>
sudo systemctl start <service>
sudo systemctl stop <service>
sudo systemctl restart <service>
sudo systemctl reload <service>
sudo systemctl enable <service>
sudo systemctl disable <service>
sudo systemctl is-enabled <service>
systemctl list-units --type service
journalctl -u <service>
journalctl -xe
sudo journalctl --vacuum-time=7d
```

### 7) Processes & Performance
```bash
ps aux
top
htop
pgrep <name>
pkill <name>
kill -9 <pid>
nice -n 10 <command>
renice 10 -p <pid>
vmstat 1
iostat -x 1
```

### 8) Users, Groups & Sudo
```bash
id
groups
who
w
last
sudo adduser <user>
sudo useradd -m <user>
sudo usermod -aG <group> <user>
sudo userdel -r <user>
sudo groupadd <group>
sudo groupmod -n <new> <old>
sudo groupdel <group>
sudo gpasswd -a <user> <group>
sudo passwd <user>
sudo chage -l <user>
sudo chage -M 90 <user>
sudo visudo
```

### 9) Networking Basics
```bash
ip a
ip link
ip route
ss -tlnp
netstat -tlnp
lsof -i
ping -c 4 <host>
traceroute <host>
mtr <host>
curl -I http://localhost
wget <url>
dig example.com
nslookup example.com
host example.com
resolvectl status
```

### 10) SSH
```bash
ssh user@host
ssh -i ~/.ssh/id_rsa user@host
ssh-keygen -t ed25519 -C "email@example.com"
ssh-copy-id user@host
scp file user@host:/path
sftp user@host
ssh-agent -s
ssh-add ~/.ssh/id_ed25519
sudo systemctl status ssh
sudo ss -tlnp | grep :22
```

### 11) Firewall (UFW + iptables/nft)
```bash
sudo ufw status verbose
sudo ufw enable
sudo ufw disable
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw delete allow 80/tcp
sudo iptables -L -n -v
sudo nft list ruleset
```

### 12) Apache2
```bash
sudo apt install apache2
sudo systemctl status apache2
sudo systemctl restart apache2
sudo systemctl reload apache2
sudo a2enmod rewrite
sudo a2dismod rewrite
sudo a2ensite <site>.conf
sudo a2dissite <site>.conf
sudo apache2ctl -t
sudo apache2ctl -S
apache2ctl -M
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log
ss -tlnp | grep -E ":80|:443"
```

### 13) MariaDB
```bash
sudo apt install mariadb-server
sudo systemctl status mariadb
sudo mysql_secure_installation
sudo mysql -u root -p
mysql -u <user> -p <db>
mysqldump -u root -p <db> > backup.sql
mysqladmin -u root -p status
mysqladmin -u root -p processlist
sudo tail -f /var/log/mysql/error.log
ss -tlnp | grep 3306
```

### 14) NFS
```bash
sudo apt install nfs-kernel-server nfs-common
sudo systemctl status nfs-server
sudo exportfs -ra
sudo exportfs -v
showmount -e <server>
sudo mount -t nfs <server>:/export /mnt
sudo umount /mnt
rpcinfo -p <server>
```

### 15) FTP (vsftpd)
```bash
sudo apt install vsftpd
sudo systemctl status vsftpd
sudo systemctl restart vsftpd
sudo tail -f /var/log/vsftpd.log
ftp <server>
lftp <server>
ss -tlnp | grep :21
```

### 16) DNS (Resolver + Bind9)
```bash
resolvectl status
dig @<dns-server> example.com
nslookup example.com
host example.com
sudo apt install bind9
sudo systemctl status bind9
sudo named-checkconf
sudo named-checkzone example.com /etc/bind/db.example.com
rndc status
```

### 17) Cron & Automation
```bash
crontab -l
crontab -e
sudo systemctl status cron
sudo journalctl -u cron
at now + 1 minute
```

### 18) Security & Auditing
```bash
sudo fail2ban-client status
sudo tail -f /var/log/auth.log
sudo lastb
sudo passwd -S <user>
sudo apt install apparmor-utils
sudo aa-status
```