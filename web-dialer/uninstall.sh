#!/usr/bin/env bash
# Removes WebDialer from a Debian host that was set up by install.sh
set -e
[[ $EUID -eq 0 ]] || { echo "Run as root:  sudo bash uninstall.sh"; exit 1; }

APP_DIR="/var/www/html/web-dialer"
DB_NAME="webdialer"
DB_USER="webdialer"

read -p "Remove WebDialer, database and services? [y/N] " ans
[[ "$ans" =~ ^[Yy]$ ]] || exit 0

systemctl stop  webdialer-fastapi 2>/dev/null || true
systemctl disable webdialer-fastapi 2>/dev/null || true
rm -f /etc/systemd/system/webdialer-fastapi.service
systemctl daemon-reload

a2dissite web-dialer 2>/dev/null || true
rm -f /etc/apache2/sites-available/web-dialer.conf
systemctl reload apache2

mysql -e "DROP DATABASE IF EXISTS \`${DB_NAME}\`;"
mysql -e "DROP USER IF EXISTS '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

rm -rf "${APP_DIR}"
rm -f  /root/webdialer_credentials.txt

echo "WebDialer uninstalled. Apache/MariaDB/Asterisk packages left in place."
