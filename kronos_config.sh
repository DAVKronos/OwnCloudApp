#!bin/sh
echo "\$CONFIG['kronos_user'] = getenv('KRONOS_USER');" >> /var/www/html/config/config.php
echo "\$CONFIG['kronos_password'] = getenv('KRONOS_PASSWORD')" >> /var/www/html/config/config.php
echo "\$CONFIG['kronos_database'] = getenv('KRONOS_DATABASE')" >> /var/www/html/config/config.php
echo "\$CONFIG['kronos_hostname'] = getenv('KRONOS_HOSTNAME')" >> /var/www/html/config/config.php

