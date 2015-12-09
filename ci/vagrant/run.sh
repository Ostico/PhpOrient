#!/usr/bin/env bash

apt-get update
apt-get -y install locate php5 php5-dev libxml2-dev libreadline-dev \
                    php5-json php5-curl php5-xdebug php5-mysql php5-xsl default-jdk ant \
                    libxslt1-dev super

# Configure XDebug
XDEBUG='zend_extension='$(find /usr/lib/php5/ -name xdebug.so)'
xdebug.remote_enable=1
xdebug.remote_autostart=1
xdebug.remote_host="'${VAGRANT_HOST}'"
xdebug.remote_port=9000
xdebug.idekey="storm"
'
printf "${XDEBUG}" > /etc/php5/mods-available/xdebug.ini

/bin/bash /var/www/PhpOrient/ci/start-ci.sh

cd /var/www/PhpOrient/
/usr/bin/php composer.phar install
alias phpunit=./vendor/bin/phpunit

wget -q https://github.com/virtphp/virtphp/releases/download/v0.5.2-alpha/virtphp.phar
chmod +x virtphp.phar
mv virtphp.phar /usr/bin/virtphp

curl -q -L -O https://github.com/phpbrew/phpbrew/raw/master/phpbrew
chmod +x phpbrew
mv phpbrew /usr/local/bin/phpbrew