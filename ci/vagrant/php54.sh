#!/usr/bin/env bash

# install with vagrant user
phpbrew init

export PHPBREW_SET_PROMPT=1
[[ -e ~/.phpbrew/bashrc ]] && source ~/.phpbrew/bashrc

phpbrew install 5.4.45 +cli +json +readline +session +sockets +xml_all \
                       +curl +mysql +pdo +xml +debug +openssl +filter +hash

phpbrew switch 5.4.45
phpbrew ext install xdebug 2.3.3

virtphp create php54
echo "source ~/.virtphp/envs/php54/bin/activate" >> ~/.bashrc
