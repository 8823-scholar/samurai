#!/bin/sh
#
#   samurai.sh
#
#   command line gateway to the samurai generator
#

## set PHP command
if [ -z "$PHP_COMMAND" ]; then
    if [ -x "@PHP-BIN@" ]; then
        PHP_COMMAND="@PHP-BIN@"
    else
        PHP_COMMAND=php
    fi
fi

## set Samurai dir
if [ -z "$SAMURAI_DIR" ]; then
    SAMURAI_DIR="@PEAR-DIR@/Samurai"
fi

## execute Generator
SAMURAI_GENERATOR="$SAMURAI_DIR/generator/generator.php"
$PHP_COMMAND -d html_errors=off -qC $SAMURAI_GENERATOR $*

