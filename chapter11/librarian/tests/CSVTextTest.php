<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Model\Filesystem\AuthorCSV;
use Librarian\Model\Filesystem\BookCSV;
use Librarian\Util\Config;
use Librarian\Model\Filesystem\AuthorCSVFinder;
use Librarian\Model\Filesystem\BookCSVFinder;
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
 * @covers AuthorCSV
 * @covers BookCSV
 * @covers Entity
 * @covers AuthorCSVFinder  
 * @covers BookCSVFinder
 * @covers AbstractRowSet
 * @covers AuthorRowSet
 * @covers BookRowSet
 * @covers Config
 * @covers TagFilter
 */
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AuthorCSV::class)]
#[CoversClass(BookCSV::class)]
#[CoversClass(Entity::class)]
#[CoversClass(AuthorCSVFinder::class)]
#[CoversClass(BookCSVFinder::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(Config::class)]
#[CoversClass(TagFilter::class)]
class CSVTextTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Config::override('storage_format','csv');
        Entity::clear('author');
        Entity::clear('book');
    }

    public function setUpBefore(): void
    {
        Entity::clear('author');
        Entity::clear('book');
    }

    // author tests
    public function testSaveAuthor()
    {
        $this->assertTrue((new AuthorCSV())->save(new Author(0,'George','Herbert','Wells')));
        Entity::clear('author');
    }
    
    public function testReadAuthor()
    {
        $authorCSV = new AuthorCSV();
        $author = (new AuthorCSVFinder())->readByCode(2);
        $this->assertEquals('',$author->middleName);
        $authorCSV->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        $authorCSV->save(new Author(0,'Francis','Scott','Fitzgerald'));
        $authorCSV->save(new Author(0,'Arthur','Conan','Doyle'));
        $author = (new AuthorCSVFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        Entity::clear('author');
    }
    
    public function testReadAuthors()
    {
        $authorCSV = new AuthorCSV();
        $authorCSV->save(new Author(0,'Mary','Wollstonecraf','Shelley'));
        $authorCSV->save(new Author(0,'Agatha','Mary','Christie'));
        $authorCSV->save(new Author(0,'Chaya','Pinkhasivna','Lispector'));
        $authors = (new AuthorCSVFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        Entity::clear('author');
    }
   
    public function testUpdateAuthor()
    {
        $authorCSV = new AuthorCSV();
        $authorCSV->save(new Author(0,'Guy','de','Maupassant'));
        $authorCSV->save(new Author(0,'Antonie','de','Saint-Exupéry'));
        $authorCSV->save(new Author(0,'Honoré','de','Balzac'));
        $authorCSVFinder = new AuthorCSVFinder();
        $author = $authorCSVFinder->readByCode(1);
        $this->assertEquals('Guy',$author->firstName);
        $authorCSV->update(1,[
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = $authorCSVFinder->readByCode(1);
        $this->assertEquals('Rudolf',$author->firstName);
        Entity::clear('author');
    }
   
    public function testDeleteAuthor()
    {
        $authorCSV = new AuthorCSV();
        $authorCSV->save(new Author(0,'Machado','de','Assis'));
        $authorCSV->save(new Author(0,'José','de','Alencar'));
        $authorCSV->save(new Author(0,'Rachel','de','Queiroz'));
        $authorCSVFinder = new AuthorCSVFinder();
        $author = $authorCSVFinder->readByCode(2);
        $this->assertEquals('Alencar',$author->lastName);
        $authorCSV->delete(2);
        $author = $authorCSVFinder->readByCode(2);
        $this->assertEmpty($author->code);        
        Entity::clear('author');
    }
    
    public function testSaveBook()
    {
        (new AuthorCSV())->save(new Author(0,'George','Herbert','Wells'));
        $this->assertTrue((new BookCSV())->save(new Book(0,'The Time Machine',new Author(1))));
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testReadBook()
    {
        (new AuthorCSV())->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        (new BookCSV())->save(new Book(0,'Fausto',new Author(1)));
        $book = (new BookCSVFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
   
    public function testReadBooks()
    {
        $bookCSV = new BookCSV();
        (new AuthorCSV())->save(new Author(0,'Agatha','Mary','Christie'));
        $bookCSV->save(new Book(0,'Murder on the Orient Express',new Author(1)));
        $bookCSV->save(new Book(0,'Death on the Nile',new Author(1)));
        $bookCSV->save(new Book(0,'Halloween Party',new Author(1)));
        $books = (new BookCSVFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testUpdateBook()
    {
        $bookCSV = new BookCSV();        
        (new AuthorCSV())->save(new Author(0,'Honoré','de','Balzac'));
        $bookCSV->save(new Book(0,'La Vendetta',new Author(1)));
        $bookCSV->save(new Book(0,'Le Contrat de mariage',new Author(1)));
        $bookCSV->save(new Book(0,'La Femme de trente ans',new Author(1)));
        $bookCSVFinder = new BookCSVFinder();
        $book = $bookCSVFinder->readByCode(2);
        $this->assertStringContainsString('mariage',$book->title);
        $bookCSV->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = $bookCSVFinder->readByCode(2);
        $this->assertStringContainsString('vie',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testDeleteBook()
    {
        $authorCSV = new AuthorCSV();
        $bookCSV = new BookCSV();        
        $authorCSV->save(new Author(0,'José','de','Alencar'));
        $bookCSV->save(new Book(0,'O Guarani',new Author(1)));
        $bookCSV->save(new Book(0,'Iracema',new Author(1)));
        $bookCSV->save(new Book(0,'Ubirajara',new Author(1)));
        $bookCSVFinder = new BookCSVFinder();        
        $book = $bookCSVFinder->readByCode(2);
        $this->assertStringContainsString('Iracema',$book->title);
        $bookCSV->delete(2);
        $book = $bookCSVFinder->readByCode(2);
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