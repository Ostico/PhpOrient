#!/usr/bin/env bash

cd /home/PhpOrient
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install


while true; do
#    echo date " => Waiting for an infinite. More or less..."
    sleep 5
done
