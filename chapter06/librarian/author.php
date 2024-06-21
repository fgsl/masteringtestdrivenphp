<?php
require 'vendor/autoload.php';
require 'rest.functions.php';
use Librarian\Controller\REST\AuthorREST;

header('Content-Type: application/json');
$response = '';
$authorREST = new AuthorREST();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $response = $authorREST->post($_POST);
        break;
    case 'PUT':
        $response = $authorREST->put(getPutData());
        break;
    case 'GET':
        $response = $authorREST->get((int)$_GET['code'] ?? 0);
        break;
    case 'DELETE':
        $response = $authorREST->delete((int)$_GET['code'] ?? 0);
        break;
    default:
        $response = ['error' => 'METHOD NOT ALLOWED'];
        http_response_code(405);
}
echo json_encode($response);