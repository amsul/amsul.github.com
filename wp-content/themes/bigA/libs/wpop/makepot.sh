#!/bin/bash

echo "Wordspop POT Generator version 1.0"
echo "----------------------------------"

cd ..
cd ..
echo "Root theme directory: `pwd`"

if [ ! -d `pwd` ]; then
  echo "Creating directory `pwd`/languages"
  mkdir "languages"
fi

echo "Creating a list of files to scan"
 find . -type f -iname "*.php" > files.tmp

echo "Creating a POT file ${PWD##*/}.pot"
xgettext --language=PHP --indent --keyword=__ --keyword=_e --keyword=__ngettext:1,2 -s -n --from-code=UTF8 --files-from=files.tmp --output=${PWD##*/}.pot --output-dir=languages
echo "POT file `pwd`/languages/${PWD##*/}.pot has been created"

echo "Removing list"
rm "files.tmp"

echo "Done!"
