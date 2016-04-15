#!/usr/bin/env bash

# install with vagrant user
phpbrew init

export PHPBREW_SET_PROMPT=1
[[ -e ~/.phpbrew/bashrc ]] && source ~/.phpbrew/bashrc

phpbrew install 5.4.45 +cli +json +readline +session +sockets +xml_all \
                       +curl +mysql +pdo +xml +debug +openssl +filter +hash +iconv +mbstring

phpbrew switch 5.4.45
phpbrew ext install xdebug 2.3.3

/usr/bin/virtphp create php54 --verbose
echo "source ~/.phpbrew/bashrc" >> ~/.bashrc
echo "source ~/.virtphp/envs/php54/bin/activate" >> ~/.bashrc
source ~/.virtphp/envs/php54/bin/activate

echo "VAGRANT UP COMPLETED."