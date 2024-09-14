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
class CSVTextTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Config::override('storage_format','csv');
    }

    // author tests
    public function testSaveAuthor()
    {
        $this->assertTrue((new AuthorCSV())->save('Wells','Herbert','George'));
        Entity::clear('author');
    }
    
    public function testReadAuthor()
    {
        $authorCSV = new AuthorCSV();
        $authorCSV->save('Von Goethe','Wolfgang','Johann');
        $authorCSV->save('Fitzgerald','Scott','Francis');
        $authorCSV->save('Doyle','Arthur','Conan');
        $author = (new AuthorCSVFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        Entity::clear('author');
    }
    
    public function testReadAuthors()
    {
        $authorCSV = new AuthorCSV();
        $authorCSV->save('Shelley','Wollstonecraft','Mary');
        $authorCSV->save('Christie','Mary','Agatha');
        $authorCSV->save('Lispector','Pinkhasivna','Chaya');
        $authors = (new AuthorCSVFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        Entity::clear('author');
    }
   
    public function testUpdateAuthor()
    {
        $authorCSV = new AuthorCSV();
        $authorCSV->save('Maupassant','de','Guy');
        $authorCSV->save('Saint-Exupéry','de','Antoine');
        $authorCSV->save('Balzac','de','Honoré');
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
        $authorCSV->save('Assis','de','Machado');
        $authorCSV->save('Alencar','de','José');
        $authorCSV->save('Queiroz','de','Rachel');
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
        (new AuthorCSV())->save('Wells','Herbert','George');
        $this->assertTrue((new BookCSV())->save('The Time Machine',1));
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testReadBook()
    {
        (new AuthorCSV())->save('Von Goethe','Wolfgang','Johann');
        (new BookCSV())->save('Fausto',1);
        $book = (new BookCSVFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
   
    public function testReadBooks()
    {
        $bookCSV = new BookCSV();
        (new AuthorCSV())->save('Christie','Mary','Agatha');
        $bookCSV->save('Murder on the Orient Express',1);
        $bookCSV->save('Death on the Nile',1);
        $bookCSV->save('Halloween Party',1);
        $books = (new BookCSVFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testUpdateBook()
    {
        $bookCSV = new BookCSV();        
        (new AuthorCSV())->save('Balzac','de','Honoré');
        $bookCSV->save('La Vendetta',1);
        $bookCSV->save('Le Contrat de mariage',1);
        $bookCSV->save('La Femme de trente ans',1);
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
        $authorCSV->save('Alencar','de','José');
        $bookCSV->save('O Guarani',1);
        $bookCSV->save('Iracema',1);
        $bookCSV->save('Ubirajara',1);
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