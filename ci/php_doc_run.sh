#!/bin/bash
set -e
mkdir ../docs/doc_creation/
php ../vendor/bin/phpdoc -d ../src/ -t ../docs/doc_creation --template="xml"

pushd ../docs/doc_creation/
rm -rf phpdoc-*
popd

pushd ../docs/
rm -rf *.md
popd

php ../vendor/bin/phpdocmd ../docs/doc_creation/structure.xml ../docs/ --lt "%c"

pushd ../docs/
rm -rf doc_creation/
## update the wiki git
git add .
git commit -a -m"Update Wiki"
popd

## update main project git
git add ../docs/
cd ../../
