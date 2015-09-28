#!/usr/bin/env bash

set -e

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

if [ ! -d "$PARENT_DIR/vendor/bin" ]; then
    cd "${PARENT_DIR}"
    php -r "readfile('https://getcomposer.org/installer');" | php
    php "$PARENT_DIR/composer.phar" install --prefer-source
fi