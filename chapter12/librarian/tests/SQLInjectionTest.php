<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Validator\NameValidator;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Model\AuthorProxy;
use Librarian\Model\BookProxy;
use Librarian\Model\Entity;
use Librarian\Util\Config;
use Librarian\Filter\TagFilter;
use Librarian\Validator\TitleValidator;
use Librarian\Model\ORM\DatabaseTrait;

/**
 * @covers TagFilter
 * @covers Author
 * @covers Book
 * @covers AuthorProxy
 * @covers BookProxy
 * @covers Entity
 * @covers Config
 * @covers NameValidator
 * @covers TitleValidator
*/
#[CoversClass(TagFilter::class)]
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AuthorProxy::class)]
#[CoversClass(BookProxy::class)]
#[CoversClass(Entity::class)]
#[CoversClass(Config::class)]
#[CoversClass(NameValidator::class)]
#[CoversClass(TitleValidator::class)]
class SQLInjectionTest extends TestCase
{
    use DatabaseTrait;

    public function testDropTableInjection()
    {
        $authorProxy = new AuthorProxy();
        $authorProxy->save('Roberts','of','Son');
        $title = '); DROP TABLE books;-';
        $bookProxy = new BookProxy();
        $bookProxy->save($title,1);

        $this->initDatabase();
        $stmt = $this->pdo->query("select * from information_schema.tables where table_schema = 'librarian' and table_name = 'books' limit 1");
        $rows = $stmt->fetchAll();
        $this->assertEquals(1,count($rows));

        Entity::clear('book');
        Entity::clear('author');
    }

}
