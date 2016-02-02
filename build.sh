#!/bin/bash

# Install the latest Composer because sometimes Travis' is out of date.
curl -sS https://getcomposer.org/installer | php
php composer.phar install;

# Run the tests.
vendor/bin/phpunit --bootstrap test/bootstrap.php test