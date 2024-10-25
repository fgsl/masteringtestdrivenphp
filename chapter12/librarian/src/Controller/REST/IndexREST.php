<?php
namespace Librarian\Controller\REST;
use Librarian\Auth\Adapter;
use Librarian\Auth\Strategy\Api;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class IndexREST extends AbstractRESTController
{
    public function post(array $data): array
    {
        $username = $data['username'];
        $password = $data['password'];

        Config::override('auth_strategy','api');
        $authAdapter = new Adapter();
        $authAdapter->setIdentity($username)
        ->setCredential($password);
        $authAdapter->authenticate();
        $response = ['token' => 'invalid'];
        if ($authAdapter->isValid()){
            $response = ['token' => API::$token];
        }
        return $response;
    }

    public function get(int $code): mixed
    {
        return ['error' => 'invalid'];
    }

    public function put(array $data): array
    {
        return ['error' => 'invalid'];
    }   
    
    public function delete(int $code): array    
    {
        return ['error' => 'invalid'];
    }
}
