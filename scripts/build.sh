#!/usr/bin/env bash

set -eo pipefail

cd "$(dirname "$0")/.."

rm -rf 'src/js'
rm -rf 'src/css/beer.min.css'
mkdir -p 'src/js'
mkdir -p 'src/css'
mkdir -p 'src/fonts'
cp 'node_modules/beercss/dist/cdn/beer.min.js' 'src/js/beer.min.js'
cp 'node_modules/beercss/dist/cdn/beer.min.css' 'node_modules/font-awesome/css/font-awesome.min.css' 'src/css'
cp 'node_modules/font-awesome/fonts/fontawesome-webfont.woff' 'node_modules/font-awesome/fonts/fontawesome-webfont.woff2' 'src/fonts'
node_modules/.bin/tsc $1
