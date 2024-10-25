<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fgsl\Rest\Rest;
use Librarian\Test\PHPServer;
use Librarian\Model\Entity;
use Librarian\Model\Author;
use Librarian\Model\Book;
use Librarian\Model\AuthorProxy;
use Librarian\Model\BookProxy;
use Librarian\Model\Filesystem\AuthorPlainText;
use Librarian\Model\Filesystem\BookPlainText;
use Librarian\Model\Filesystem\AuthorPlainTextFinder;
use Librarian\Model\Filesystem\BookPlainTextFinder;
use Librarian\Model\AbstractRowSet;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\BookRowSet;
use Librarian\Util\Config;

/**
 * @covers Author
 * @covers Book
 * @covers AuthorProxy
 * @covers BookProxy
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
#[CoversClass(AuthorProxy::class)]
#[CoversClass(BookProxy::class)]
#[CoversClass(AuthorPlainText::class)]
#[CoversClass(BookPlainText::class)]
#[CoversClass(Entity::class)]
#[CoversClass(AuthorPlainTextFinder::class)]
#[CoversClass(BookPlainTextFinder::class)]
#[CoversClass(AbstractRowSet::class)]
#[CoversClass(AuthorRowSet::class)]
#[CoversClass(BookRowSet::class)]
#[CoversClass(Config::class)]
class APITest extends TestCase
{
    private static $authStrategy;
    private static $permissionsStrategy;
    private static $token;

    public static function setUpBeforeClass(): void
    {
        putenv('LIBRARIAN_TEST_ENVIRONMENT=true');
        PHPServer::getInstance()->start();
        self::$authStrategy = Config::get('auth_strategy');
        Config::change('auth_strategy','api');
        self::$permissionsStrategy = Config::get('permissions_strategy');
        Config::change('permissions_strategy','rbac');
        $data = [
            'username' => 'jack',
            'password' => 'MySecret123@'
        ];
        $rest = new Rest();
        $headers = ['Content-type:application/x-www-form-urlencoded'];
        $response = $rest->doPost($data,$headers,'localhost:8008/index.php?api=index',200);
        $object = json_decode($response);    
        self::$token = $object->token;    
    }

    //authors
    public function testListAuthors()
    {
        (new AuthorProxy())->save('Márquez','García','Gabriel');
        (new AuthorProxy())->save('Borges','Luis','Jorge');
        (new AuthorProxy())->save('Llosa','Vargas','Mario');
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?api=author&token=' . self::$token,200);
        $list = json_decode($response);
        $this->assertEquals('Borges',$list[1]->lastName);
        Entity::clear('author');
    }

    public function testListNoAuthors()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?api=author&token=' . self::$token,200);
        $authors = json_decode($response);
        $this->assertEmpty($authors);
        Entity::clear('author');
    }
    
    public function testSaveAuthor()
    {
        $data = [
            'first_name' => 'Fyodor',
            'middle_name' => 'Mikhailovich',
            'last_name' => 'Dostoevsky'
        ];
        $rest = new Rest();
        $response = $rest->doPost($data, [],'localhost:8008/index.php?api=author&token=' . self::$token,200);
        $object = json_decode($response);
        $this->assertTrue($object->included);
        Entity::clear('author');
    }
    
    public function testUpdateAuthor()
    {
        $data = [
            'first_name' => 'Boris',
            'middle_name' => 'Leonidovich',
            'last_name' => 'Pasternak'
        ];
        (new AuthorProxy())->save($data['last_name'],$data['middle_name'],$data['first_name']);
        $data['code'] = 1;
        $data['last_name'] = 'Neigauz';
        $rest = new Rest();
        $response = $rest->doPut($data, [],'localhost:8008/index.php?api=author&token=' . self::$token,200);
        $object = json_decode($response);
        $this->assertTrue($object->updated);
        $author = (new AuthorProxy())->getByCode(1);
        $this->assertEquals('Neigauz',$author->lastName);
        Entity::clear('author');
    }
    
    public function testDeleteAuthor()
    {
        $data = [
            'first_name' => 'Vladimir',
            'middle_name' => 'Vladimirovich',
            'last_name' => 'Nabokov'
        ];
        (new AuthorProxy())->save($data['last_name'],$data['middle_name'],$data['first_name']);
        $rest = new Rest();
        $response = $rest->doDelete([],'localhost:8008/index.php?c=author&a=delete&code=1&token=' . self::$token,200);
        $object = json_decode($response);
        $author = (new AuthorProxy())->getByCode(1);
        $this->assertEmpty($author->lastName);
        Entity::clear('author');
    }

    //books
    public function testListBooks()
    {
        (new AuthorProxy())->save('Márquez','García','Gabriel');
        (new BookProxy())->save('La hojarasca',1);
        (new BookProxy())->save('Cien años de soledad',1);
        (new BookProxy())->save('Crónica de una muerte anunciada',1);                
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?api=book&token=' . self::$token,200);
        $list = json_decode($response);
        $this->assertStringContainsString('muerte anunciada',$list[2]->title);
        Entity::clear('book');
        Entity::clear('author');        
    }

    public function testListNoBooks()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=book&a=list&token=' . self::$token,200);
        $object = json_decode($response);
        $this->assertEmpty($object);
        Entity::clear('book');
    }   

    public function testSaveBook()
    {
        $data = [
            'first_name' => 'Fyodor',
            'middle_name' => 'Mikhailovich',
            'last_name' => 'Dostoevsky'
        ];
        (new AuthorProxy())->save($data['last_name'],$data['middle_name'],$data['first_name']);
        $data = [
            'title' => 'Crime and Punishment',
            'author_code' => 1
        ];
        (new BookProxy())->save($data['title'],$data['author_code']);
        $rest = new Rest();
        $response = $rest->doPost($data, [],'localhost:8008/index.php?api=book&token=' . self::$token,200);
        $object = json_decode($response);
        $this->assertTrue($object->included);
        Entity::clear('book');
        Entity::clear('author');
    }
    
    public function testUpdateBook()
    {
        $data = [
            'first_name' => 'Boris',
            'middle_name' => 'Leonidovich',
            'last_name' => 'Pasternak'
        ];
        (new AuthorProxy())->save($data['last_name'],$data['middle_name'],$data['first_name']);
        $data = [
            'title' => 'Doktor Jivago',
            'author_code' => 1
        ];
        (new BookProxy())->save($data['title'],$data['author_code']);
        $data['code'] = 1;
        $data['title'] = 'Stihi';
        $rest = new Rest();
        $response = $rest->doPut($data, [],'localhost:8008/index.php?api=book&token=' . self::$token,200);
        $object = json_decode($response);
        $this->assertTrue($object->updated);
        $book = (new BookProxy())->getByCode(1);
        $this->assertEquals('Stihi',$book->title);
        Entity::clear('book');
        Entity::clear('author');
    }
    
    public function testDeleteBook()
    {
        $data = [
            'first_name' => 'Vladimir',
            'middle_name' => 'Vladimirovich',
            'last_name' => 'Nabokov'
        ];
        (new AuthorProxy())->save($data['last_name'],$data['middle_name'],$data['first_name']);
        (new BookProxy())->save('Lolita',1);
        $rest = new Rest();
        $response = $rest->doDelete([],'localhost:8008/index.php?api=book&code=1&token=' . self::$token,200);
        $object = json_decode($response);
        $this->assertTrue($object->deleted);
        $book = (new BookProxy())->getByCode(1);
        $this->assertEmpty($book->title);
        Entity::clear('book');
        Entity::clear('author');
    }
   
    public static function tearDownAfterClass():void
    {
        Config::change('auth_strategy',self::$authStrategy);
        Config::change('permissions_strategy', self::$permissionsStrategy);
        putenv('LIBRARIAN_TEST_ENVIRONMENT=false');
        PHPServer::getInstance()->stop();
        Entity::clear('book');
        Entity::clear('author');
    }
}
