<?php
namespace Librarian\Model\Filesystem;
use Librarian\Util\Config;
use Librarian\Model\Book;
use Librarian\Model\BookWriterInterface;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookCSV extends AbstractBookFilesystem implements BookWriterInterface
{
    public function save(Book $book): bool
    {
        $filepath = Config::getPathFile('book_csv_file');
        $handle = fopen($filepath,'a+');
        $code = 0;
        while (!feof($handle)){
            $row = fgetcsv($handle, null, ';');
            $code = $row[0] ?? $code;
        }
        $code++;
        $fields = [
            $this->formatField($code, 4),
            $this->formatField($book->title, 80),
            $this->formatField($book->author->code, 4)
        ];
        fputcsv($handle, $fields, ';');
        fclose($handle);
        $handle = fopen($filepath,'r');
        $found = false;
        $currentCode = 0;
        while (!feof($handle)) {
            $currentRow = fgetcsv($handle, null, ';');
            $currentCode = $currentRow[0] ?? $currentCode;
        }
        $found = ((int) $currentCode == $code);  
        fclose($handle);
        return $found;
    }
    
    public function update(int $code, array $data): bool
    {
        $sourcePath = Config::getPathFile('book_csv_file');
        $targetPath = str_replace('.csv','.tmp',$sourcePath);
        $sourceHandle = fopen($sourcePath,'r');
        $targetHandle = fopen($targetPath,'w');
        $changed = false;
        while(!feof($sourceHandle)){
            $row = fgetcsv($sourceHandle, null, ';');
            if (!is_array($row) || count($row) != 3) continue;
            $readCode = (int) $row[0];        
            if ($readCode == $code){
                $book = [
                    'code' => $code,
                    'title' => trim($row[1]),
                    'author_code' => trim($row[2])
                ];
                foreach($data as $key => $value){
                    $book[$key] = $value;
                }
                $row = [
                    $this->formatField($code,self::CODE_LENGTH),
                    $this->formatField($book['title'],self::TITLE_LENGTH),
                    $this->formatField($book['author_code'],self::CODE_LENGTH)
                ];
                $changed = true;
            }
            fputcsv($targetHandle,$row,';');
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
        $sourcePath = Config::getPathFile('book_csv_file');
        $targetPath = str_replace('.csv','.tmp',$sourcePath);
        $sourceHandle = fopen($sourcePath,'r');
        $targetHandle = fopen($targetPath,'w');
        $changed = false;
        while(!feof($sourceHandle)){
            $row = fgetcsv($sourceHandle, null, ';');
            if (!is_array($row) || count($row) != 3) continue;
            $readCode = (int) $row[0];        
            if ($readCode == $code){
                $changed = true;
                continue;
            }
            fputcsv($targetHandle,$row,';');
        }
        fclose($sourceHandle);
        fclose($targetHandle);
        unlink($sourcePath);
        copy($targetPath,$sourcePath);
        unlink($targetPath);
        return $changed;
    }  
}