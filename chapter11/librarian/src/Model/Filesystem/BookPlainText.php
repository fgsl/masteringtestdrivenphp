<?php
namespace Librarian\Model\Filesystem;
use Librarian\Util\Config;
use Librarian\Model\Book;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookPlainText extends AbstractBookFilesystem
{
    public function save(Book $book): bool
    {
        $filepath = Config::getPathFile('book_plaintext_file');
        $handle = fopen($filepath,'a+');
        fseek($handle, -self::ROW_LENGTH, SEEK_END);
        $row = fread($handle, self::ROW_LENGTH);
        $code = (int) substr($row, 0, 4);
        $code++;
        $row = $this->formatField($code, self::CODE_LENGTH) . $this->formatField($book->title, self::TITLE_LENGTH) . 
        $this->formatField($book->author->code, self::CODE_LENGTH) . "\n";
        fwrite($handle,$row);
        fclose($handle);
        $handle = fopen($filepath,'r');
        $found = false;
        fseek($handle, -self::ROW_LENGTH, SEEK_END);
        $currentRow = fread($handle, self::ROW_LENGTH);
        $found = ($currentRow == $row);
        fclose($handle);
        return $found;
    }

    public function update(int $code, array $data): bool
    {
        $sourcePath = Config::getPathFile('book_plaintext_file');
        $targetPath = str_replace('.txt','.tmp',$sourcePath);
        $sourceHandle = fopen($sourcePath,'r');
        $targetHandle = fopen($targetPath,'w');
        $changed = false;
        while(!feof($sourceHandle)){
            $row = fread($sourceHandle, self::ROW_LENGTH);
            $readCode = (int) substr($row,0,self::CODE_LENGTH);
            if ($readCode == $code){
                $book = [
                    'code' => $code,
                    'title' => trim(substr($row,self::CODE_LENGTH,self::TITLE_LENGTH)),
                    'author_code' => trim(substr($row,self::CODE_LENGTH+self::TITLE_LENGTH,self::CODE_LENGTH))
                ];
                foreach($data as $key => $value){
                    $book[$key] = $value;
                }
                $row = $this->formatField($code,self::CODE_LENGTH) . $this->formatField($book['title'],80) .
                $this->formatField($book['author_code'], self::CODE_LENGTH) . "\n";
                $changed = true;
            }
            fwrite($targetHandle,$row,self::ROW_LENGTH);
        }
        fclose($sourceHandle);
        fclose($targetHandle);
        unlink($sourcePath);
        copy($targetPath,$sourcePath);
        unlink($targetPath);
        return $changed;
    }

    public function delete(int $code): bool
    {
        $sourcePath = Config::getPathFile('book_plaintext_file');
        $targetPath = str_replace('.txt','.tmp',$sourcePath);
        $sourceHandle = fopen($sourcePath,'r');
        $targetHandle = fopen($targetPath,'w');
        $changed = false;
        while(!feof($sourceHandle)){
            $row = fread($sourceHandle, self::ROW_LENGTH);
            $readCode = (int) substr($row,0,self::CODE_LENGTH);
            if ($readCode == $code) {
                $changed = true;
                continue;
            }
            fwrite($targetHandle,$row,self::ROW_LENGTH);
        }
        fclose($sourceHandle);
        fclose($targetHandle);
        unlink($sourcePath);
        copy($targetPath,$sourcePath);
        unlink($targetPath);
        return $changed;
    }
}