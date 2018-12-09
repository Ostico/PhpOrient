#!/usr/bin/env bash

cd /home/PhpOrient
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install

if [[ -n "${XDEBUG_CONFIG}" ]]; then
    XDEBUG='zend_extension='$(find /usr/lib/php -name xdebug.so)'
    xdebug.remote_enable=1
    xdebug.remote_autostart=1
    xdebug.remote_host="'${XDEBUG_CONFIG}'"
    xdebug.remote_port=9000
    xdebug.idekey="PHPSTORM"'

    printf "${XDEBUG}\n\n"
    printf "${XDEBUG}" > /etc/php/7.2/mods-available/xdebug.ini
fi

while true; do
#    echo date " => Waiting for an infinite. More or less..."
    sleep 5
done
