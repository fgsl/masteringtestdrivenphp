<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Util\Config;
use Librarian\Model\Entity;
use Librarian\Controller\REST\AuthorREST;
use Librarian\Controller\REST\BookREST;
use Librarian\Model\AuthorProxy;
use Librarian\Model\BookProxy;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Model\AbstractRowSet;
use Librarian\Model\Filesystem\AbstractAuthorFilesystem;
use Librarian\Model\Filesystem\AbstractBookFilesystem;
use Librarian\Model\Filesystem\AuthorPlainText;
use Librarian\Model\Filesystem\AuthorPlainTextFinder;
use Librarian\Model\Filesystem\BookPlainText;
use Librarian\Model\Filesystem\BookPlainTextFinder;
use Librarian\Filter\TagFilter;
use Librarian\Validator\NameValidator;
use Librarian\Validator\TitleValidator;

/**
 * @covers AuthorREST
 * @covers BookREST
 * @covers Author
 * @covers Book
 * @covers AuthorRowSet
 * @covers BookRowSet
 * @covers AuthorProxy
 * @covers BookProxy
 * @covers Entity
 * @covers AuthorREST
 * @covers BookREST
 * @covers Config
 * @covers AbstractRowSet
 * @covers AbstractAuthorFilesystem
 * @covers AbstractBookFilesystem
 * @covers AuthorPlainText
 * @covers AuthorPlainTextFinder
 * @covers BookPlainText
 * @covers BookPlainTextFinder
 * @covers TagFilter
 * @covers NameValidator
 * @covers TitleValidator
 */
#[CoversClass(AuthorREST ::class)]
#[CoversClass(BookREST::class)]
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(AuthorProxy::class)]
#[CoversClass(BookProxy::class)]
#[CoversClass(Entity::class)]
#[CoversClass(Config::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AbstractAuthorFilesystem::class)]
#[CoversClass(AbstractBookFilesystem::class)]
#[CoversClass(AuthorPlainText::class)]
#[CoversClass(AuthorPlainTextFinder::class)]
#[CoversClass(BookPlainText::class)]
#[CoversClass(BookPlainTextFinder::class)]
#[CoversClass(TagFilter::class)]
#[CoversClass(NameValidator::class)]
#[CoversClass(TitleValidator::class)]
class RESTControllerTest extends TestCase
{
    public function setUpBefore(): void
    {
        Entity::clear('author');
        Entity::clear('book');
    }

    // author tests
    public function testSaveAuthor()
    {
        $authorREST = new AuthorREST();
        $response = $authorREST->post([
            'last_name' => 'Wells',
            'middle_name' => 'Herbert',
            'first_name' => 'George'
        ]);
        $this->assertTrue($response['included']);
        Entity::clear('author');
    }
  
