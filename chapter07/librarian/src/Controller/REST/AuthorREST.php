<?php
namespace Librarian\Controller\REST;
use Librarian\Model\PDO\AuthorPDO;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorREST
{
    private AuthorPDO $authorPDO;

    public function __construct()
    {
        $this->authorPDO = new AuthorPDO();
    }

    public function post(array $data): array
    {
        $firstName = $data['first_name'];
        $middleName = $data['middle_name'];
        $lastName = $data['last_name'];

        $response = ['included' => false];

        if ($this->authorPDO->save($firstName, $middleName, $lastName))
        {
            $response = ['included' => true];
        }
        return $response;
    }

    public function get(int $code): array
    {
        if ($code == 0) {
            return $this->authorPDO->readAuthors();            
        }
        return $this->authorPDO->readByCode($code);
    }
    
    public function put(array $data): array
    {        
        $code = (int) $data['code'];
        unset($data['code']);
        $response = ['updated' => false];
        if ($this->authorPDO->update($code, $data))
        {
            $response = ['updated' => true];
        }
        return $response;
    }

    public function delete(int $code): array
    {
        $response = ['deleted' => false];
        if ($this->authorPDO->delete($code))
        {
            $response = ['deleted' => true];
        }
        return $response;
    }
}
