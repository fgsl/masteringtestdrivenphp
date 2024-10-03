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
class ValidationTest extends TestCase
{
    public function testAuthorNames()
    {
        $firstName = 'a';
        $middleName = 'bad';
        $lastName = 'author';

        $author = new Author(0,$firstName,$middleName,$lastName);
        $this->assertFalse($author->isValid());

        $firstName = 'one';
        $middleName = 'author';
        $lastName = 'with more words than this field can support';

        $author = new Author(0,$firstName,$middleName,$lastName);
        $this->assertFalse($author->isValid());

        $firstName = 'this';
        $middleName = 'is';
        $lastName = 'valid';

        $author = new Author(0,$firstName,$middleName,$lastName);

        $this->assertTrue($author->isValid());
    }

    public function testSaveInvalidAuthor()
    {
        $authorProxy = new AuthorProxy();
        $this->assertFalse($authorProxy->save('o','this is','invalid'));
        Entity::clear('author');
    }

    public function testBookTitles()
    {
        $title = 'hi';
        $book = new Book(0,$title);
        $this->assertFalse($book->isValid());
        $title = 'now';
        $book = new Book(0,$title);        
        $this->assertTrue($book->isValid());
    }    

    public function testSaveInvalidBook()
    {
        $bookProxy = new BookProxy();
        $this->assertFalse($bookProxy->save('hi',0));
        Entity::clear('book');
    }
}
