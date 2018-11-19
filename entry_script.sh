#!/bin/bash

set -e

if [ "$1" = 'apache2-foreground' ]; then
    ./yii migrate --interactive=0

    shift
    exec apache2-foreground "$@"
fi

exec "$@"
