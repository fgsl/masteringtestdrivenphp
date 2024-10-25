<?php
namespace Librarian\Model;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
abstract class AbstractProxy
{
    protected function getModelName(): string
    {
        $fullClassName = get_class($this);
        $parts = explode('\\',$fullClassName);
        $className = end($parts);
        return str_replace('Proxy','',$className);
    }    

    protected function getDataGateway(): object
    {
        $storageFormat = Config::get('storage_format');
        $modelName = $this->getModelName(); 
        try {
            $dataGateway = match($storageFormat){
                'txt' => "Librarian\Model\Filesystem\\{$modelName}PlainText",
                'csv' => "Librarian\Model\Filesystem\\{$modelName}CSV",
                'json' => "Librarian\Model\Filesystem\\{$modelName}JSON",
                'rdb' => "Librarian\Model\ORM\\{$modelName}TableGateway",
                'ddb' => "Librarian\Model\ODM\\{$modelName}Collection"
            };    
        } catch (\Throwable $th) {
            throw $th;
        }
        return new $dataGateway();
    }

    public function getFinder(): FinderInterface
    {
        $storageFormat = Config::get('storage_format');
        $modelName = $this->getModelName();
        try {
            $finder = match($storageFormat){
                'txt' => "Librarian\Model\Filesystem\\{$modelName}PlainTextFinder",
                'csv' => "Librarian\Model\Filesystem\\{$modelName}CSVFinder",
                'json' => "Librarian\Model\Filesystem\\{$modelName}JSONFinder",
                'rdb' => "Librarian\Model\ORM\\{$modelName}Finder",
                'ddb' => "Librarian\Model\ODM\\{$modelName}CollectionFinder"
            };
        } catch (\Throwable $th) {
            throw $th;
        }
        return new $finder();        
    }    

    public function getByCode($code): AbstractCode
    {
        try {
            $object = $this->getFinder()->readByCode($code);
        } catch(\Exception $e) {
            $model = __NAMESPACE__ . '\\' . $this->getModelName();
            $object = new $model();
        }
        return $object;
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
