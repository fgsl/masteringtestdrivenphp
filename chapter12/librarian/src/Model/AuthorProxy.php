<?php
namespace Librarian\Model;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorProxy extends AbstractProxy
{
    public function save($lastName, $middleName, $firstName): bool
    {
        $author = new Author(0,$firstName,$middleName,$lastName);
        if (!$author->isValid()){
            return false;
        }
        try {
            $saved = ($this->getDataGateway())->save($author);
        } catch (\Throwable $th) {
            $saved = false;
        }

        return $saved;
    }
}
