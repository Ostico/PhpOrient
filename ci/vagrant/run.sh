#!/usr/bin/env bash

apt-get update
apt-get -y install ssh-client vim locate curl wget net-tools \
    php5 php5-json php5-curl php5-xdebug php5-mysql php5-xsl default-jdk ant

# Configure XDebug
XDEBUG='zend_extension='$(find /usr/lib/php5/ -name xdebug.so)'
xdebug.remote_enable=1
xdebug.remote_autostart=1
xdebug.remote_host="'${DOCKER_HOST}'"
xdebug.remote_port=9000
xdebug.idekey="storm"
'
printf "${XDEBUG}" > /etc/php5/mods-available/xdebug.ini

/bin/bash /var/www/PhpOrient/ci/start-ci.sh

cd /var/www/PhpOrient/
/usr/bin/php composer.phar install
alias phpunit=./vendor/bin/phpunit
