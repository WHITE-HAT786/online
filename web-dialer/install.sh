#!/usr/bin/env bash
# =============================================================================
#  WebDialer — one-shot installer for a fresh Debian 12 (bookworm) server
# -----------------------------------------------------------------------------
#  Installs:  Apache 2 + PHP 8.2 + MariaDB (MySQL) + Asterisk + Python/FastAPI
#  Configures: DB import, Apache vhost, Asterisk AMI, FastAPI systemd service,
#              firewall rules.
#
#  Usage:   sudo bash install.sh
# =============================================================================
set -Eeuo pipefail

# ---------- Colors --------------------------------------------------------------
R='\033[0;31m'; G='\033[0;32m'; Y='\033[1;33m'; B='\033[0;34m'; C='\033[0;36m'; N='\033[0m'
step()  { echo -e "\n${B}==>${N} ${C}$*${N}"; }
ok()    { echo -e "  ${G}✓${N} $*"; }
warn()  { echo -e "  ${Y}!${N} $*"; }
fail()  { echo -e "  ${R}✗${N} $*"; exit 1; }
[[ $EUID -eq 0 ]] || fail "Please run as root:  sudo bash install.sh"

# ---------- Config (edit as needed) --------------------------------------------
APP_DIR="/var/www/html/web-dialer"
DOMAIN="_"                                # Apache ServerName (use _ for default)
DB_NAME="webdialer"
DB_USER="webdialer"
DB_PASS="$(openssl rand -hex 16)"
AMI_USER="webdialer"
AMI_SECRET="$(openssl rand -hex 12)"
FASTAPI_PORT="8766"
FASTAPI_USER="www-data"
SOURCE_DIR="$(cd "$(dirname "$0")" && pwd)"    # where install.sh lives

echo -e "${B}
╔══════════════════════════════════════════════════════════╗
║             WebDialer  ·  Debian 12 Installer            ║
╚══════════════════════════════════════════════════════════╝${N}"

# ---------- 1. System update ----------------------------------------------------
step "Updating apt cache"
apt-get update -y >/dev/null
apt-get -y upgrade >/dev/null || true
ok "System up to date"

# ---------- 2. Core packages ----------------------------------------------------
step "Installing core packages (Apache, PHP, MariaDB, Python, tools)"
DEBIAN_FRONTEND=noninteractive apt-get install -y \
    apache2 \
    mariadb-server mariadb-client \
    php php-cli php-fpm php-mysql php-curl php-mbstring php-xml php-zip php-json php-gd \
    libapache2-mod-php \
    python3 python3-pip python3-venv \
    git curl unzip ufw openssl ca-certificates >/dev/null
ok "Base packages installed"

# ---------- 3. Asterisk ---------------------------------------------------------
step "Installing Asterisk"
DEBIAN_FRONTEND=noninteractive apt-get install -y asterisk asterisk-modules >/dev/null
systemctl enable asterisk >/dev/null 2>&1 || true
ok "Asterisk installed"

# ---------- 4. Enable Apache modules --------------------------------------------
step "Enabling Apache modules"
a2enmod rewrite headers php* >/dev/null 2>&1 || true
ok "mod_rewrite + mod_headers enabled"

# ---------- 5. MariaDB — secure + database --------------------------------------
step "Configuring MariaDB"
systemctl enable mariadb >/dev/null 2>&1
systemctl start  mariadb
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
ok "Database '${DB_NAME}' and user '${DB_USER}' created"

# Import schema if present
if [[ -f "${SOURCE_DIR}/database.sql" ]]; then
    mysql "${DB_NAME}" < "${SOURCE_DIR}/database.sql"
    ok "database.sql imported"
else
    warn "database.sql not found next to install.sh — skipping schema import"
fi

# ---------- 6. Deploy web-dialer files ------------------------------------------
step "Deploying app to ${APP_DIR}"
mkdir -p "${APP_DIR}"
# If we're already IN the source tree, just symlink; else copy
if [[ "${SOURCE_DIR}" != "${APP_DIR}" ]]; then
    rsync -a --delete --exclude='.git' --exclude='storage/*.log' "${SOURCE_DIR}/" "${APP_DIR}/"
fi
mkdir -p "${APP_DIR}/storage" "${APP_DIR}/uploads"
chown -R www-data:www-data "${APP_DIR}"
chmod -R 755 "${APP_DIR}"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/uploads"
ok "Files deployed"

