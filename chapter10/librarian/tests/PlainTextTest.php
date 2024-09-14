<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Model\Filesystem\AuthorPlainText;
use Librarian\Model\Filesystem\BookPlainText;
use Librarian\Util\Config;
use Librarian\Model\Filesystem\AuthorPlainTextFinder;
use Librarian\Model\Filesystem\BookPlainTextFinder;
use Librarian\Model\Entity;
use Librarian\Model\AbstractRowSet;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Model\Author;
use Librarian\Model\Book;

/**
 * @covers Author
 * @covers Book
 * @covers AuthorPlainText
 * @covers BookPlainText
 * @covers Entity
 * @covers AuthorPlainTextFinder  
 * @covers BookPlainTextFinder
 * @covers AbstractRowSet
 * @covers AuthorRowSet
 * @covers BookRowSet
 * @covers Config
 */
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AuthorPlainText::class)]
#[CoversClass(BookPlainText::class)]
#[CoversClass(Entity::class)]
#[CoversClass(AuthorPlainTextFinder::class)]
#[CoversClass(BookPlainTextFinder::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(Config::class)]
class PlainTextTest extends TestCase
{
    // author tests
    public function testSaveAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $this->assertTrue($authorPlainText->save('Wells','Herbert','George'));
        Entity::clear('author');
    }
  
    public function testReadAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $authorPlainText->save('Von Goethe','Wolfgang','Johann');
        $authorPlainText->save('Fitzgerald','Scott','Francis');
        $authorPlainText->save('Doyle','Arthur','Conan');
        $author = (new AuthorPlainTextFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        Entity::clear('author');
    }

    public function testReadAuthors()
    {   
        $authorPlainText = new AuthorPlainText();     
        $authorPlainText->save('Shelley','Wollstonecraft','Mary');
        $authorPlainText->save('Christie','Mary','Agatha');
        $authorPlainText->save('Lispector','Pinkhasivna','Chaya');
        $authors = (new AuthorPlainTextFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        Entity::clear('author');
    }
    
    public function testUpdateAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $authorPlainText->save('Maupassant','de','Guy');
        $authorPlainText->save('Saint-Exupéry','de','Antoine');
        $authorPlainText->save('Balzac','de','Honoré');
        $authorPlainTextFinder = new AuthorPlainTextFinder();
        $author = $authorPlainTextFinder->readByCode(1);
        $this->assertEquals('Guy',$author->firstName);
        $authorPlainText->update(1,[
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = $authorPlainTextFinder->readByCode(1);
        $this->assertEquals('Rudolf',$author->firstName);
        Entity::clear('author');
    }
    
    public function testDeleteAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $authorPlainText->save('Assis','de','Machado');
        $authorPlainText->save('Alencar','de','José');
        $authorPlainText->save('Queiroz','de','Rachel');
        $authorPlainTextFinder = new AuthorPlainTextFinder();
        $author = $authorPlainTextFinder->readByCode(2);
        $this->assertEquals('Alencar',$author->lastName);
        $authorPlainText->delete(2);
        $author = $authorPlainTextFinder->readByCode(2);
        $this->assertEmpty($author->code);
        Entity::clear('author');
    }
    
    public function testSaveBook()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save('Wells','Herbert','George');
        $this->assertTrue($bookPlainText->save('The Time Machine',1));
        Entity::clear('author');
        Entity::clear('book');
    }
 
    public function testReadBook()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save('Von Goethe','Wolfgang','Johann');        
        $bookPlainText->save('Fausto',1);
        $book = (new BookPlainTextFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testReadBooks()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save('Christie','Mary','Agatha');
        $bookPlainText->save('Murder on the Orient Express',1);
        $bookPlainText->save('Death on the Nile',1);
        $bookPlainText->save('Halloween Party',1);
        $books = (new BookPlainTextFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testUpdateBook()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save('Balzac','de','Honoré');
        $bookPlainText->save('La Vendetta',1);
        $bookPlainText->save('Le Contrat de mariage',1);
        $bookPlainText->save('La Femme de trente ans',1);
        $bookPlainTextFinder = new BookPlainTextFinder();
        $book = $bookPlainTextFinder->readByCode(2);
        $this->assertStringContainsString('mariage',$book->title);
        $bookPlainText->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = $bookPlainTextFinder->readByCode(2);
        $this->assertStringContainsString('vie',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testDeleteBook()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save('Alencar','de','José');
        $bookPlainText->save('O Guarani',1);
        $bookPlainText->save('Iracema',1);
        $bookPlainText->save('Ubirajara',1);
        $bookPlainTextFinder = new BookPlainTextFinder();
        $book = $bookPlainTextFinder->readByCode(2);
        $this->assertStringContainsString('Iracema',$book->title);
        $bookPlainText->delete(2);
        $book = $bookPlainTextFinder->readByCode(2);
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
