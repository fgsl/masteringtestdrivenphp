<?php
namespace Librarian\Model;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookProxy extends AbstractProxy
{
    public function save($title, $authorCode): bool
    {
        $book = new Book(0,$title,new Author($authorCode));
        if (!$book->isValid()){
            return false;
        }
        try {
            $saved = ($this->getDataGateway())->save($book);
        } catch (\Throwable $th) {
            $saved = false;
        }
        return $saved;
    }
}
