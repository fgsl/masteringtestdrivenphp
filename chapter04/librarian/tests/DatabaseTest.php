<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Fgsl\Rest\Rest;

class DatabaseTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        replaceConfigFileContent("'database' => 'librarian'","'database' => 'librarian_test'");
    }

    /**
     * @coversNothing
     */
    #[CoversNothing()]
    public function testSaveAuthorInDatabase()
    {
        $this->assertTrue(saveAuthorInDatabase('Wells','Herbert','George'));
        truncateTable('authors');
    }

    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadAuthorInDatabase()
    {
        saveAuthorInDatabase('Von Goethe','Wolfgang','Johann');
        saveAuthorInDatabase('Fitzgerald','Scott','Francis');
        saveAuthorInDatabase('Doyle','Arthur','Conan');
        $author = readAuthorInDatabaseByCode(2);
        $this->assertEquals('Scott',$author['middle_name']);
        truncateTable('authors');        
    }

    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadAuthorsInDatabase()
    {
        saveAuthorInDatabase('Shelley','Wollstonecraft','Mary');
        saveAuthorInDatabase('Christie','Mary','Agatha');
        saveAuthorInDatabase('Lispector','Pinkhasivna','Chaya');
        $authors = readAuthorsInDatabase();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors[1]['first_name']);
        $filepath = getConfig()['author_plaintext_filepath'];
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testUpdateAuthorInDatabase()
    {
        saveAuthorInDatabase('Maupassant','de','Guy');
        saveAuthorInDatabase('Saint-Exupéry','de','Antoine');
        saveAuthorInDatabase('Balzac','de','Honoré');
        $author = readAuthorInDatabaseByCode(1);
        $this->assertEquals('Guy',$author['first_name']);
        updateAuthorInDatabase(1,[
            'last_name' => 'Raspe',
            'middle_name' => 'Erich',
            'first_name' => 'Rudolf'
        ]);
        $author = readAuthorInDatabaseByCode(1);
        $this->assertEquals('Rudolf',$author['first_name']);
        truncateTable('authors');        
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testDeleteAuthorInDatabase()
    {
        saveAuthorInDatabase('Assis','de','Machado');
        saveAuthorInDatabase('Alencar','de','José');
        saveAuthorInDatabase('Queiroz','de','Rachel');
        $author = readAuthorInDatabaseByCode(2);
        $this->assertEquals('Alencar',$author['last_name']);
        deleteAuthorInDatabase(2);
        $author = readAuthorInDatabaseByCode(2);
        $this->assertEmpty($author);
        truncateTable('authors');
    }    

    private function truncateTable(string $table)
    {
        $mysqli = getConnection();
        $mysqli->query('DELETE FROM ' . $table);
        $mysqli->query('ALTER TABLE ' . $table . ' AUTO_INCREMENT = 1');
    }

    public static function tearDownAfterClass():void
    {
        proc_terminate(self::$process);
        replaceConfigFileContent("'database' => 'librarian_test'","'database' => 'librarian'");
    }    
}
