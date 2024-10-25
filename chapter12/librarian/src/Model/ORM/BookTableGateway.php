<?php
namespace Librarian\Model\ORM;
use Librarian\Model\Book;
use Librarian\Model\BookWriterInterface;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookTableGateway extends AbstractDatabase implements BookWriterInterface
{
    public function save(Book $book): bool
    {
        $sql = 'INSERT INTO books(title,author_code) values (:title,:authorcode)';    
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':title', $book->title);
        $statement->bindParam(':authorcode', $book->author->code);
        $statement->execute();
        return $statement->rowCount() == 1;
    }
  
    public function update(int $code, array $data): bool
    {        
        $placeholders = '';
        $fields = [];
        foreach($data as $field => $value){
            $placeholder = str_replace('_','',$field);
            $placeholders .= $field . " = :$placeholder,";
            $fields[$placeholder] = $value;
        }
        $placeholders = substr($placeholders,0,strlen($placeholders)-1);        
        $sql = 'UPDATE books SET ' . $placeholders;
        $statement = $this->pdo->prepare($sql);
        foreach($fields as $field => &$value){
            $statement->bindParam(":$field", $value);
        }
        $statement->execute();
        return $statement->rowCount() == 1;
    }

    public function delete(int $code): bool
    {        
        $sql = 'DELETE FROM books WHERE code = :code';
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':code', $code);
        $statement->execute();
        return $statement->rowCount() == 1;
    }    
}
