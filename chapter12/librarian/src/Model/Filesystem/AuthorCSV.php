<?php
namespace Librarian\Model\Filesystem;
use Librarian\Util\Config;
use Librarian\Model\Author;
use Librarian\Model\AuthorWriterInterface;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorCSV extends AbstractAuthorFilesystem implements AuthorWriterInterface
{
    public function save(Author $author): bool
    {
        $filepath = Config::getPathFile('author_csv_file');
        $handle = fopen($filepath,'a+');
        $code = 0;
        while (!feof($handle)){
            $row = fgetcsv($handle, null, ';');
            $code = $row[0] ?? $code;
        }
        $code++;
        $fields = [
            $this->formatField($code, 4),
            $this->formatField($author->lastName, 20),
            $this->formatField($author->middleName, 20),
            $this->formatField($author->firstName, 20)
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
        $sourcePath = Config::getPathFile('author_csv_file');
        $targetPath = str_replace('.csv','.tmp',$sourcePath);
        $sourceHandle = fopen($sourcePath,'r');
        $targetHandle = fopen($targetPath,'w');
        $changed = false;
        while(!feof($sourceHandle)){
            $row = fgetcsv($sourceHandle, null, ';');
            if (!is_array($row) || count($row) != self::CODE_LENGTH) continue;
            $readCode = (int) $row[0];        
            if ($readCode == $code){
                $author = [
                    'code' => $code,
                    'last_name' => trim($row[1]),
                    'middle_name' => trim($row[2]),
                    'first_name' => trim($row[3]),
                ];
                foreach($data as $key => $value){
                    $author[$key] = $value;
                }
                $row = [
                    $this->formatField($code,self::CODE_LENGTH),
                    $this->formatField($author['last_name'],self::CODE_LENGTH,self::NAME_LENGTH),
                    $this->formatField($author['middle_name'],self::NAME_LENGTH),
                    $this->formatField($author['first_name'],self::NAME_LENGTH)
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
        $sourcePath = Config::getPathFile('author_csv_file');
        $targetPath = str_replace('.csv','.tmp',$sourcePath);
        $sourceHandle = fopen($sourcePath,'r');
        $targetHandle = fopen($targetPath,'w');
        $changed = false;
        while(!feof($sourceHandle)){
            $row = fgetcsv($sourceHandle, null, ';');
            if (!is_array($row) || count($row) != self::CODE_LENGTH) continue;
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
