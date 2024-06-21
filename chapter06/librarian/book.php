<?php
require 'vendor/autoload.php';
require 'rest.functions.php';
use Librarian\Controller\REST\BookREST;
header('Content-Type: application/json');
$response = '';
$bookREST = new BookREST();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $response = $bookREST->post($_POST);
        break;
    case 'PUT':        
        $response = $bookREST->put(getPutData());
        break;
    case 'GET':
        $response = $bookREST->get($_GET['code'] ?? 0);
        break;
    case 'DELETE':
        $response = $bookREST->delete($_GET['code'] ?? 0);
        break;
    default:
        $response = ['error' => 'METHOD NOT ALLOWED'];
        http_response_code(405);
}
echo json_encode($response);