<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Test\PHPServer;
use Librarian\Util\Config;
use Librarian\Model\ODM\AuthorCollection;
use Librarian\Model\ODM\BookCollection;
use Librarian\Model\ODM\AuthorCollectionFinder;
use Librarian\Model\ODM\BookCollectionFinder;
use Librarian\Model\ODM\Collection;
use Librarian\Model\AuthorProxy;
use Librarian\Model\BookProxy;
use Librarian\Model\Entity;

if (!class_exists('ViewDatabaseTest')){
    require_once 'ViewDatabaseTest.php';
}
/**
 * @covers AuthorProxy
 * @covers BookProxy
 * @covers AuthorCollection
 * @covers BookCollection
 * @covers AuthorCollectionFinder
 * @covers BookCollectionFinder
 * @covers AuthorCollection
 * @covers Collection
 * @covers Config
 * @covers Entity
 */
#[CoversClass(AuthorProxy::class)]
#[CoversClass(BookProxy::class)]
#[CoversClass(AuthorCollection::class)]
#[CoversClass(BookCollection::class)]
#[CoversClass(AuthorCollectionFinder::class)]
#[CoversClass(BookCollectionFinder::class)]
#[CoversClass(Collection::class)]
#[CoversClass(Config::class)]
#[CoversClass(Entity::class)]
class ViewCollectionTest extends ViewDatabaseTest
{
    public static function setUpBeforeClass(): void
    {
        putenv('LIBRARIAN_TEST_ENVIRONMENT=true');        
        Config::change('storage_format','ddb');
        PHPServer::getInstance()->start();
    }

    public static function tearDownAfterClass():void
    {
        Config::change('storage_format','txt');
        putenv('LIBRARIAN_TEST_ENVIRONMENT=false');
        PHPServer::getInstance()->stop();
    }
}
