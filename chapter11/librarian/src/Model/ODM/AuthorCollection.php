<?php
namespace Librarian\Model\ODM;
use Librarian\Util\Config;
use Librarian\Model\Author;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorCollection extends AbstractCollection
{
    public function save(Author $author)
    {	
        $insertOneResult = $this->database->authors->insertOne([
            'code' => $this->database->authors->countDocuments() + 1,
            'last_name' => $author->lastName,
            'middle_name' => $author->middleName,
            'first_name' => $author->firstName
        ]);
        return $insertOneResult->getInsertedCount() == 1;
    }

    public function update(int $code, array $data)
    {
        $result = $this->database->authors->updateOne(
            ['code' => $code],
            ['$set' => $data]
        );
        return $result->getModifiedCount() == 1;
    }

    public function delete(int $code)
    {
        $result = $this->database->authors->deleteOne(['code' => $code]);
        return $result->getDeletedCount() == 1;
    }
}