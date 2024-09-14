<?php
namespace Librarian\Model;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class BookProxy
{
    private function getDataGateway(): object
    {
        $storageFormat = Config::get('storage_format');
    
        try {
            $dataGateway = match($storageFormat){
                'txt' => 'Librarian\Model\Filesystem\BookPlainText',
                'csv' => 'Librarian\Model\Filesystem\BookCSV',
                'json' => 'Librarian\Model\Filesystem\BookJSON',
                'rdb' => 'Librarian\Model\ORM\BookTableGateway',
                'ddb' => 'Librarian\Model\ODM\BookCollection'
            };    
        } catch (\Throwable $th) {
            throw $th;
        }
        return new $dataGateway();
    }

    public function save($title, $authorCode): bool
    {
        try {
            $saved = ($this->getDataGateway())->save($title, $authorCode);
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
                'txt' => 'Librarian\Model\Filesystem\BookPlainTextFinder',
                'csv' => 'Librarian\Model\Filesystem\BookCSVFinder',
                'json' => 'Librarian\Model\Filesystem\BookJSONFinder',
                'rdb' => 'Librarian\Model\ORM\BookFinder',
                'ddb' => 'Librarian\Model\ODM\BookCollectionFinder'
            };    
        } catch (\Throwable $th) {
            throw $th;
        }
        return new $finder();
    }    
    
    public function getByCode($code): Book
    {
        try {
            $book = $this->getFinder()->readByCode($code);        
        } catch(\Exception $e) {
            $book = new Book();
        }
        return $book;
    }
    
    public function update($code, $title, $authorCode): bool
    {
        $data = [
            'title' => $title,
            'author_code' => $authorCode
        ];

        try {
            $saved = ($this->getDataGateway())->update($code, $data);
        } catch (\Throwable $th) {
            $saved = false;
        }
        return $saved;
    }
    
    public function delete($code)
    {
        try {
            $deleted = ($this->getDataGateway())->delete($code);
        } catch (\Throwable $th) {
            $deleted = false;
        }
        return $deleted;
    }    
}
