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
use Librarian\Util\Config;
use Librarian\Model\AuthorProxy;
use Librarian\Model\BookProxy;
use Librarian\Model\ORM\AbstractDatabase;
use Librarian\Model\ORM\AuthorTableGateway;
use Librarian\Model\ORM\BookTableGateway;
use Librarian\Model\ORM\Table;

/**
 * @covers AbstractDatabase
 * @covers AuthorTableGateway
 * @covers BookTableGateway
 * @covers AuthorProxy
 * @covers BookProxy
 * @covers Entity
 * @covers Config
 * @covers Table
 */
#[CoversClass(AbstractDatabase::class)]
#[CoversClass(AuthorTableGateway::class)]
#[CoversClass(BookTableGateway::class)]
#[CoversClass(AuthorProxy::class)]
#[CoversClass(BookProxy::class)]
#[CoversClass(Entity::class)]
#[CoversClass(Config::class)]
#[CoversClass(Table::class)]
class ViewDatabaseTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        putenv('LIBRARIAN_TEST_ENVIRONMENT=true');
        Config::change("'txt'","'rdb'");
        PHPServer::getInstance()->start();
    }

    public function testIndex()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php',200);
        $this->assertStringContainsString('Librarian',$response);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $elements = $xpath->query("/html/body/ul");
        $this->assertEquals('Authors',$elements[0]->childNodes[1]->nodeValue);
    }
    //authors
    public function testListAuthors()
    {
        (new AuthorProxy())->save('Márquez','García','Gabriel');
        (new AuthorProxy())->save('Borges','Luis','Jorge');
        (new AuthorProxy())->save('Llosa','Vargas','Mario');
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=author&a=list',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $elements = $xpath->query("/html/body/h1");
        $this->assertEquals('Authors',$elements[0]->childNodes[0]->nodeValue);
        $this->assertStringContainsString('Jorge Luis Borges',$response);
        Entity::clear('author');
    }

    public function testListNoAuthors()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=author&a=list',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $elements = $xpath->query("/html/body/h1");
        $this->assertEquals('Authors',$elements[0]->childNodes[0]->nodeValue);
        Entity::clear('author');
    }   

    public function testNewAuthor()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=author&a=edit',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $nodeList = $xpath->query('//input[@type="text"]');
        $this->assertEquals(3,$nodeList->length);
        $node = $nodeList->item(0);
        $this->assertEmpty($node->nodeValue);
    }
    
    public function testEditAuthor()
    {
        (new AuthorProxy())->save('Sharma','Lakshmi','Raj');
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=author&a=edit&code=1',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $nodeList = $xpath->query('//input[@type="text"]');
        $this->assertEquals(3,$nodeList->length);
        $node = $nodeList->item(0);
        $this->assertEquals('Raj',$node->getAttribute('value'));
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
        $response = $rest->doPost($data, [],'localhost:8008/index.php?c=author&a=save',302);
        $this->assertStringContainsString('Record saved successfully!',$response);
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
        $response = $rest->doPost($data, [],'localhost:8008/index.php?c=author&a=save',302);
        $this->assertStringContainsString('Record updated successfully!',$response);
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
        $response = $rest->doGet([],'localhost:8008/index.php?c=author&a=delete&code=1',302);
        $this->assertStringContainsString('Record deleted successfully!',$response);
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
        $response = $rest->doGet([],'localhost:8008/index.php?c=book&a=list',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $elements = $xpath->query("/html/body/h1");
        $this->assertEquals('Books',$elements[0]->childNodes[0]->nodeValue);
        $this->assertStringContainsString('muerte anunciada',$response);
        Entity::clear('book');
        Entity::clear('author');        
    }

    public function testListNoBooks()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=book&a=list',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $elements = $xpath->query("/html/body/h1");
        $this->assertEquals('Books',$elements[0]->childNodes[0]->nodeValue);
        Entity::clear('book');
    }   

    public function testNewBook()
    {
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=book&a=edit',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $nodeList = $xpath->query('//input[@type="text"]');
        $this->assertEquals(1,$nodeList->length);
        $node = $nodeList->item(0);
        $this->assertEmpty($node->nodeValue);
    }
    
    public function testEditBook()
    {
        (new AuthorProxy())->save('Sharma','Lakshmi','Raj');
        (new BookProxy())->save('Saba and Nisha',1);
        $rest = new Rest();
        $response = $rest->doGet([],'localhost:8008/index.php?c=book&a=edit&code=1',200);
        $doc = new DomDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXpath($doc);
        $nodeList = $xpath->query('//input[@type="text"]');
        $this->assertEquals(1,$nodeList->length);
        $node = $nodeList->item(0);
        $this->assertEquals('Saba and Nisha',$node->getAttribute('value'));
        Entity::clear('book');
        Entity::clear('author');
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
        $response = $rest->doPost($data, [],'localhost:8008/index.php?c=book&a=save',302);
        $this->assertStringContainsString('Record saved successfully!',$response);
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
        $response = $rest->doPost($data, [],'localhost:8008/index.php?c=book&a=save',302);
        $this->assertStringContainsString('Record updated successfully!',$response);
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
        $response = $rest->doGet([],'localhost:8008/index.php?c=book&a=delete&code=1',302);
        $this->assertStringContainsString('Record deleted successfully!',$response);
        $book = (new BookProxy())->getByCode(1);
        $this->assertEmpty($book->title);
        Entity::clear('book');
        Entity::clear('author');
    }

    public static function tearDownAfterClass():void
    {
        Config::change("'rdb'","'txt'");
        putenv('LIBRARIAN_TEST_ENVIRONMENT=false');
        PHPServer::getInstance()->stop();
    }
}
