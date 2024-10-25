<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Model\ODM\AuthorCollection;
use Librarian\Model\ODM\BookCollection;
use Librarian\Model\ODM\AuthorCollectionFinder;
use Librarian\Model\ODM\BookCollectionFinder;
use Librarian\Model\ODM\Collection;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Model\AbstractRowSet;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Util\Config;
use Librarian\Filter\TagFilter;
/**
 * @covers AuthorCollection
 * @covers BookCollection
 * @covers AuthorCollectionFinder
 * @covers BookCollectionFinder
 * @covers AuthorCollection
 * @covers Collection
 * @covers Author
 * @covers Book
 * @covers AbstractRowSet
 * @covers AuthorRowSet
 * @covers BookRowSet
 * @covers Config
 * @covers TagFilter
 */
#[CoversClass(AuthorCollection::class)]
#[CoversClass(BookCollection::class)]
#[CoversClass(AuthorCollectionFinder::class)]
#[CoversClass(BookCollectionFinder::class)]
#[CoversClass(Collection::class)]
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(Config::class)]
#[CoversClass(TagFilter::class)]
class CollectionTest extends TestCase
{
    // author tests
    public function testSaveAuthor()
    {
        $this->assertTrue((new AuthorCollection())->save(new Author(0,'George','Herbert','Wells')));
        (new Collection('authors'))->drop();
    }

    public function testReadAuthor()
    {
        $authorCollection = new AuthorCollection();
        $authorCollection->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        $authorCollection->save(new Author(0,'Francis','Scott','Fitzgerald'));
        $authorCollection->save(new Author(0,'Arthur','Conan','Doyle'));
        $author = (new AuthorCollectionFinder())->readByCode(2);
        $this->assertEquals('Scott',$author->middleName);
        (new Collection('authors'))->drop();
    }

    public function testReadAuthors()
    {
        $authorCollection = new AuthorCollection();
        $authors = (new AuthorCollectionFinder())->readAll();
        $this->assertCount(0,$authors);
        $authorCollection->save(new Author(0,'Mary','Wollstonecraft','Shelley'));
        $authorCollection->save(new Author(0,'Agatha','Mary','Christie'));
        $authorCollection->save(new Author(0,'Chaya','Pinkhasivna','Lispector'));
        $authors = (new AuthorCollectionFinder())->readAll();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors->get(1)->firstName);
        (new Collection('authors'))->drop();
    }
    
    public function testUpdateAuthor()
    {
        $authorCollection = new AuthorCollection();
        $authorCollection->save(new Author(0,'Guy','de','Maupassant'));
        $authorCollection->save(new Author(0,'Antoine','de','Saint-Exupéry'));
        $authorCollection->save(new Author(0,'Honoré','de','Balzac'));
        $authorCollectionFinder = new AuthorCollectionFinder();
        $author = $authorCollectionFinder->readByCode(1);
        $this->assertEquals('Guy',$author->firstName);
        $authorCollection->update(1,[
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = $authorCollectionFinder->readByCode(1);
        $this->assertEquals('Rudolf',$author->firstName);
        (new Collection('authors'))->drop();
    }
    
    public function testDeleteAuthor()
    {
        $authorCollection = new AuthorCollection();
        $authorCollection->save(new Author(0,'Machado','de','Assis'));
        $authorCollection->save(new Author(0,'José','de','Alencar'));
        $authorCollection->save(new Author(0,'Rachel','de','Queiroz'));
        $authorCollectionFinder = new AuthorCollectionFinder();
        $author = $authorCollectionFinder->readByCode(2);
        $this->assertEquals('Alencar',$author->lastName);
        $authorCollection->delete(2);
        $author = $authorCollectionFinder->readByCode(2);
        $this->assertEmpty($author->code);
        (new Collection('authors'))->drop();
    }

    // book tests
    public function testSaveBook()
    {
        (new AuthorCollection())->save(new Author(0,'George','Herbert','Wells'));
        $this->assertTrue((new BookCollection())->save(new Book(0,'The Time Machine',new Author(1))));
        (new Collection('books'))->drop();
        (new Collection('authors'))->drop();
    }
  
    public function testReadBook()
    {
        (new AuthorCollection())->save(new Author(0,'Johann','Wolfgang','Von Goethe'));
        (new BookCollection())->save(new Book(0,'Fausto',new Author(1)));
        $book = (new BookCollectionFinder())->readByCode(1);
        $this->assertStringContainsString('Fausto',$book->title);
        (new Collection('books'))->drop();
        (new Collection('authors'))->drop();
    }
    
    public function testReadBooks()
    {
        (new AuthorCollection())->save(new Author(0,'Agatha','Mary','Christie'));
        $bookCollection = new BookCollection();
        $bookCollection->save(new Book(0,'Murder on the Orient Express',new Author(1)));
        $bookCollection->save(new Book(0,'Death on the Nile',new Author(1)));
        $bookCollection->save(new Book(0,'Halloween Party',new Author(1))); 
        $books = (new BookCollectionFinder())->readAll();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books->get(0)->title);
        (new Collection('books'))->drop();
        (new Collection('authors'))->drop();
    }
    
    public function testUpdateBook()
    {
        (new AuthorCollection())->save(new Author(0,'Honoré','de','Balzac'));
        $bookCollection = new BookCollection();
        $bookCollection->save(new Book(0,'La Vendetta',new Author(1)));
        $bookCollection->save(new Book(0,'Le Contrat de mariage',new Author(1)));
        $bookCollection->save(new Book(0,'La Femme de trente ans',new Author(1)));
        $bookCollectionFinder = new BookCollectionFinder();
        $book = $bookCollectionFinder->readByCode(2);
        $this->assertStringContainsString('mariage',$book->title);
        $bookCollection->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = $bookCollectionFinder->readByCode(2);
        $this->assertStringContainsString('vie',$book->title);
        (new Collection('books'))->drop();
        (new Collection('authors'))->drop();
    }
   
    public function testDeleteBook()
    {
        (new AuthorCollection())->save(new Author(0,'José','de','Alencar'));
        $bookCollection = new BookCollection();
        $bookCollection->save(new Book(0,'O Guarani',new Author(1)));
        $bookCollection->save(new Book(0,'Iracema',new Author(1)));
        $bookCollection->save(new Book(0,'Ubirajara',new Author(1)));
        $bookCollectionFinder = new BookCollectionFinder();
        $book = $bookCollectionFinder->readByCode(2);
        $this->assertStringContainsString('Iracema',$book->title);
        $bookCollection->delete(2);
        $book = $bookCollectionFinder->readByCode(2);
        $this->assertEmpty($book->code);
        (new Collection('books'))->drop();
        (new Collection('authors'))->drop();
    }
}
