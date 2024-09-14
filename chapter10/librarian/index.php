<?php
require 'vendor/autoload.php';

if (isset($_GET['api'])){
    $api = $_GET['api'];
    $controller = 'Librarian\Controller\\REST\\' . ucfirst($api) . 'REST';
    $action = 'index'; 
} else {
    $c = isset($_GET['c']) ? $_GET['c'] : 'index';
    $action = isset($_GET['a']) ? $_GET['a'] : 'index';

    $controller = 'Librarian\Controller\\' . ucfirst($c);
}

(new $controller())->run($action);