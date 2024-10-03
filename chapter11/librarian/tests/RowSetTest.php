<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Model\AbstractRowSet;

/**
 * @covers AbstractRowSet
 * @covers ConcreteRowSet
*/
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(ConcreteRowSet::class)]
class RowSetTest extends TestCase
{
    public function testRowSet()
    {
        $concreteRowSet = new ConcreteRowSet();
        $object = new StdClass();
        $object->name = 'acme';
        $concreteRowSet->add($object);        
        $this->assertEquals('acme', $concreteRowSet->current()->name);
        $this->assertCount(1,$concreteRowSet->getRows());
    }
}

class ConcreteRowSet extends AbstractRowSet
{

}
