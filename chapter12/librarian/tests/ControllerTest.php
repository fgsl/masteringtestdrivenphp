<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Controller\Author;
use Librarian\Controller\Book;
use Librarian\Controller\Index;
use Librarian\Controller\AbstractPageController;
use Librarian\Util\Config;
use Librarian\Model\Author as AuthorModel;
use Librarian\Model\Book as BookModel;
use Librarian\Model\Filesystem\AuthorPlainTextFinder;
use Librarian\Model\Filesystem\BookPlainTextFinder;
use Librarian\Filter\TagFilter;

/**
 * @covers Author
 * @covers Book
 * @covers Index
 * @covers AbstractPageController
 * @covers AuthorModel
 * @covers BookModel
 * @covers Config
 * @covers AuthorPlainTextFinder
 * @covers BookPlainTextFinder
 * @covers TagFilter
*/
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(Index::class)]
#[CoversClass(AbstractPageController::class)]
#[CoversClass(AuthorModel::class)]
#[CoversClass(BookModel::class)]
#[CoversClass(Config::class)]
#[CoversClass(AuthorPlainTextFinder::class)]
#[CoversClass(BookPlainTextFinder::class)]
#[CoversClass(TagFilter::class)]
class ControllerTest extends TestCase
{
    public static function setUpBeforeClass():void 
    {
        Config::override('storage_format','txt');
    }

    public function testAuthorController()
    {
        $author = new Author();
        $this->assertNull($author->list());
        $this->assertNull($author->edit());
        //$this->assertNull($author->save());
        //$this->assertNull($author->message());
        //$this->assertNull($author->insert());
        //$this->assertNull($author->update());
        //$this->assertNull($author->delete());
    }

    public function testBookController()
    {
        $book = new Book();
        $this->assertNull($book->list());
        $this->assertNull($book->edit());
        //$this->assertNull($book->save());
        //$this->assertNull($book->message());
        //$this->assertNull($book->insert());
        //$this->assertNull($book->update());
        //$this->assertNull($book->delete());
    }

    public function testIndexController()
    {
        $index = new Index();
        $this->assertNull($index->index());
        //$this->assertNull($book->save());
        //$this->assertNull($book->message());
        //$this->assertNull($book->insert());
        //$this->assertNull($book->update());
        //$this->assertNull($book->delete());
    }  

}
