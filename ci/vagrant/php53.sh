#!/usr/bin/env bash

# install with vagrant user
phpbrew init

export PHPBREW_SET_PROMPT=1
[[ -e ~/.phpbrew/bashrc ]] && source ~/.phpbrew/bashrc

phpbrew install 5.3.29 +cli +json +readline +session +sockets +xml_all \
                       +curl +mysql +pdo +xml +debug +openssl +filter +hash +iconv +mbstring

phpbrew switch 5.3.29
phpbrew ext install xdebug 2.3.3

virtphp create php53
echo "source ~/.virtphp/envs/php53/bin/activate" >> ~/.bashrc
