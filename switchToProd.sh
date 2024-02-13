php composer.phar dump-env prod
php composer.phar dump-autoload -o --no-dev
php bin/console cache:clear