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
use Librarian\Filter\TagFilter;

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
 * @covers TagFilter
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
#[CoversClass(TagFilter::class)]
class PlainTextTest extends TestCase
{
    public function setUpBefore(): void
    {
        Entity::clear('author');
        Entity::clear('book');
    }

    // author tests
    public function testSaveAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $this->assertTrue($authorPlainText->save(new Author(0,'George','Herbert','Wells')));
        Entity::clear('author');
    }
  
    public function testReadAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $authorPlainText->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        $authorPlainText->save(new Author(0,'Francis','Scott','Fitzgerald'));
        $authorPlainText->save(new Author(0,'Arthur','Conan','Doyle'));
        $author = (new AuthorPlainTextFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        Entity::clear('author');
    }

    public function testReadAuthors()
    {   
        $authorPlainText = new AuthorPlainText();     
        $authorPlainText->save(new Author(0,'Mary','Wollstonecraft','Shelley'));
        $authorPlainText->save(new Author(0,'Agatha','Mary','Christie'));
        $authorPlainText->save(new Author(0,'Chaya','Pinkhasivna','Lispector'));
        $authors = (new AuthorPlainTextFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        Entity::clear('author');
    }
    
    public function testUpdateAuthor()
    {
        $authorPlainText = new AuthorPlainText();
        $authorPlainText->save(new Author(0,'Guy','de','Maupassant'));
        $authorPlainText->save(new Author(0,'Antoine','de','Saint-Exupéry'));
        $authorPlainText->save(new Author(0,'Honoré','de','Balzac'));
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
        $authorPlainText->save(new Author(0,'Machado','de','Assis'));
        $authorPlainText->save(new Author(0,'José','de','Alencar'));
        $authorPlainText->save(new Author(0,'Rachel','de','Queiroz'));
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
        $authorPlainText->save(new Author(0,'George','Herbert','Wells'));
        $this->assertTrue($bookPlainText->save(new Book(0,'The Time Machine',new Author(1))));
        Entity::clear('author');
        Entity::clear('book');
    }
 
    public function testReadBook()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save(new Author(0,'Johann','Wolfgang','Von Goethe'));  
        $bookPlainText->save(new Book(0,'Fausto', new Author(1)));
        $book = (new BookPlainTextFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        Entity::clear('author');
        Entity::clear('book');
    }
    
    public function testReadBooks()
    {
        $authorPlainText = new AuthorPlainText();
        $bookPlainText = new BookPlainText();
        $authorPlainText->save(new Author(0,'Agatha','Mary','Christie'));
        $bookPlainText->save(new Book(0,'Murder on the Orient Express',new Author(1)));
        $bookPlainText->save(new Book(0,'Death on the Nile',new Author(1)));
        $bookPlainText->save(new Book(0,'Halloween Party',new Author(1)));
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
        $authorPlainText->save(new Author(0,'Honoré','de','Balzac'));
        $bookPlainText->save(new Book(0,'La Vendetta',new Author(1)));
        $bookPlainText->save(new Book(0,'Le Contrat de mariage',new Author(1)));
        $bookPlainText->save(new Book(0,'La Femme de trente ans',new Author(1)));
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
        $authorPlainText->save(new Author(0,'José','de','Alencar'));
        $bookPlainText->save(new Book(0,'O Guarani',new Author(1)));
        $bookPlainText->save(new Book(0,'Iracema',new Author(1)));
        $bookPlainText->save(new Book(0,'Ubirajara',new Author(1)));
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
