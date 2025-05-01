#!/bin/bash

# Hive Naval System Setup Script
echo -e "\033[34m"
echo "   __   __  _______  __   __  ___   _______  ___      ___ "
echo "  |  | |  ||       ||  | |  ||   | |       ||   |    |   |"
echo "  |  |_|  ||    ___||  |_|  ||   | |_     _||   |    |   |"
echo "  |       ||   |___ |       ||   |   |   |  |   |    |   |"
echo "  |  | |  ||    ___||_     _||   |   |   |  |   |___ |   |"
echo "  | ||_|| ||   |___   |   |  |   |   |   |  |       ||   |"
echo "  |_|   |_||_______|  |___|  |___|   |___|  |_______||___|"
echo -e "\033[0m"

# Check for root privileges
if [ "$(id -u)" -ne 0 ]; then
  echo -e "\033[31m✘ Ahoy Captain! We need root privileges to deploy the system!\033[0m"
  exit 1
fi

# Install required packages
echo -e "\033[33m⚓ Anchors aweigh! Installing dependencies...\033[0m"
apt-get update

# Handle different MySQL server package names
if apt-cache show mysql-server &> /dev/null; then
    apt-get install -y mysql-server php php-mysql
else
    echo -e "\033[33m⚠ mysql-server not found, trying default-mysql-server...\033[0m"
    apt-get install -y default-mysql-server php php-mysql
fi

# Verify MySQL installation and install client if needed
if ! command -v mysql &> /dev/null; then
    echo -e "\033[33m⚠ MySQL client not found, installing...\033[0m"
    if apt-cache show mysql-client &> /dev/null; then
        apt-get install -y mysql-client
    elif apt-cache show default-mysql-client &> /dev/null; then
        apt-get install -y default-mysql-client
    else
        echo -e "\033[31m✘ MySQL client packages not found! Trying mariadb-server...\033[0m"
        apt-get install -y mariadb-server php php-mysql
    fi
fi

# Configure MySQL
echo -e "\033[33m🌊 Charting the database waters...\033[0m"

# Start MySQL service if not running
if ! systemctl is-active --quiet mysql; then
    systemctl start mysql || {
        echo -e "\033[33m⚠ Couldn't start MySQL normally, trying with sudo...\033[0m"
        sudo systemctl start mysql
    }
fi

# Create database and user without prompting for password
echo -e "\033[33m🔑 Attempting database setup...\033[0m"

if ! mysql -e "CREATE DATABASE IF NOT EXISTS hive_naval;" 2>/dev/null; then
    echo -e "\033[33m⚠ MySQL root access required. Trying with sudo...\033[0m"
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS hive_naval;" || {
        echo -e "\033[31m✘ Failed to create database with sudo.\033[0m"
        exit 1
    }
else
    echo -e "\033[32m✔ Database hive_naval exists or created successfully.\033[0m"
fi

if ! mysql -e "CREATE USER IF NOT EXISTS 'hive_user'@'localhost' IDENTIFIED BY 'N@vyS3cr3t!';" 2>/dev/null; then
    echo -e "\033[33m⚠ MySQL root access required. Trying with sudo...\033[0m"
    sudo mysql -e "CREATE USER IF NOT EXISTS 'hive_user'@'localhost' IDENTIFIED BY 'N@vyS3cr3t!';" || {
        echo -e "\033[31m✘ Failed to create user with sudo.\033[0m"
        exit 1
    }
else
    echo -e "\033[32m✔ User hive_user exists or created successfully.\033[0m"
fi

echo -e "\033[33m🔐 Granting privileges to hive_user...\033[0m"
if ! mysql -e "GRANT ALL PRIVILEGES ON hive_naval.* TO 'hive_user'@'localhost';" 2>/dev/null; then
    echo -e "\033[33m⚠ Trying to grant privileges with sudo...\033[0m"
    sudo mysql -e "GRANT ALL PRIVILEGES ON hive_naval.* TO 'hive_user'@'localhost';" || {
        echo -e "\033[31m✘ Failed to grant privileges with sudo.\033[0m"
        exit 1
    }
