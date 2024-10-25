<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Librarian\Model\AbstractRowSet;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Model\ORM\AuthorRowGateway;
use Librarian\Model\ORM\BookRowGateway;
use Librarian\Model\ORM\AuthorFinder;
use Librarian\Model\ORM\BookFinder;
use Librarian\Model\ORM\Table;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Util\Config;
use Librarian\Filter\TagFilter;

/**
 * @covers Table
 * @covers Author
 * @covers Book 
 * @covers AbstractRowSet
 * @covers AuthorRowSet
 * @covers BookRowSet 
 * @covers AuthorRowGateway
 * @covers BookRowGateway
 * @covers AuthorFinder
 * @covers BookFinder
 * @covers Config
 * @covers TagFilter
 */
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(Table::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(AuthorRowGateway::class)]
#[CoversClass(BookRowGateway::class)]
#[CoversClass(AuthorFinder::class)]
#[CoversClass(BookFinder::class)]
#[CoversClass(Config::class)]
#[CoversClass(TagFilter::class)]
class RowGatewayTest extends TestCase
{
    // author tests
    public function testSaveAuthor()
    {
        $this->assertTrue((new AuthorRowGateway(0,'George', 'Herbert', 'Wells'))->save());
        (new Table('authors'))->truncate();
    }

    public function testReadAuthor()
    {
        (new AuthorRowGateway(0,'Johann', 'Wolfgang', 'Von Goethe'))->save();
        (new AuthorRowGateway(0, 'Francis', 'Scott', 'Fitzgerald'))->save();
        (new AuthorRowGateway(0, 'Arthur', 'Conan', 'Doyle'))->save();
        $author = (new AuthorFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        (new Table('authors'))->truncate();
    }

    public function testReadAuthors()
    {
        (new AuthorRowGateway(0, 'Mary', 'Wollstonecraft', 'Shelley'))->save();
        (new AuthorRowGateway(0, 'Agatha', 'Mary', 'Christie'))->save();
        (new AuthorRowGateway(0, 'Chaya', 'Pinkhasivna', 'Lispector'))->save();
        $authors = (new AuthorFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        (new Table('authors'))->truncate();
    }
    
    public function testUpdateAuthor()
    {
        (new AuthorRowGateway(0, 'Guy', 'de', 'Maupassant'))->save();
        (new AuthorRowGateway(0, 'Antoine', 'de', 'Saint-Exupéry'))->save();
        (new AuthorRowGateway(0, 'Honoré', 'de', 'Balzac'))->save();
        $finder = new AuthorFinder();
        $author = $finder->readByCode(1);
        $this->assertEquals('Guy',$author->firstName);
        $author->firstName = 'Rudolf';
        $author->middleName = 'Erich';
        $author->lastName = 'Raspe';
        $author->update();
        $author = $finder->readByCode(1);
        $this->assertEquals('Rudolf',$author->firstName);
        (new Table('authors'))->truncate();
    }
   
    public function testDeleteAuthor()
    {
        (new AuthorRowGateway(0, 'Machado', 'de', 'Assis'))->save();
        (new AuthorRowGateway(0, 'José', 'de', 'Alencar'))->save();
        (new AuthorRowGateway(0, 'Rachel', 'de', 'Queiroz'))->save();
        $finder = new AuthorFinder();
        $author = $finder->readByCode(2);
        $this->assertEquals('Alencar',$author->lastName);
        $author->delete();
        $author = $finder->readByCode(2);
        $this->assertTrue($author->isEmpty());
        (new Table('authors'))->truncate();
    }    
    // book tests
    public function testSaveBook()
    {
        (new AuthorRowGateway(0, 'Herbert','George','Wells'))->save();
        $this->assertTrue((new BookRowGateway(0, 'The Time Machine', new Author(1)))->save());
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
  
    public function testReadBook()
    {
        (new AuthorRowGateway(0, 'Johann','Wolfgang','Von Goethe'))->save();
        (new BookRowGateway(0, 'Fausto', new Author(1)))->save();
        $book = (new BookFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
    
    public function testReadBooks()
    {
        (new AuthorRowGateway(0, 'Agatha','Mary','Christie'))->save();
        (new BookRowGateway(0, 'Murder on the Orient Express', new Author(1)))->save();
        (new BookRowGateway(0, 'Death on the Nile', new Author(1)))->save();
        (new BookRowGateway(0, 'Halloween Party', new Author(1)))->save(); 
        $books = (new BookFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
    
    public function testUpdateBook()
    {
        (new AuthorRowGateway(0, 'Honoré','de','Balzac'))->save();
        (new BookRowGateway(0, 'La Vendetta', new Author(1)))->save();
        (new BookRowGateway(0, 'Le Contrat de mariage', new Author(1)))->save();
        (new BookRowGateway(0, 'La Femme de trente ans', new Author(1)))->save();
        $finder = new BookFinder();
        $book = $finder->readByCode(2);
        $this->assertStringContainsString('mariage',$book->title);
        $book->title = 'Un début dans la vie';
        $book->update();
        $book = $finder->readByCode(2);
        $this->assertStringContainsString('vie',$book->title);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
   
    public function testDeleteBook()
    {
        (new AuthorRowGateway(0, 'José','de','Alencar'))->save();
        (new BookRowGateway(0, 'O Guarani', new Author(1)))->save();
        (new BookRowGateway(0, 'Iracema', new Author(1)))->save();
        (new BookRowGateway(0, 'Ubirajara', new Author(1)))->save();
        $finder = new BookFinder();
        $book = $finder->readByCode(2);
        $this->assertStringContainsString('Iracema',$book->title);
        $book->delete();
        $book = $finder->readByCode(2);
        $this->assertTrue($book->isEmpty());
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
}
