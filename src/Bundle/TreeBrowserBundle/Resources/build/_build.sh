#!/usr/bin/env bash
set -e

MODE=$1||development

echo ""
echo " * Executing webpack..."
# Compile all javascript files and minify them together with Fancytree
webpack --mode ${MODE} -p --progress

echo ""
echo " * Copying required vendor files to public directory..."
# jQuery
if [ ! -d "../public/vendor/jquery" ]; then
    mkdir -p ../public/vendor/jquery/dist
fi
cp ./node_modules/jquery/dist/jquery.min.js ../public/vendor/jquery/dist/jquery.min.js

# jQuery UI
if [ ! -d "../public/vendor/jquery-ui" ]; then
    mkdir ../public/vendor/jquery-ui
fi
cp ./node_modules/jquery-ui/dist/jquery-ui.min.js ../public/vendor/jquery-ui/jquery-ui.min.js
