#!/bin/bash

php ../vendor/bin/phpdoc -d /var/www/PhpOrient/src/ -t ../docs/doc_creation --template="xml"
pushd ../docs/doc_creation/
rm -rf phpdoc-*
popd
pushd ../docs/
rm -rf *.md
popd
php ../vendor/bin/phpdocmd ../docs/doc_creation/structure.xml ../docs/
git add ../docs/