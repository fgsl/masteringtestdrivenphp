<?php
namespace Librarian\Model\Filesystem;
use Librarian\Util\Config;
use Librarian\Model\Author;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorCSVFinder extends AbstractAuthorFilesystem
{
    public function readByCode(int $code): Author
    {
        $filepath = Config::get('author_csv_filepath');
        $handle = fopen($filepath,'r');
        $author = new Author();
        while(!feof($handle)){
            $row = fgetcsv($handle, null, ';');
            $readCode = (int) is_array($row) && isset($row[0]) ? $row[0] : 0;
            if ($readCode == $code){
                $author = new Author(
                    $code,
                    trim($row[3]),
                    trim($row[2]),
                    trim($row[1])
                );
                break;
            }
        }
        fclose($handle);
        return $author;
    }

    public function readAll(): AuthorRowSet
    {
        $filepath = Config::get('author_csv_filepath');
        $handle = fopen($filepath,'r');
        $authors = new AuthorRowSet();
        while(!feof($handle)){
            $row = fgetcsv($handle, null, ';');
            if (!is_array($row) || count($row) != self::CODE_LENGTH) continue;
            $author = new Author(
                (int) $row[0],
                trim($row[3]),
                trim($row[2]),
                trim($row[1])
            );
            $authors->add($author);
        }
        fclose($handle);
        return $authors;
    }
}
