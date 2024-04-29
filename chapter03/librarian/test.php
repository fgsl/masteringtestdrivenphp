<?php
require 'vendor/autoload.php';
$path = realpath(__DIR__ . '/../');
$descriptorspec = array(
            0 => ["pipe", "r"], 
            1 => ["pipe", "w"],
            2 => ["file", "/dev/null", "a"]
);
$process = proc_open('nohup php -S localhost:8008 &',$descriptorspec,$path);
$rest = new Fgsl\Rest\Rest();
$response = $rest->doGet([],'localhost:8008/book.edit.php?code=1',200);
echo $response;
proc_terminate($process);
$doc = new DomDocument();
$doc->loadHTML($response);

