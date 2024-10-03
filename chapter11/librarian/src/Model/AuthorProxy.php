<?php
namespace Librarian\Model;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class AuthorProxy
{
    private function getDataGateway(): object
    {
        $storageFormat = Config::get('storage_format'); 
        try {
            $dataGateway = match($storageFormat){
                'txt' => 'Librarian\Model\Filesystem\AuthorPlainText',
                'csv' => 'Librarian\Model\Filesystem\AuthorCSV',
                'json' => 'Librarian\Model\Filesystem\AuthorJSON',
                'rdb' => 'Librarian\Model\ORM\AuthorTableGateway',
                'ddb' => 'Librarian\Model\ODM\AuthorCollection'
            };    
        } catch (\Throwable $th) {
            throw $th;
        }
        return new $dataGateway();
    }

    public function save($lastName, $middleName, $firstName): bool
    {
        $author = new Author(0,$firstName,$middleName,$lastName);
        if (!$author->isValid()){
            return false;
        }
        try {
            $saved = ($this->getDataGateway())->save($author);
        } catch (\Throwable $th) {
            $saved = false;
        }

        return $saved;
    }

    public function getFinder(): FinderInterface
    {
        $storageFormat = Config::get('storage_format');
        
        try {
            $finder = match($storageFormat){
                'txt' => 'Librarian\Model\Filesystem\AuthorPlainTextFinder',
                'csv' => 'Librarian\Model\Filesystem\AuthorCSVFinder',
                'json' => 'Librarian\Model\Filesystem\AuthorJSONFinder',
                'rdb' => 'Librarian\Model\ORM\AuthorFinder',
                'ddb' => 'Librarian\Model\ODM\AuthorCollectionFinder'
            };
        } catch (\Throwable $th) {
            throw $th;
        }
        return new $finder();        
    }    

    public function getByCode($code): Author
    {
        try {
            $author = $this->getFinder()->readByCode($code);
        } catch(\Exception $e) {
            $author = new Author();
        }
        return $author;
    }
    
    public function update($code, $data): bool
    {
        try {
            $saved = ($this->getDataGateway())->update($code, $data);
        } catch (\Throwable $th) {
            $saved = false;
        }
        return $saved;
    }
    
    public function delete($code): bool
    {
        try {
            $deleted = ($this->getDataGateway())->delete($code);
        } catch (\Throwable $th) {
            $deleted = false;
        }
        return $deleted;
    }
}
