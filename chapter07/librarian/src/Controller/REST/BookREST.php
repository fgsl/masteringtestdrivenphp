<?php
namespace Librarian\Controller\REST;
use Librarian\Model\PDO\BookPDO;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookREST
{
    private BookPDO $bookPDO;

    public function __construct()
    {
        $this->bookPDO = new BookPDO();
    }

    public function post(array $data): array
    {
        $title = $data['title'];
        $authorCode = (int) $data['author_code'];

        $response = ['included' => false];

        if ($this->bookPDO->save($title, $authorCode))
        {
            $response = ['included' => true];
        }
        return $response;
    }

    public function get(int $code): array
    {
        if ($code == 0) {
            return $this->bookPDO->readBooks();
        }
        return $this->bookPDO->readByCode($code);
    }
    
    public function put(array $data): array
    {
        $code = (int)$data['code'];
        unset($data['code']);
        $response = ['updated' => false];
        if ($this->bookPDO->update($code, $data))
        {
            $response = ['updated' => true];
        }
        return $response;
    }

    public function delete(int $code): array
    {
        $response = ['deleted' => false];
        if ($this->bookPDO->delete($code))
        {
            $response = ['deleted' => true];
        }
        return $response;
    }
}