# ---------- 7. Update backend/config/database.php with real credentials ---------
step "Writing database credentials into backend/config/database.php"
cat > "${APP_DIR}/backend/config/database.php" <<PHPEOF
<?php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', '${DB_NAME}');
define('DB_USER', '${DB_USER}');
define('DB_PASS', '${DB_PASS}');
define('DB_CHARSET', 'utf8mb4');

function db(): PDO {
  static \$pdo = null;
  if (\$pdo === null) {
    \$dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset='.DB_CHARSET;
    try {
      \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
      ]);
    } catch (PDOException \$e) {
      error_log('DB connection failed: '.\$e->getMessage());
      http_response_code(500);
      header('Content-Type: application/json');
      echo json_encode(['success'=>false,'message'=>'Database connection failed.']);
      exit;
    }
  }
  return \$pdo;
}
PHPEOF
chown www-data:www-data "${APP_DIR}/backend/config/database.php"
chmod 640 "${APP_DIR}/backend/config/database.php"
ok "database.php configured"

# ---------- 8. Apache virtual host ----------------------------------------------
step "Configuring Apache vhost"
cat > /etc/apache2/sites-available/web-dialer.conf <<APACHECONF
<VirtualHost *:80>
    ServerName ${DOMAIN}
    DocumentRoot ${APP_DIR}
    DirectoryIndex app/login.php

    <Directory ${APP_DIR}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  \${APACHE_LOG_DIR}/webdialer_error.log
    CustomLog \${APACHE_LOG_DIR}/webdialer_access.log combined
</VirtualHost>
APACHECONF
a2dissite 000-default >/dev/null 2>&1 || true
a2ensite  web-dialer  >/dev/null
systemctl reload apache2
ok "vhost live — DirectoryIndex → app/login.php"

# ---------- 9. Asterisk AMI configuration ---------------------------------------
step "Configuring Asterisk AMI for WebDialer"
MANAGER_CONF="/etc/asterisk/manager.conf"
# Ensure [general] enabled=yes
if grep -q "^enabled *= *no" "${MANAGER_CONF}" 2>/dev/null; then
    sed -i 's/^enabled *= *no/enabled = yes/' "${MANAGER_CONF}"
elif ! grep -q "^enabled *= *yes" "${MANAGER_CONF}" 2>/dev/null; then
    printf "\n[general]\nenabled = yes\nport = 5038\nbindaddr = 0.0.0.0\n" >> "${MANAGER_CONF}"
fi
# Add webdialer manager user (idempotent)
if ! grep -q "^\[${AMI_USER}\]" "${MANAGER_CONF}" 2>/dev/null; then
cat >> "${MANAGER_CONF}" <<AMIEOF

[${AMI_USER}]
secret = ${AMI_SECRET}
deny   = 0.0.0.0/0.0.0.0
permit = 127.0.0.1/255.255.255.255
read   = system,call,log,verbose,command,agent,user,config,dtmf,reporting,cdr,dialplan
write  = system,call,agent,user,command,reporting,originate
AMIEOF
fi
systemctl restart asterisk >/dev/null 2>&1 || true
asterisk -rx "manager reload" >/dev/null 2>&1 || true
ok "AMI enabled — user '${AMI_USER}' created (localhost only)"

# ---------- 10. Write asterisk/config.php with real AMI credentials -------------
step "Writing asterisk/config.php"
cat > "${APP_DIR}/asterisk/config.php" <<ASTPHPEOF
<?php
return [
  'mode'         => 'bridge',
  'bridge_url'   => getenv('DIALER_API_URL') ?: 'http://127.0.0.1:${FASTAPI_PORT}',
  'bridge_key'   => getenv('DIALER_API_KEY') ?: '',
  'host'         => '127.0.0.1',
  'port'         => 5038,
  'username'     => '${AMI_USER}',
  'secret'       => '${AMI_SECRET}',
  'timeout'      => 5,
  'context'      => 'from-internal',
  'channel_tech' => 'PJSIP',
  'caller_id'    => 'WebDialer <1000>',
  'enabled'      => true,
];
ASTPHPEOF
chown www-data:www-data "${APP_DIR}/asterisk/config.php"
chmod 640 "${APP_DIR}/asterisk/config.php"
ok "asterisk/config.php written (bridge mode)"

