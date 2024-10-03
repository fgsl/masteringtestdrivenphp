<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Librarian\Model\ORM\AuthorTableGateway;
use Librarian\Model\ORM\BookTableGateway;
use Librarian\Model\ORM\Table;
use Librarian\Util\Config;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Filter\TagFilter;

/**
 * @covers Table
 * @covers AuthorTableGateway
 * @covers BookTableGateway
 * @covers Config
 * @coverst Author
 * @covers Book
 * @covers TagFilter
 */
#[CoversClass(Table::class)]
#[CoversClass(AuthorTableGateway::class)]
#[CoversClass(BookTableGateway::class)]
#[CoversClass(Config::class)]
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(TagFilter::class)]
class TableGatewayTest extends TestCase
{
    // author tests
    public function testSaveAuthor()
    {
        $authorTableGateway = new AuthorTableGateway();
        $this->assertTrue($authorTableGateway->save(new Author(0,'George','Herbert','Wells')));
        (new Table('authors'))->truncate();
    }

    public function testReadAuthor()
    {
        $authorTableGateway = new AuthorTableGateway();
        $authorTableGateway->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        $authorTableGateway->save(new Author(0,'Francis','Scott','Fitzgerald'));
        $authorTableGateway->save(new Author(0,'Arthur','Conan','Doyle'));
        $author = $authorTableGateway->readByCode(2);
        $this->assertEquals('Scott',$author['middle_name']);
        (new Table('authors'))->truncate();
    }

    public function testReadAuthors()
    {
        $authorTableGateway = new AuthorTableGateway();
        $authorTableGateway->save(new Author(0,'Mary','Wollstonecraft','Shelley'));
        $authorTableGateway->save(new Author(0,'Agatha','Mary','Christie'));
        $authorTableGateway->save(new Author(0,'Chaya','Pinkhasivna','Lispector'));
        $authors = $authorTableGateway->readAuthors();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors[1]['first_name']);
        (new Table('authors'))->truncate();
    }
    
    public function testUpdateAuthor()
    {
        $authorTableGateway = new AuthorTableGateway();
        $authorTableGateway->save(new Author(0,'Guy','de','Maupassant'));
        $authorTableGateway->save(new Author(0,'Antoine','de','Saint-Exupéry'));
        $authorTableGateway->save(new Author(0,'Honoré','de','Balzac'));
        $author = $authorTableGateway->readByCode(1);
        $this->assertEquals('Guy',$author['first_name']);
        $authorTableGateway->update(1,['first_name' =>'Rudolf','middle_name'=>'Erich','last_name' =>'Raspe']);
        $author = $authorTableGateway->readByCode(1);
        $this->assertEquals('Rudolf',$author['first_name']);
        (new Table('authors'))->truncate();
    }
   
    public function testDeleteAuthor()
    {
        $authorTableGateway = new AuthorTableGateway();
        $authorTableGateway->save(new Author(0,'Machado','de','Assis'));
        $authorTableGateway->save(new Author(0,'José','de','Alencar'));
        $authorTableGateway->save(new Author(0,'Rachel','de','Queiroz'));
        $author = $authorTableGateway->readByCode(2);
        $this->assertEquals('Alencar',$author['last_name']);
        $authorTableGateway->delete(2);
        $author = $authorTableGateway->readByCode(2);
        $this->assertEmpty($author);
        (new Table('authors'))->truncate();
    }    
    // book tests
    public function testSaveBook()
    {
        $authorTableGateway = new AuthorTableGateway();
        $bookTableGateway = new BookTableGateway();
        $authorTableGateway->save(new Author(0,'George','Herbert','Wells'));
        $this->assertTrue($bookTableGateway->save(new Book(0,'The Time Machine',new Author(1))));
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
  
    public function testReadBook()
    {
        $authorTableGateway = new AuthorTableGateway();
        $bookTableGateway = new BookTableGateway();        
        $authorTableGateway->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        $bookTableGateway->save(new Book(0,'Fausto',new Author(1)));
        $book = $bookTableGateway->readByCode(1);
        $this->assertStringContainsString('Fausto',$book['title']);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
    
    public function testReadBooks()
    {
        $authorTableGateway = new AuthorTableGateway();
        $bookTableGateway = new BookTableGateway();        
        $authorTableGateway->save(new Author(0,'Agatha','Mary','Christie'));
        $bookTableGateway->save(new Book(0,'Murder on the Orient Express',new Author(1)));
        $bookTableGateway->save(new Book(0,'Death on the Nile',new Author(1)));
        $bookTableGateway->save(new Book(0,'Halloween Party',new Author(1))); 
        $books = $bookTableGateway->readBooks();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books[0]['title']);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
    
    public function testUpdateBook()
    {
        $authorTableGateway = new AuthorTableGateway();
        $bookTableGateway = new BookTableGateway();        
        $authorTableGateway->save(new Author(0,'Honoré','de','Balzac'));
        $bookTableGateway->save(new Book(0,'La Vendetta',new Author(1)));
        $bookTableGateway->save(new Book(0,'Le Contrat de mariage',new Author(1)));
        $bookTableGateway->save(new Book(0,'La Femme de trente ans',new Author(1)));
        $book = $bookTableGateway->readByCode(2);
        $this->assertStringContainsString('mariage',$book['title']);
        $bookTableGateway->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = $bookTableGateway->readByCode(2);
        $this->assertStringContainsString('vie',$book['title']);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
   
    public function testDeleteBook()
    {
        $authorTableGateway = new AuthorTableGateway();
        $bookTableGateway = new BookTableGateway();        
        $authorTableGateway->save(new Author(0,'José','de','Alencar'));
        $bookTableGateway->save(new Book(0,'O Guarani',new Author(1)));
        $bookTableGateway->save(new Book(0,'Iracema',new Author(1)));
        $bookTableGateway->save(new Book(0,'Ubirajara',new Author(1)));
        $book = $bookTableGateway->readByCode(2);
        $this->assertStringContainsString('Iracema',$book['title']);
        $bookTableGateway->delete(2);
        $book = $bookTableGateway->readByCode(2);
        $this->assertEmpty($book);
        (new Table('books'))->truncate();
        (new Table('authors'))->truncate();
    }
}
