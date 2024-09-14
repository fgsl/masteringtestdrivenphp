<?php
namespace Librarian\Controller\REST;
use Librarian\Model\BookProxy;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookREST extends AbstractRESTController
{
    private BookProxy $bookProxy;

    public function __construct()
    {
        $this->bookProxy = new BookProxy();
    }

    public function post(array $data): array
    {
        $title = $data['title'];
        $authorCode = (int) $data['author_code'];

        $response = ['included' => false];

        if ($this->bookProxy->save($title, $authorCode))
        {
            $response = ['included' => true];
        }
        return $response;
    }

    

    public function get(int $code): mixed
    {
        if ($code == 0) {
            return $this->bookProxy->getFinder()->readAll()->getRows();
        }
        return $this->bookProxy->getByCode($code);
    }
    
    public function put(array $data): array
    {
        $code = (int)$data['code'];
        $response = ['updated' => false];
        if ($this->bookProxy->update($code, $data['title'],$data['author_code']))
        {
            $response = ['updated' => true];
        }
        return $response;
    }

    public function delete(int $code): array
    {
        $response = ['deleted' => false];
        if ($this->bookProxy->delete($code))
        {
            $response = ['deleted' => true];
        }
        return $response;
    }
}