else
    echo -e "\033[32m✔ Privileges granted successfully.\033[0m"
fi

mysql -e "FLUSH PRIVILEGES;"

# Import database schema if officers.sql has changed
echo -e "\033[33m📦 Checking officers.sql for changes...\033[0m"
if [ ! -f .officers_sql.md5 ]; then
    echo -e "\033[33mℹ No previous checksum found. Importing officers.sql...\033[0m"
    import_needed=true
else
    current_md5=$(md5sum officers.sql | awk '{ print $1 }')
    saved_md5=$(cat .officers_sql.md5)
    if [ "$current_md5" != "$saved_md5" ]; then
        echo -e "\033[33mℹ officers.sql has changed. Importing updated file...\033[0m"
        import_needed=true
    else
        echo -e "\033[32m✔ officers.sql unchanged. Skipping import.\033[0m"
        import_needed=false
    fi
fi

if [ "$import_needed" = true ]; then
    if [ -f officers.sql ]; then
        # Import schema and data, suppress error if table exists
        mysql hive_naval < officers.sql 2>&1 | grep -v "ERROR 1050 (42S01)"
        if [ $? -eq 0 ]; then
            echo -e "\033[32m✔ Database schema imported successfully.\033[0m"
            md5sum officers.sql > .officers_sql.md5
        else
            echo -e "\033[31m✘ Error importing database schema.\033[0m"
        fi
    else
        echo -e "\033[31m✘ Missing officers.sql - database will be empty!\033[0m"
    fi

    # Ensure 'carl' user is present
    echo -e "\033[33m🔧 Ensuring 'carl' user is present in the database...\033[0m"
    mysql hive_naval -e "
    INSERT INTO officers (uuid, rank, name, username, email, password) VALUES
    ('cdb525f9-6a36-484e-8aeb-47c8bf097c69', 'Captain', 'Captain Frimpong Carl', 'carl', 'user4@hivenaval.local', 'ilovemywife')
    ON DUPLICATE KEY UPDATE rank=VALUES(rank), name=VALUES(name), email=VALUES(email), password=VALUES(password);
    UPDATE officers SET username='carl', rank='Captain', name='Captain Frimpong Carl', email='user4@hivenaval.local', password='ilovemywife' WHERE uuid='cdb525f9-6a36-484e-8aeb-47c8bf097c69';
    "

    # Ensure 'admin' user is present
    echo -e "\033[33m🔧 Ensuring 'admin' user is present in the database...\033[0m"
    mysql hive_naval -e "
    INSERT INTO officers (uuid, rank, name, username, email, password) VALUES
    ('0b8d6b5d-109d-11f0-8f93-d92a45da78ca', 'Admiral', 'System Admin', 'admin', 'admin@navy.mil', 'navy12345')
    ON DUPLICATE KEY UPDATE rank=VALUES(rank), name=VALUES(name), email=VALUES(email), password=VALUES(password);
    UPDATE officers SET username='admin', rank='Admiral', name='System Admin', email='admin@navy.mil', password='navy12345' WHERE uuid='0b8d6b5d-109d-11f0-8f93-d92a45da78ca';
    "
fi

# Set file permissions
echo -e "\033[33m🔒 Securing the hatches...\033[0m"
chmod 600 includes/config.php

echo -e "\033[32m"
echo "╔══════════════════════════════════════════╗"
echo "║  Hive Naval System successfully deployed  ║"
echo "╚══════════════════════════════════════════╝"
echo -e "\033[0m"

echo -e "\033[36m🚀 Launching system on http://127.0.0.1:9000\033[0m"
echo -e "\033[35mTry these secret codes after setup:"
echo "- Login with the credential of that guy that was careless with his logins: carl/ilovemywife"
echo "- Backup code: 1337"
echo "- Konami code: ↑↑↓↓←→←→BA"
echo "- Right-click admin dashboard\033[0m"
php -S 127.0.0.1:9000