    public function testReadAuthor()
    {
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Von Goethe',
            'middle_name' => 'Wolfgang',
            'first_name' => 'Johann'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Fitzgerald',
            'middle_name' => 'Scott',
            'first_name' => 'Francis'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Doyle',
            'middle_name' => 'Arthur',
            'first_name' => 'Conan'
        ]);        
        $author = $authorREST->get(2);
        $this->assertEquals('Scott',$author->middleName);
        Entity::clear('author');
    }

    public function testReadAuthors()
    {   
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Shelley',
            'middle_name' => 'Wollstonecraft',
            'first_name' => 'Mary'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Christie',
            'middle_name' => 'Mary',
            'first_name' => 'Agatha'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Lispector',
            'middle_name' => 'Pinkhasivna',
            'first_name' => 'Chaya'
        ]);
        $authors = $authorREST->get(0);;
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors[1]->firstName);
        Entity::clear('author');
    }
    
    public function testUpdateAuthor()
    {
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Maupassant',
            'middle_name' => 'de',
            'first_name' => 'Guy'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Saint-Exupéry',
            'middle_name' => 'de',
            'first_name' => 'Antoine'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Balzac',
            'middle_name' => 'de',
            'first_name' => 'Honoré'
        ]);
        $author = $authorREST->get(1);
        $this->assertEquals('Guy',$author->firstName);
        $authorREST->put([
            'code' => 1,
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = $authorREST->get(1);
        $this->assertEquals('Rudolf',$author->firstName);
        Entity::clear('author');
    }
    
    public function testDeleteAuthor()
    {
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Assis',
            'middle_name' => 'de',
            'first_name' => 'Machado'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Alencar',
            'middle_name' => 'de',
            'first_name' => 'José'
        ]);
        $authorREST = new AuthorREST();
        $authorREST->post([
            'last_name' => 'Queiroz',
            'middle_name' => 'de',
            'first_name' => 'Rachel'
        ]);
        $author = $authorREST->get(2);
        $this->assertEquals('Alencar',$author->lastName);
        $authorREST->delete(2);
        $author = $authorREST->get(2);
        $this->assertEmpty($author->code);
        Entity::clear('author');
    }
    
    public function testSaveBook()
    {
        $authorREST = new AuthorREST();
        $bookREST = new BookREST();
        $authorREST->post([
            'last_name' => 'Wells',
            'middle_name' => 'Herbert',
            'first_name' => 'George'
        ]);
        $response = $bookREST->post([
            'title' => 'The Time Machine',
            'author_code' => 1
        ]);
        $this->assertTrue($response['included']);
        Entity::clear('author');
        Entity::clear('book');
    }
 
    public function testReadBook()
    {
        $authorREST = new AuthorREST();
        $bookREST = new BookREST();
        $authorREST->post([
            'last_name' => 'Von Goethe',
            'middle_name' => 'Wolfgang',
            'first_name' => 'Johann'
        ]);
        $bookREST->post([
            'title' => 'Fausto',
            'author_code' => 1
        ]);
        $book = $bookREST->get(1);
        $this->assertStringContainsString('Fausto',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testReadBooks()
    {
        $authorREST = new AuthorREST();
        $bookREST = new BookREST();
        $authorREST->post([
            'last_name' => 'Christie',
            'middle_name' => 'Mary',
            'first_name' => 'Agatha'
        ]);
        $bookREST->post([
            'title' => 'Murder on the Orient Express',
            'author_code' => 1
        ]);
        $bookREST->post([
            'title' => 'Death on the Nile',
            'author_code' => 1
        ]);
        $bookREST->post([
            'title' => 'Halloween Party',
            'author_code' => 1
        ]);
        $books = $bookREST->get(0);
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books[0]->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testUpdateBook()
    {
        $authorREST = new AuthorREST();
        $bookREST = new BookREST();
        $authorREST->post([
            'last_name' => 'Balzac',
            'middle_name' => 'de',
            'first_name' => 'Honoré'
        ]);
        $bookREST->post([
            'title' => 'La Vendetta',
            'author_code' => 1
        ]);
        $bookREST->post([
            'title' => 'Le Contrat de mariage',
            'author_code' => 1
        ]);
        $bookREST->post([
            'title' => 'La Femme de trente ans',
            'author_code' => 1
        ]);
        $book = $bookREST->get(2);
        $this->assertStringContainsString('mariage',$book->title);
        $bookREST->put([
            'code' => 2,
            'title' => 'Un début dans la vie',
            'author_code' => 1
        ]);
        $book = $bookREST->get(2);
        $this->assertStringContainsString('vie',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testDeleteBook()
    {
        $authorREST = new AuthorREST();
        $bookREST = new BookREST();
        $authorREST->post([
            'last_name' => 'Alencar',
            'middle_name' => 'de',
            'first_name' => 'José'
        ]);
        $bookREST->post([
            'title' => 'O Guarani',
            'author_code' => 1
        ]);
        $bookREST->post([
            'title' => 'Iracema',
            'author_code' => 1
        ]);
        $bookREST->post([
            'title' => 'Ubirajara',
            'author_code' => 1
        ]);
        $book = $bookREST->get(2);
        $this->assertStringContainsString('Iracema',$book->title);
        $bookREST->delete(2);
        $book = $bookREST->get(2);
        $this->assertEmpty($book->code);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public static function tearDownAfterClass(): void
    {
        Entity::clear('author');
        Entity::clear('book');
    }
}
