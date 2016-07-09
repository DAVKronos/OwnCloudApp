#!bin/env bash
echo "\$CONFIG['kronos_user'] = getenv('KRONOS_USER');" >> config.php
echo "\$CONFIG['kronos_password'] = getenv('KRONOS_PASSWORD')" >> config.php
echo "\$CONFIG['kronos_database'] = getenv('KRONOS_DATABASE')" >> config.php
echo "\$CONFIG['kronos_hostname'] = getenv('KRONOS_HOSTNAME')" >> config.php

