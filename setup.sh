#!/bin/bash

# Hive Naval System Setup Script

# Check for root privileges
if [ "$(id -u)" -ne 0 ]; then
  echo "Please run as root"
  exit 1
fi

# Install required packages
apt-get update
apt-get install -y mysql-server php php-mysql

# Configure MySQL
mysql -e "CREATE DATABASE IF NOT EXISTS hive_naval;"
mysql -e "CREATE USER IF NOT EXISTS 'hive_user'@'localhost' IDENTIFIED BY 'N@vyS3cr3t!';"
mysql -e "GRANT ALL PRIVILEGES ON hive_naval.* TO 'hive_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Import database schema
mysql hive_naval < officers.sql

# Set file permissions
chmod 600 includes/config.php

echo "Hive Naval System setup complete"
echo "Starting PHP development server..."
php -S localhost:8080
