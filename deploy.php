<?php
/*
 * This file has been generated automatically.
 * Please change the configuration for correct use deploy.
 */

require 'vendor/deployer/deployer/recipe/symfony.php';

// Set configurations
set('repository', 'https://9319258b78e46a55bdb352bfdc2e3c198b4b30d2@github.com/otlet/JestemGraczem.git');
set('shared_files', ['app/config/parameters.yml']);
set('shared_dirs', ['app/logs']);
set('writable_dirs', ['app/cache', 'app/logs']);

// Configure servers
server('production', 'prod.domain.com')
    ->user('username')
    ->password()
    ->env('deploy_path', '/var/www/prod.domain.com');

server('beta', 'ubuntu.mike.otlet.pl')
    ->user('beta')
    ->password('Siewo2323')
    ->env('deploy_path', '/var/www');

/**
 * Restart php-fpm on success deploy.
 */
task('php-fpm:restart', function () {
    // Attention: The user must have rights for restart service
    // Attention: the command "sudo /bin/systemctl restart php-fpm.service" used only on CentOS system
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo /bin/systemctl restart php-fpm.service');
})->desc('Restart PHP-FPM service');

after('success', 'php-fpm:restart');


/**
 * Attention: This command is only for for example. Please follow your own migrate strategy.
 * Attention: Commented by default.  
 * Migrate database before symlink new release.
 */
 
// before('deploy:symlink', 'database:migrate');