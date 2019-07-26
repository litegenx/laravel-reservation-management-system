#!/usr/bin/env bash

set -e

current=$(cd $(dirname $0);
pwd)
source ${current}/variables.sh

if [[ "${TRAVIS_BUILD_STAGE_NAME}" == "Test" ]]; then
    echo ""
    echo ">> Setup"
    composer setup

    echo ""
    echo ">> Setup Database"
    psql -c 'create database travis_ci_test;' -U postgres
    cp .env.travis .env
    php artisan key:generate
fi

if [[ "${TRAVIS_BUILD_STAGE_NAME}" == "Deploy" ]]; then
    echo ""
    echo ">> Build"

    composer build
fi

if [[ -n "${LARAVEL_DUSK}" ]]; then
    echo ""
    echo ">> Prepare for Laravel Dusk"

    google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost &
    php artisan serve &
fi
