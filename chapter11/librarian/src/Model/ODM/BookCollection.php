<?php
namespace Librarian\Model\ODM;
use Librarian\Util\Config;
use Librarian\Model\Book;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookCollection extends AbstractCollection
{
    public function save(Book $book)
    {	
        $insertOneResult = $this->database->books->insertOne([
            'code' => $this->database->books->countDocuments() + 1,
            'title' => $book->title,
            'author_code' => $book->author->code        
        ]);
        return $insertOneResult->getInsertedCount() == 1;    
    }

    public function update(int $code, array $data)
    {
        $result = $this->database->books->updateOne(
            ['code' => $code],
            ['$set' => $data]
        );
        return $result->getModifiedCount() == 1;
    }

    public function delete(int $code)
    {
        $result = $this->database->books->deleteOne(['code' => $code]);
        return $result->getDeletedCount() == 1;
    }
}