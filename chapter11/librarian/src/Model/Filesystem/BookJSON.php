<?php
namespace Librarian\Model\Filesystem;
use Librarian\Util\Config;
use Librarian\Model\Book;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookJSON extends AbstractBookFilesystem
{
    public function save(Book $book): bool
    {
        $filepath = Config::getPathFile('book_json_file');
        $handle = fopen($filepath,'a');
        fclose($handle);
        $json = json_decode(file_get_contents($filepath));
        if ($json == NULL){
            $json = [];
        }
        $code = 0;
        foreach ($json as $row) {
            $code = $row->code;
        }
        $code++;
        $dict = [ 
            'code' => $code,
            'title'  => $book->title,
            'author_code' => $book->author->code,
        ];
        $json[] = $dict;
        $text = json_encode($json);
        file_put_contents($filepath, $text);
        $json = json_decode(file_get_contents($filepath));
        $found = false;
        $currentCode = 0;
        foreach ($json as $row) {
            $currentCode = $row->code;
        }
        $found = ($currentCode == $code);
        return $found;
    }

    public function update(int $code, array $data): bool
    {
        $sourcePath = Config::getPathFile('book_json_file');
        $content = file_get_contents($sourcePath);
        $books = json_decode($content);
        $changed = false;
        foreach($books as $index => $book) {
            if ((int) $book->code == $code) {
                foreach($data as $key => $value){
                    $book->$key = $value;
                }
                $books[$index] = $book;
                $changed = true;
            }
        }
        $targetPath = str_replace('.json','.tmp',$sourcePath);
        file_put_contents($targetPath,json_encode($books));
        unlink($sourcePath);
        copy($targetPath,$sourcePath);
        unlink($targetPath);  
        return $changed;
    }
    
    public function delete(int $code): bool
    {
        $sourcePath = Config::getPathFile('book_json_file');
        $content = file_get_contents($sourcePath);
        $books = json_decode($content);
        $changed = false;
        foreach($books as $index => $book) {
            if ((int) $book->code == $code) {
                unset($books[$index]);
            }
            $changed = true;
        }
        $targetPath = str_replace('.json','.tmp',$sourcePath);
        file_put_contents($targetPath,json_encode($books));
        unlink($sourcePath);
        copy($targetPath,$sourcePath);
        unlink($targetPath);  
        return $changed;
    }    
}