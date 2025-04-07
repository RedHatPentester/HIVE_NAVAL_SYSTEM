#!/bin/bash

# Hive Naval System Setup Script
echo -e "\033[34m"
echo "   __   __  _______  __   __  ___   _______  ___      ___ "
echo "  |  |_|  ||       ||  | |  ||   | |       ||   |    |   |"
echo "  |       ||    ___||  |_|  ||   | |_     _||   |    |   |"
echo "  |       ||   |___ |       ||   |   |   |  |   |    |   |"
echo "  |       ||    ___||_     _||   |   |   |  |   |___ |   |"
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
apt-get install -y mysql-server php php-mysql

# Configure MySQL
echo -e "\033[33m🌊 Charting the database waters...\033[0m"
mysql -e "CREATE DATABASE IF NOT EXISTS hive_naval;"
mysql -e "CREATE USER IF NOT EXISTS 'hive_user'@'localhost' IDENTIFIED BY 'N@vyS3cr3t!';"
mysql -e "GRANT ALL PRIVILEGES ON hive_naval.* TO 'hive_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Import database schema
echo -e "\033[33m📦 Loading cargo (database schema)...\033[0m"
[ -f officers.sql ] && mysql hive_naval < officers.sql || echo -e "\033[31m✘ Missing officers.sql - database will be empty!\033[0m"

# Set file permissions
echo -e "\033[33m🔒 Securing the hatches...\033[0m"
chmod 600 includes/config.php

echo -e "\033[32m"
echo "╔══════════════════════════════════════════╗"
echo "║  Hive Naval System successfully deployed  ║"
echo "╚══════════════════════════════════════════╝"
echo -e "\033[0m"

echo -e "\033[36m🚀 Launching system on http://localhost:8080\033[0m"
echo -e "\033[35mTry these secret codes after setup:"
echo "- Login with the credential of that guy that was careless with his logins: carl/ilovemywife"
echo "- Backup code: 1337"
echo "- Konami code: ↑↑↓↓←→←→BA"
echo "- Right-click admin dashboard\033[0m"
php -S localhost:8080
