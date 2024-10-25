<?php
namespace Librarian\Controller;
use Librarian\Util\Config;
use Librarian\Model\AbstractCode;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
trait CRUDControllerTrait
{
    public function list(): void
    {
        
    }

    public function message(): void
    {
        
    }

    protected function getDataGateway()
    {
        $model = $this->getModel();
        $storageFormat = Config::get('storage_format');        
        return 'Librarian\Model\\' .  match($storageFormat){
            'txt' => "Filesystem\\{$model}PlainText",
            'csv' => "Filesystem\\{$model}CSV",
            'json' => "Filesystem\\{$model}JSON",
            'rdb' => "ORM\\{$model}TableGateway",
            'ddb' => "ODM\\{$model}Collection"
        };
    }

    protected function getModel()
    {
        $fullClassName = get_class($this);
        $parts = explode('\\',$fullClassName);
        return end($parts);
    }

    public function delete()
    {
        $code = $_GET['code'] ?? 0;
        $message = 'The record has not been deleted';
        if ($code == 0){
            $message = 'It cannot delete what does not exist';
        }
        if ($code <> 0 && $this->remove($code)) {
            $message = 'Record deleted successfully!';
        }
        $route = lcfirst($this->getModel());
        $this->redirect('author','message',['message' => base64_encode($message)]);         
    }

    protected function remove(int $code)    
    {
        $storageFormat = Config::get('storage_format');
    
        $deleted = false;
        $dataGateway = $this->getDataGateway();

        try {
            return (new $dataGateway())->delete($code);
        } catch (\Exception $e){
            return false;
        }
    }

    protected function getByCode($code): AbstractCode
    {
        $model = $this->getModel();
        $finderMethod = "get{$model}Finder";
        $modelClass = "Librarian\Model\{$model}";
        try {
            $entity = $this->$finderMethod()->readByCode($code);
        } catch(\Exception $e) {
            $entity = new $modelClass();
        }
        return $entity;
    }
}
