#!/usr/bin/env bash

apt-get update
apt-get -y install locate php5 php5-dev libxml2-dev libreadline-dev \
                    php5-json php5-curl php5-mysql php5-xsl default-jdk ant \
                    libxslt1-dev super git libssl-dev maven libcurl3-openssl-dev

sed -ri -e "s/^[;]{1}phar.readonly = On/phar.readonly = Off/" /etc/php5/cli/php.ini

#OrientDB init.d and installation
/bin/bash /var/www/PhpOrient/ci/start-ci.sh ${ORIENTDB_VERSION}
cp /var/www/PhpOrient/ci/vagrant/start_stop_daemon.orientdb.sh /etc/init.d/orientdb
chmod +x /etc/init.d/orientdb


cd /var/www/
git clone https://github.com/virtphp/virtphp.git

cd virtphp/
/usr/bin/php -r "readfile('https://getcomposer.org/installer');" | php
/usr/bin/php composer.phar install

cd ./bin
./compile
chmod +x virtphp.phar
mv virtphp.phar /usr/bin/virtphp

cd ..
cp composer.phar /var/www/PhpOrient/

cd /var/www/
curl -q -L -O https://github.com/phpbrew/phpbrew/raw/master/phpbrew
chmod +x phpbrew
mv phpbrew /usr/local/bin/phpbrew

cd /var/www/PhpOrient
find . -type d | xargs chmod 775
find . -type f | xargs chmod 664


apt-get install -y php5-xdebug

# Configure XDebug
XDEBUG='zend_extension='$(find /usr/lib/php5/ -name xdebug.so)'
xdebug.remote_enable=1
xdebug.remote_autostart=1
xdebug.remote_host="'${VAGRANT_HOST}'"
xdebug.remote_port=9000
xdebug.idekey="storm"
'

printf "${XDEBUG}" > /etc/php5/mods-available/xdebug.ini