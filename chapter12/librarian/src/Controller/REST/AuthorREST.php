<?php
namespace Librarian\Controller\REST;
use Librarian\Model\AuthorProxy;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorREST extends AbstractRESTController
{
    private AuthorProxy $authorProxy;

    public function __construct()
    {
        $this->authorProxy = new AuthorProxy();
    }

    public function post(array $data): array
    {
        $firstName = $data['first_name'];
        $middleName = $data['middle_name'];
        $lastName = $data['last_name'];

        $response = ['included' => false];

        if ($this->authorProxy->save($lastName, $middleName, $firstName))
        {
            $response = ['included' => true];
        }
        return $response;
    }

    public function get(int $code): mixed
    {
        if ($code == 0) {
            return $this->authorProxy->getFinder()->readAll()->getRows();
        }
        return $this->authorProxy->getByCode($code);
    }
    
    public function put(array $data): array
    {        
        $code = (int) $data['code'];
        unset($data['code']);
        $response = ['updated' => false];
        if ($this->authorProxy->update($code, $data))
        {
            $response = ['updated' => true];
        }
        return $response;
    }

    public function delete(int $code): array
    {
        $response = ['deleted' => false];
        if ($this->authorProxy->delete($code))
        {
            $response = ['deleted' => true];
        }
        return $response;
    }
}
