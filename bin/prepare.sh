#!/usr/bin/env bash

set -e

current=$(cd $(dirname $0);
pwd)
source ${current}/variables.sh

if [[ -z "${NO_NPM}" ]]; then
    echo ""
    echo ">> Install latest node."
    source ${current}/prepare/install-latest-node.sh
fi

if [[ "${TRAVIS_BUILD_STAGE_NAME}" == "Deploy" ]]; then
    echo ""
    echo ">> Build"
    composer build
else
    echo ""
    echo ">> Setup"
    if [[ -z "${NO_COMPOSER}" ]]; then
        composer install
    fi
    if [[ -z "${NO_NPM}" ]]; then
        yarn install
    fi
fi

if [[ "${TRAVIS_BUILD_STAGE_NAME}" == "Test" ]]; then
    echo ""
    echo ">> Setup Database"
    psql -c 'create database travis_ci_test;' -U postgres
    cp .env.travis .env
    php artisan key:generate
fi

if [[ -n "${LARAVEL_DUSK}" ]]; then
    echo ""
    echo ">> Prepare for Laravel Dusk"
    google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost &
    php artisan serve &
fi