# ---------- 11. Python FastAPI bridge -------------------------------------------
step "Setting up FastAPI bridge (port ${FASTAPI_PORT})"
if [[ -f "${APP_DIR}/fastapi_app.py" ]]; then
    python3 -m venv "${APP_DIR}/venv"
    "${APP_DIR}/venv/bin/pip" install --upgrade pip >/dev/null
    if [[ -f "${APP_DIR}/requirements.txt" ]]; then
        "${APP_DIR}/venv/bin/pip" install -r "${APP_DIR}/requirements.txt" >/dev/null
    else
        "${APP_DIR}/venv/bin/pip" install fastapi uvicorn mysql-connector-python python-multipart >/dev/null
    fi

    cat > /etc/systemd/system/webdialer-fastapi.service <<SYSDEOF
[Unit]
Description=WebDialer FastAPI bridge
After=network.target mariadb.service asterisk.service

[Service]
Type=simple
User=root
WorkingDirectory=${APP_DIR}
Environment="ASTERISK_CLI_PATH=/usr/sbin/asterisk"
Environment="FILE_ROOT=/var/www"
ExecStart=${APP_DIR}/venv/bin/uvicorn fastapi_app:app --host 0.0.0.0 --port ${FASTAPI_PORT}
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
SYSDEOF
    systemctl daemon-reload
    systemctl enable webdialer-fastapi >/dev/null 2>&1
    systemctl restart webdialer-fastapi
    ok "FastAPI running as systemd service on :${FASTAPI_PORT}"
else
    warn "fastapi_app.py not found — skipping FastAPI setup. Asterisk will use direct AMI."
fi

# ---------- 12. Firewall --------------------------------------------------------
step "Configuring firewall (ufw)"
if ! ufw status | grep -q "Status: active"; then
    ufw --force enable >/dev/null 2>&1 || true
fi
ufw allow 22/tcp    >/dev/null 2>&1 || true    # SSH
ufw allow 80/tcp    >/dev/null 2>&1 || true    # HTTP
ufw allow 443/tcp   >/dev/null 2>&1 || true    # HTTPS
ufw allow 5060/udp  >/dev/null 2>&1 || true    # SIP UDP
ufw allow 5060/tcp  >/dev/null 2>&1 || true    # SIP TCP
ufw allow 5061/tcp  >/dev/null 2>&1 || true    # SIP TLS
ufw allow 10000:20000/udp >/dev/null 2>&1 || true  # RTP media
ufw allow ${FASTAPI_PORT}/tcp >/dev/null 2>&1 || true
ok "Firewall rules applied"

# ---------- 13. Restart services ------------------------------------------------
step "Restarting services"
systemctl restart apache2  && ok "Apache restarted"
systemctl restart mariadb  && ok "MariaDB restarted"
systemctl restart asterisk && ok "Asterisk restarted" || warn "Asterisk restart returned non-zero"

# ---------- 14. Save credentials for the operator -------------------------------
CREDS_FILE="/root/webdialer_credentials.txt"
cat > "${CREDS_FILE}" <<INFO
================ WebDialer — Installation Credentials ================
Install date : $(date)

--- MariaDB ---
Database     : ${DB_NAME}
User         : ${DB_USER}
Password     : ${DB_PASS}
Host         : 127.0.0.1:3306

--- Asterisk AMI ---
User         : ${AMI_USER}
Secret       : ${AMI_SECRET}
Port         : 5038 (localhost only)

--- FastAPI bridge ---
Port         : ${FASTAPI_PORT}
Service      : systemctl status webdialer-fastapi
Base URL     : http://127.0.0.1:${FASTAPI_PORT}
Public URL   : http://<this-host-ip>:${FASTAPI_PORT}

--- App URL ---
http://<this-host-ip>/    →    login.php

--- Demo user (from database.sql) ---
Username     : john
Password     : 123456
=======================================================================
INFO
chmod 600 "${CREDS_FILE}"

# ---------- Done ----------------------------------------------------------------
IP="$(hostname -I | awk '{print $1}')"
echo -e "\n${G}════════════════════════════════════════════════════════════${N}"
echo -e " ${G}✓ Installation complete!${N}"
echo -e "${G}════════════════════════════════════════════════════════════${N}"
echo -e "  Open      : ${C}http://${IP}/${N}"
echo -e "  Login as  : ${C}john  /  123456${N}"
echo -e "  Creds     : ${Y}${CREDS_FILE}${N}"
echo -e "  Services  : ${C}systemctl status apache2 mariadb asterisk webdialer-fastapi${N}"
echo -e "  Logs      : ${C}journalctl -u webdialer-fastapi -f${N}"
echo -e "  Uninstall : ${C}sudo bash ${APP_DIR}/uninstall.sh${N}"
echo ""
