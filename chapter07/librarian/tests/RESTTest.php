    <?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Librarian\Model\REST\AuthorREST;
use Librarian\Model\REST\BookREST;
use Fgsl\Rest\Rest;
use Librarian\Test\PHPServer;

class RESTTest extends TestCase
{
    public static function setUpBeforeClass(): void 
    {
        replaceConfigFileContent("'database' => 'librarian'","'database' => 'librarian_test'");        
        replaceConfigFileContent('dbname=librarian','dbname=librarian_test');        
        PHPServer::getInstance()->start();
    }

    // author tests
    /**
     * @coversNothing
     */
    #[CoversNothing()]
    public function testSaveAuthor()
    {        
        truncateTable('authors');
        $url = 'http://localhost:8008/author.php';
        $rest = new Rest();
        $data = [
            'first_name' => 'George',
            'middle_name' => 'Herbert',
            'last_name' => 'Wells'
        ];
        $response = $rest->doPost($data, [], $url, 200);
        $json = json_decode($response);
        $this->assertIsObject($json);
        $this->assertObjectHasProperty('included',$json);
        $this->assertTrue($json->included);
        truncateTable('authors');
    }

    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadAuthor()
    {
        truncateTable('authors');
        $url = 'http://localhost:8008/author.php';
        $rest = new Rest();
        $data = [
            'first_name' => 'Johann',
            'middle_name' => 'Wolfgang',
            'last_name' => 'Von Goethe'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Francis',
            'middle_name' => 'Scott',
            'last_name' => 'Fitzgerald'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Arthur',
            'middle_name' => 'Conan',
            'last_name' => 'Doyle'
        ];
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $author = json_decode($response);
        $this->assertEquals('Scott',$author->middle_name);
        truncateTable('authors');
    }

    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadAuthors()
    {
        truncateTable('authors');
        $url = 'http://localhost:8008/author.php';
        $rest = new Rest();
        $data = [
            'first_name' => 'Mary',
            'middle_name' => 'Wollstonecraft',
            'last_name' => 'Shelley'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Agatha',
            'middle_name' => 'Mary',
            'last_name' => 'Christie'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Chaya',
            'middle_name' => 'Pinkhasivna',
            'last_name' => 'Lispector'
        ];
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url, 200);
        $authors = json_decode($response);
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors[1]->first_name);
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testUpdateAuthor()
    {
        truncateTable('authors');
        $url = 'http://localhost:8008/author.php';
        $rest = new Rest();
        $data = [
            'first_name' => 'Guy',
            'middle_name' => 'de',
            'last_name' => 'Maupassant'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Antoine',
            'middle_name' => 'de',
            'last_name' => 'Saint-Exupéry'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Honoré',
            'middle_name' => 'de',
            'last_name' => 'Balzac'
        ];
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=1', 200);
        $author = json_decode($response);
        $this->assertEquals('Guy',$author->first_name);
        $data = $this->getAuthorArray('Rudolf','Erich','Raspe');
        $data['code'] = $author->code;
        $rest->doPut($data,[], $url, 200);
        $response = $rest->doGet([], $url . '?code=1', 200);
        $author = json_decode($response);
        $this->assertEquals('Rudolf',$author->first_name);
        truncateTable('authors');
    }
   
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testDeleteAuthor()
    {
        truncateTable('authors');
        $url = 'http://localhost:8008/author.php';
        $rest = new Rest();
        $data = [
            'first_name' => 'Machado',
            'middle_name' => 'de',
            'last_name' => 'Assis'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'José',
            'middle_name' => 'de',
            'last_name' => 'Alencar'
        ];
        $rest->doPost($data, [], $url, 200);
        $data = [
            'first_name' => 'Rachel',
            'middle_name' => 'de',
            'last_name' => 'Queiroz'
        ];
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $author = json_decode($response);
        $this->assertEquals('Alencar',$author->last_name);
        $rest->doDelete([], $url . '?code=2', 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $author = json_decode($response);
        $this->assertEmpty($author);
        truncateTable('authors');
    }    
    // book tests
    /**
     * @coversNothing
     */
    #[CoversNothing()]
    public function testSaveBook()
    {
        truncateTable('books');
        truncateTable('authors');
        $rest = new Rest();
        $data = $this->getAuthorArray('Herbert','George','Wells');
        $url = 'http://localhost:8008/author.php';
        $rest->doPost($data, [], $url, 200);
        $data = [
            'title' => 'The Time Machine',
            'author_code' => 1
        ];
        $url = 'http://localhost:8008/book.php';
        $response = $rest->doPost($data, [], $url, 200);
        $json = json_decode($response);
        $this->assertIsObject($json);
        $this->assertObjectHasProperty('included',$json);
        $this->assertTrue($json->included); 
        truncateTable('books');
        truncateTable('authors');
    }
  
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadBook()
    {
        truncateTable('books');
        truncateTable('authors');        
        $rest = new Rest();
        $data = $this->getAuthorArray('Johann','Wolfgang','Von Goethe');
        $url = 'http://localhost:8008/author.php';
        $rest->doPost($data, [], $url, 200);
        $data = [
            'title' => 'Fausto',
            'author_code' => 1
        ];
        $url = 'http://localhost:8008/book.php';
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=1', 200);
        $book = json_decode($response);
        $this->assertStringContainsString('Fausto',$book->title);
        truncateTable('books');
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadBooks()
    {
        truncateTable('books');
        truncateTable('authors');
        $rest = new Rest();
        $data = $this->getAuthorArray('Agatha','Mary','Christie');
        $url = 'http://localhost:8008/author.php';
        $rest->doPost($data, [], $url, 200);
        $url = 'http://localhost:8008/book.php';
        $data = $this->getBookArray('Murder on the Orient Express',1);
        $rest->doPost($data, [], $url, 200);
        $data = $this->getBookArray('Death on the Nile',1);
        $rest->doPost($data, [], $url, 200);
        $data = $this->getBookArray('Halloween Party',1);
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url, 200);
        $books = json_decode($response);        
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books[0]->title);
        truncateTable('books');
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testUpdateBook()
    { 
        truncateTable('books');
        truncateTable('authors');
        $rest = new Rest();
        $data = $this->getAuthorArray('Honoré','de','Balzac');
        $url = 'http://localhost:8008/author.php';
        $rest->doPost($data, [], $url, 200);
        $url = 'http://localhost:8008/book.php';
        $data = $this->getBookArray('La Vendetta',1);
        $rest->doPost($data, [], $url, 200);
        $data = $this->getBookArray('Le Contrat de mariage',1);
        $rest->doPost($data, [], $url, 200);
        $data = $this->getBookArray('La Femme de trente ans',1);
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $book = json_decode($response);
        $this->assertStringContainsString('mariage',$book->title);
        $data = [
            'title' => 'Un début dans la vie',
            'code' => 2
        ];
        $rest->doPut($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $book = json_decode($response);
        $this->assertStringContainsString('vie',$book->title);
        truncateTable('books');
        truncateTable('authors');
    }
   
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testDeleteBook()
    {
        truncateTable('books');
        truncateTable('authors');
        $rest = new Rest();
        $data = $this->getAuthorArray('José','de','Alencar');
        $url = 'http://localhost:8008/author.php';
        $rest->doPost($data, [], $url, 200);
        $url = 'http://localhost:8008/book.php';
        $data = $this->getBookArray('O Guarani',1);
        $rest->doPost($data, [], $url, 200);
        $data = $this->getBookArray('Iracema',1);
        $rest->doPost($data, [], $url, 200);        
        $data = $this->getBookArray('Ubirajara',1);
        $rest->doPost($data, [], $url, 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $book = json_decode($response);                
        $this->assertStringContainsString('Iracema',$book->title);
        $response = $rest->doDelete([], $url . '?code=2', 200);
        $response = $rest->doGet([], $url . '?code=2', 200);
        $book = json_decode($response);
        $this->assertEmpty($book);
        truncateTable('books');
        truncateTable('authors');
    }

    private function getAuthorArray(string $firstName, string $middleName, string $lastName)
    {
        return [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName
        ];
    }

    private function getBookArray(string $title, int $authorCode)
    {
        return [
            'title' => $title,
            'author_code' => $authorCode
        ];
    }    

    public static function tearDownAfterClass(): void
    {
        replaceConfigFileContent("'database' => 'librarian_test'","'database' => 'librarian'");        
        replaceConfigFileContent('dbname=librarian_test','dbname=librarian');
        PHPServer::getInstance()->stop();
    }
}
