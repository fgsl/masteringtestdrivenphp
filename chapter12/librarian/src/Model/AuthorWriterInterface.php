<?php
namespace Librarian\Model;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
interface AuthorWriterInterface extends WriterInterface 
{
    public function save(Author $author): bool;
}
