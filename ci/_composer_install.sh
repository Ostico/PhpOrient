#!/usr/bin/env bash

set -e

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

if [ ! -d "$PARENT_DIR/vendor/bin" ]; then
    cd "${PARENT_DIR}"
    echo "Updating Composer....\n"
    php -r "readfile('https://getcomposer.org/installer');" | php
    echo "Launch...\n"
    php "$PARENT_DIR/composer.phar" update --prefer-source
fi