<?php
namespace Librarian\Model\Filesystem;
use Librarian\Util\Config;
use Librarian\Model\Book;
use Librarian\Model\Author;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookPlainTextFinder extends AbstractBookFilesystem
{
    public function readByCode(int $code): Book
    {
        $filepath = Config::get('book_plaintext_filepath');
        $handle = fopen($filepath,'r');
        $book = new Book();
        while(!feof($handle)){
            $row = fread($handle, self::ROW_LENGTH);
            $readCode = (int) substr($row,0,self::CODE_LENGTH);
            if ($readCode == $code){
                $book = new Book(
                    $code,
                    trim(substr($row,self::CODE_LENGTH,80)),
                    new Author((int)trim(substr($row,self::CODE_LENGTH+self::TITLE_LENGTH,4)))
                );
                break;
            }    
        }
        fclose($handle);
        return $book;
    }

    public function readAll(): BookRowSet
    {
        $filepath = Config::get('book_plaintext_filepath');
        $handle = fopen($filepath,'r');
        $books = new BookRowSet();
        while(!feof($handle)){
            $row = fread($handle, self::ROW_LENGTH);
            $book = new Book(
                (int) substr($row,0,self::CODE_LENGTH),
                trim(substr($row,self::CODE_LENGTH,self::TITLE_LENGTH)),
                new Author((int)(substr($row,self::CODE_LENGTH+self::TITLE_LENGTH,self::CODE_LENGTH)))
            );
            if ($book->code != 0) {
                $books->add($book);
            }
        }
        fclose($handle);
        return $books;
    }
}