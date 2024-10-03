<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Model\Filesystem\AuthorJSON;
use Librarian\Model\Filesystem\BookJSON;
use Librarian\Util\Config;
use Librarian\Model\Filesystem\AuthorJSONFinder;
use Librarian\Model\Filesystem\BookJSONFinder;
use Librarian\Model\Entity;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Model\AbstractRowSet;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Filter\TagFilter;

/**
 * @covers Author
 * @covers Book
 * @covers AuthorJSON
 * @covers BookJSON
 * @covers Entity
 * @covers AuthorJSONFinder  
 * @covers BookJSONFinder
 * @covers AbstractRowSet
 * @covers AuthorRowSet
 * @covers BookRowSet
 * @covers Config
 * @covers TagFilter
 */
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AuthorJSON::class)]
#[CoversClass(BookJSON::class)]
#[CoversClass(Entity::class)]
#[CoversClass(AuthorJSONFinder::class)]
#[CoversClass(BookJSONFinder::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(Config::class)]
#[CoversClass(TagFilter::class)]
class JSONTextTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Config::override('storage_format','json');
        Entity::clear('author');
        Entity::clear('book');
    }

    public function setUpBefore(): void
    {
        Entity::clear('author');
        Entity::clear('book');
    }

    public function testSaveAuthor()
    {
        $this->assertTrue((new AuthorJSON())->save(new Author(0,'George','Herbert','Wells')));
        Entity::clear('author');
    }
    
    public function testReadAuthor()
    {
        $authorJSON = new AuthorJSON();
        $authorJSON->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        $authorJSON->save(new Author(0,'Francis','Scott','Fitzgerald'));
        $authorJSON->save(new Author(0,'Arthur','Conan','Doyle'));
        $author = (new AuthorJSONFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        Entity::clear('author');
    }
    
    public function testReadAuthors()
    {
        $authorJSON = new AuthorJSON();
        $authorJSON->save(new Author(0,'Mary','Wollstonecraft','Shelley'));
        $authorJSON->save(new Author(0,'Agatha','Mary','Christie'));
        $authorJSON->save(new Author(0,'Chaya','Pinkhasivna','Lispector'));
        $authors = (new AuthorJSONFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        Entity::clear('author');
    }
   
    public function testUpdateAuthor()
    {
        $authorJSON = new AuthorJSON();
        $authorJSON->save(new Author(0,'Guy','de','Maupassant'));
        $authorJSON->save(new Author(0,'Antoine','de','Saint-Exupéry'));
        $authorJSON->save(new Author(0,'Honoré','de','Balzac'));
        $authorJSONFinder = new AuthorJSONFinder();
        $author = $authorJSONFinder->readByCode(1);
        $this->assertEquals('Guy',$author->firstName);
        $authorJSON->update(1,[
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = $authorJSONFinder->readByCode(1);
        $this->assertEquals('Rudolf',$author->firstName);
        Entity::clear('author');
    }
    
    public function testDeleteAuthor()
    {
        $authorJSON = new AuthorJSON();
        $authorJSON->save(new Author(0,'Machado','de','Assis'));
        $authorJSON->save(new Author(0,'José','de','Alencar'));
        $authorJSON->save(new Author(0,'Rachel','de','Queiroz'));
        $authorJSONFinder = new AuthorJSONFinder();        
        $author = $authorJSONFinder->readByCode(2);
        $this->assertEquals('Alencar',$author->lastName);
        $authorJSON->delete(2);
        $author = $authorJSONFinder->readByCode(2);
        $this->assertEmpty($author->code);
        Entity::clear('author');
    }
    // book tests
    public function testSaveBook()
    {
        (new AuthorJSON())->save(new Author(0,'George','Herbert','Wells'));
        $this->assertTrue((new BookJSON())->save(new Book(0,'The Time Machine',new Author(1))));
        Entity::clear('author');
        Entity::clear('book');
    }
  
    public function testReadBookInJSON()
    {
        (new AuthorJSON())->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        (new BookJSON())->save(new Book(0,'Fausto',new Author(1)));
        $book = (new BookJSONFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testReadBooks()
    {
        $bookJSON = new BookJSON();
        (new AuthorJSON())->save(new Author(0,'Agatha','Mary','Christie'));
        $bookJSON->save(new Book(0,'Murder on the Orient Express',new Author(1)));
        $bookJSON->save(new Book(0,'Death on the Nile',new Author(1)));
        $bookJSON->save(new Book(0,'Halloween Party',new Author(1))); 
        $books = (new BookJSONFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testUpdate()
    {
        $bookJSON = new BookJSON();
        (new AuthorJSON())->save(new Author(0,'Honoré','de','Balzac'));
        $bookJSON->save(new Book(0,'La Vendetta',new Author(1)));
        $bookJSON->save(new Book(0,'Le Contrat de mariage',new Author(1)));
        $bookJSON->save(new Book(0,'La Femme de trente ans',new Author(1)));
        $bookJSONFinder = new BookJSONFinder();
        $book = $bookJSONFinder->readByCode(2);
        $this->assertStringContainsString('mariage',$book->title);
        $bookJSON->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = $bookJSONFinder->readByCode(2);
        $this->assertStringContainsString('vie',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testDelete()
    {
        $authorJSON = new AuthorJSON();
        $bookJSON = new BookJSON();
        $authorJSON->save(new Author(0,'José','de','Alencar'));
        $bookJSON->save(new Book(0,'O Guarani',new Author(1)));
        $bookJSON->save(new Book(0,'Iracema',new Author(1)));
        $bookJSON->save(new Book(0,'Ubirajara',new Author(1)));
        $bookJSONFinder = new BookJSONFinder();
        $book = $bookJSONFinder->readByCode(2);
        $this->assertStringContainsString('Iracema',$book->title);
        $bookJSON->delete(2);
        $book = $bookJSONFinder->readByCode(2);
        $this->assertEmpty($book->code);
        Entity::clear('author');
        Entity::clear('book');
    }

    public static function tearDownAfterClass(): void
    {
        Config::override('storage_format','txt');
        Entity::clear('author');
        Entity::clear('book');
    }    
}
