<?php
namespace Librarian\Controller\REST;
use Librarian\Util\Config;
use Librarian\Auth\Adapter as AuthAdapter;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
abstract class AbstractRESTController
{
    public function run(string $action)
    {
        $fullClassName = get_class($this);
        $parts = explode('\\',$fullClassName);
        $className = end($parts);

        Config::override('auth_strategy','api');
        $authAdapter = new AuthAdapter();
        if (!$authAdapter->hasIdentity() && $className != 'IndexREST')
        {
            $_SERVER['REQUEST_METHOD'] = 'INVALID';
        }

        $response = '';
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $response = $this->post($_POST);
                break;
            case 'PUT':
                $response = $this->put($this->getPutData());
                break;
            case 'GET':
                $response = $this->get((int)($_GET['code'] ?? 0));                
                break;
            case 'DELETE':
                $response = $this->delete((int)($_GET['code'] ?? 0));
                break;
            default:
                $response = ['error' => 'METHOD NOT ALLOWED'];
                http_response_code(405);
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    protected function getPutData()
    {
        $stdin = fopen('php://input', 'r');
        $putData = '';
        while($data = fread($stdin, 1024))
            $putData .= $data;
        fclose($stdin);
        $rows = explode('&',$putData);
        $fields = [];
        foreach($rows as $row)
        {
            $tokens = explode('=', $row);
            $fields[$tokens[0]] = $tokens[1];
        }
        return $fields;
    }

    abstract public function post(array $data): array;

    abstract public function get(int $code): mixed;
    
    abstract public function put(array $data): array;

    abstract public function delete(int $code): array;
}
