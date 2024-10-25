<?php
namespace Librarian\Model\ORM;
use Librarian\Model\Author;
use Librarian\Model\AuthorWriterInterface;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorTableGateway extends AbstractDatabase implements AuthorWriterInterface
{
    public function save(Author $author): bool
    {
        $sql = 'INSERT INTO authors(first_name,middle_name,last_name) values (:firstname,:middlename,:lastname)';    
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':firstname', $author->firstName);
        $statement->bindParam(':middlename', $author->middleName);
        $statement->bindParam(':lastname', $author->lastName);
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
        $sql = 'UPDATE authors SET ' . $placeholders . ' WHERE code = :code';
        $statement = $this->pdo->prepare($sql);
        foreach($fields as $field => &$value){
            $statement->bindParam(":$field", $value);
        }
        $statement->bindParam(':code', $code);
        $statement->execute();
        return $statement->rowCount() == 1;  
    }

    public function delete(int $code): bool
    {        
        $sql = 'DELETE FROM authors WHERE code = :code';
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':code', $code);
        $statement->execute();
        return $statement->rowCount() == 1; 
    }    
}
