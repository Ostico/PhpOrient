#!/usr/bin/env bash

apt-get update
apt-get -y install locate php5 php5-dev libxml2-dev libreadline-dev \
                    php5-json php5-curl php5-xdebug php5-mysql php5-xsl default-jdk ant \
                    libxslt1-dev super git libssl-dev

# Configure XDebug
XDEBUG='zend_extension='$(find /usr/lib/php5/ -name xdebug.so)'
xdebug.remote_enable=1
xdebug.remote_autostart=1
xdebug.remote_host="'${VAGRANT_HOST}'"
xdebug.remote_port=9000
xdebug.idekey="storm"
'
printf "${XDEBUG}" > /etc/php5/mods-available/xdebug.ini


#OrientDB init.d and installation
/bin/bash /var/www/PhpOrient/ci/start-ci.sh ${ORIENTDB_VERSION}
cp /var/www/PhpOrient/ci/vagrant/start_stop_daemon.orientdb.sh /etc/init.d/orientdb
chmod +x /etc/init.d/orientdb

P_DIR=$(dirname $(dirname $(cd "$(dirname "$0")"; pwd ) ) )
ODB_DIR="${P_DIR}/ci/environment/orientdb-community-${ORIENTDB_VERSION}"
sed -ri -e "s#^ORIENT_ROOT.*#ORIENT_ROOT=\`readlink -f ${ODB_DIR}/bin\`#" /etc/init.d/orientdb
#OrientDB init.d and installation


cd /var/www/PhpOrient/
curl -sS https://getcomposer.org/installer | php
/usr/bin/php composer.phar install
alias phpunit=./vendor/bin/phpunit

pushd /var/www/

#wget -q https://github.com/virtphp/virtphp/releases/download/v0.5.2-alpha/virtphp.phar
git clone https://github.com/virtphp/virtphp.git
#git checkout v0.5.2-alpha    # 6ada045dad97624c94cdff327f686d5db745aa47

cd /var/www/virtphp/
cp ../PhpOrient/composer.phar .
/usr/bin/php composer.phar install

cd ./bin
sed -ri -e "s/^[;]{1}phar.readonly = On/phar.readonly = Off/" /etc/php5/cli/php.ini

/usr/bin/php ./compile
chmod +x virtphp.phar
mv virtphp.phar /usr/bin/virtphp

curl -q -L -O https://github.com/phpbrew/phpbrew/raw/master/phpbrew
chmod +x phpbrew
mv phpbrew /usr/local/bin/phpbrew

popd