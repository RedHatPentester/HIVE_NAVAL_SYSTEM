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

# Import database schema if tables do not exist
echo -e "\033[33m📦 Checking database schema...\033[0m"
table_exists=$(mysql -D hive_naval -e "SHOW TABLES LIKE 'officers';" | grep officers)
if [ "$table_exists" == "officers" ]; then
    echo -e "\033[32m✔ Database schema already exists. Skipping import.\033[0m"
else
    echo -e "\033[33m📦 Importing database schema...\033[0m"
    if [ -f officers.sql ]; then
        mysql hive_naval < officers.sql
        if [ $? -eq 0 ]; then
            echo -e "\033[32m✔ Database schema imported successfully.\033[0m"
        else
            echo -e "\033[31m✘ Error importing database schema.\033[0m"
        fi
    else
        echo -e "\033[31m✘ Missing officers.sql - database will be empty!\033[0m"
    fi
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
