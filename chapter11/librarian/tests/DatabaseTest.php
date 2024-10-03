<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Test\DatabaseTestInterface;
use Librarian\Model\ORM\Table;
use Librarian\Model\ORM\AuthorTableGateway;
use Librarian\Model\ORM\BookTableGateway;
use Librarian\Model\ORM\AuthorFinder;
use Librarian\Model\ORM\BookFinder;
use Librarian\Util\Config;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Model\ORM\AuthorRowGateway;
use Librarian\Model\ORM\BookRowGateway;
use Librarian\Filter\TagFilter;

/**
 * @covers Author
 * @covers Book
 * @covers AuthorRowSet
 * @covers BookRowSet
 * @covers Table
 * @covers AuthorTableGateway
 * @covers BookTableGateway
 * @covers AuthorFinder
 * @covers BookFinder
 * @covers Config
 * @covers AuthorRowGateway
 * @covers BookRowGateway 
 * @covers TagFilter
 */
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(Table::class)]
#[CoversClass(AuthorTableGateway::class)]
#[CoversClass(BookTableGateway::class)]
#[CoversClass(AuthorFinder::class)]
#[CoversClass(BookFinder::class)]
#[CoversClass(Config::class)]
#[CoversClass(AuthorRowGateway::class)]
#[CoversClass(BookRowGateway::class)]
#[CoversClass(TagFilter::class)]
class DatabaseTest extends TestCase implements DatabaseTestInterface
{
    public function testSaveAuthor()
    {
        $this->assertTrue((new AuthorTableGateway())->save(new Author(0,'George','Herbert','Wells')));
        (new Table('authors'))->truncate();
    }

    public function testReadAuthor()
    {
        (new AuthorTableGateway())->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        (new AuthorTableGateway())->save(new Author(0,'Francis','Scott','Fitzgerald'));
        (new AuthorTableGateway())->save(new Author(0,'Arthur','Conan','Doyle'));
        $author = (new AuthorFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        (new Table('authors'))->truncate();
    }

    public function testReadAuthors()
    {
        (new AuthorTableGateway())->save(new Author(0,'Mary','Wollstonecraft','Shelley'));
        (new AuthorTableGateway())->save(new Author(0,'Agatha','Mary','Christie'));
        (new AuthorTableGateway())->save(new Author(0,'Chaya','Pinkhasivna','Lispector'));
        $authors = (new AuthorFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        (new Table('authors'))->truncate();
    }
    
    public function testUpdateAuthor()
    {
        (new AuthorTableGateway())->save(new Author(0,'Guy','de','Maupassant'));
        (new AuthorTableGateway())->save(new Author(0,'Antoine','de','Saint-Exupéry'));
        (new AuthorTableGateway())->save(new Author(0,'Honoré','de','Balzac'));
        $author = (new AuthorFinder())->readByCode(1);
        $this->assertEquals('Guy',$author->firstName);
        (new AuthorTableGateway())->update(1,[
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = (new AuthorFinder())->readByCode(1);
        $this->assertEquals('Rudolf',$author->firstName);
        (new Table('authors'))->truncate();        
    }
    
    public function testDeleteAuthor()
    {
        (new AuthorTableGateway())->save(new Author(0,'Machado','de','Assis'));
        (new AuthorTableGateway())->save(new Author(0,'José','de','Alencar'));
        (new AuthorTableGateway())->save(new Author(0,'Rachel','de','Queiroz'));
        $author = (new AuthorFinder())->readByCode(2);
        $this->assertEquals('Alencar',$author->lastName);
        (new AuthorTableGateway())->delete(2);
        $author = (new AuthorFinder())->readByCode(2);
        $this->assertEmpty($author->code);
        (new Table('authors'))->truncate();
    }    
    // book tests
    public function testSaveBook()
    {
        (new AuthorTableGateway())->save(new Author(0,'George','Herbert','Wells'));
        $this->assertTrue((new BookTableGateway())->save(new Book(0,'The Time Machine',new Author(1))));
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
  
    public function testReadBook()
    {
        (new AuthorTableGateway())->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        (new BookTableGateway())->save(new Book(0,'Fausto',new Author(1)));
        $book = (new BookFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
    
    public function testReadBooks()
    {
        (new AuthorTableGateway())->save(new Author(0,'Agatha','Mary','Christie'));
        (new BookTableGateway())->save(new Book(0,'Murder on the Orient Express',new Author(1)));
        (new BookTableGateway())->save(new Book(0,'Death on the Nile',new Author(1)));
        (new BookTableGateway())->save(new Book(0,'Halloween Party',new Author(1))); 
        $books = (new BookFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
    
    public function testUpdateBook()
    {
        (new AuthorTableGateway())->save(new Author(0,'Honoré','de','Balzac'));
        (new BookTableGateway())->save(new Book(0,'La Vendetta',new Author(1)));
        (new BookTableGateway())->save(new Book(0,'Le Contrat de mariage',new Author(1)));
        (new BookTableGateway())->save(new Book(0,'La Femme de trente ans',new Author(1)));
        $book = (new BookFinder())->readByCode(2);
        $this->assertStringContainsString('mariage',$book->title);
        (new BookTableGateway())->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = (new BookFinder())->readByCode(2);
        $this->assertStringContainsString('vie',$book->title);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
   
    public function testDeleteBook()
    {
        (new AuthorTableGateway())->save(new Author(0,'José','de','Alencar'));
        (new BookTableGateway())->save(new Book(0,'O Guarani',new Author(1)));
        (new BookTableGateway())->save(new Book(0,'Iracema',new Author(1)));
        (new BookTableGateway())->save(new Book(0,'Ubirajara',new Author(1)));
        $book = (new BookFinder())->readByCode(2);
        $this->assertStringContainsString('Iracema',$book->title);
        (new BookTableGateway())->delete(2);
        $book = (new BookFinder())->readByCode(2);
        $this->assertEmpty($book->code);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
}
