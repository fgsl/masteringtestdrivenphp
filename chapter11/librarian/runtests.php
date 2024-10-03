<?php
use Librarian\Test\PHPServer;

require 'vendor/autoload.php';
PHPServer::getInstance()->start();
echo shell_exec('vendor/bin/phpunit uitests');
PHPServer::getInstance()->stop();