<?php
namespace Librarian\Model;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorRowSet extends AbstractRowSet
{
    public function __construct(array $rows = [])
    {
        foreach($rows as $row){
            $this->rows[] = new Author(
                (int)$row['code'],
                $row['first_name'],
                $row['middle_name'],
                $row['last_name']
            );
        }
    }
}
