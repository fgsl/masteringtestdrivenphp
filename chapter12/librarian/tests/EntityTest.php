<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Model\Entity;
use Librarian\Util\Config;
use Librarian\Model\ODM\Collection;
use Librarian\Model\ORM\Table;

/**
 * @covers Author
 * @covers Collection
 * @covers Table
 * @covers Config
*/
#[CoversClass(Entity::class)]
#[CoversClass(Collection::class)]
#[CoversClass(Table::class)]
#[CoversClass(Config::class)]
class EntityTest extends TestCase
{
    public function testEntity()
    {
        $formats = ['txt','csv','json','rdb','ddb'];

        foreach($formats as $format){
            Config::override('storage_format',$format);
            Entity::clear('author');
            Entity::clear('book');
    
            $this->assertEquals($format, Config::get('storage_format'));
        }
        Config::override('storage_format','txt');
        $this->assertEquals('txt', Config::get('storage_format'));
    }
}
