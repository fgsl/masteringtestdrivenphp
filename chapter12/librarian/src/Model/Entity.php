<?php
namespace Librarian\Model;
use Librarian\Util\Config;
use Librarian\Model\ODM\Collection;
use Librarian\Model\ORM\Table;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
final class Entity
{
    public static function clear(string $entity)
    {
        $storageFormat = Config::get('storage_format');
    
        $path = '';
        switch($storageFormat){
            case 'txt':
            case 'csv':
            case 'json':
                $path = self::getPathForFile($entity);
                if (file_exists($path)) { unlink($path); }
                break;
            case 'rdb':
                (new Table($entity . 's'))->truncate();
                break;
            case 'ddb':
                (new Collection($entity . 's'))->drop();            
        }
    }
    
    private static function getPathForFile(string $entity)
    {
        $storageFormat = Config::get('storage_format');
    
        $file = '';
        switch($storageFormat){
            case 'txt':
                $file = Config::get($entity . '_plaintext_file');
                break;
            case 'csv':
                $file = Config::get($entity . '_csv_file');
                break;
            case 'json':
                $file = Config::get($entity . '_json_file');
        }
        return __DIR__ . '/../../' . $file;
    }
        
}
