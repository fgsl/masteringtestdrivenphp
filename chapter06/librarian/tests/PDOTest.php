<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\Attributes\CoversNothing;
use Librarian\Test\AbstractDatabaseTest;
use Librarian\Model\PDO\AuthorPDO;
use Librarian\Model\PDO\BookPDO;

class PDOTest extends AbstractDatabaseTest
{
    // author tests
    /**
     * @coversNothing
     */
    #[CoversNothing()]
    public function testSaveAuthorInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $this->assertTrue($authorPDO->save('George', 'Herbert', 'Wells'));
        truncateTable('authors');
    }

    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadAuthorInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $authorPDO->save('Johann', 'Wolfgang', 'Von Goethe');
        $authorPDO->save('Francis', 'Scott', 'Fitzgerald');
        $authorPDO->save('Arthur', 'Conan', 'Doyle');
        $author = $authorPDO->readByCode(2);
        $this->assertEquals('Scott',$author['middle_name']);
        truncateTable('authors');
    }

    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadAuthorsInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $authorPDO->save('Mary', 'Wollstonecraft', 'Shelley');
        $authorPDO->save('Agatha', 'Mary', 'Christie');
        $authorPDO->save('Chaya', 'Pinkhasivna', 'Lispector');
        $authors = $authorPDO->readAuthors();
        $this->assertCount(3,$authors);
        $this->assertEquals('Agatha',$authors[1]['first_name']);
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testUpdateAuthorInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $authorPDO->save('Guy', 'de', 'Maupassant');
        $authorPDO->save('Antoine', 'de', 'Saint-Exupéry');
        $authorPDO->save('Honoré', 'de', 'Balzac');
        $author = $authorPDO->readByCode(1);
        $this->assertEquals('Guy',$author['first_name']);
        $authorPDO->update(1,['first_name' =>'Rudolf','middle_name'=>'Erich','last_name' =>'Raspe']);
        $author = $authorPDO->readByCode(1);
        $this->assertEquals('Rudolf',$author['first_name']);
        truncateTable('authors');        
    }
   
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testDeleteAuthorInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $authorPDO->save('Machado', 'de', 'Assis');
        $authorPDO->save('José', 'de', 'Alencar');
        $authorPDO->save('Rachel', 'de', 'Queiroz');
        $author = $authorPDO->readByCode(2);
        $this->assertEquals('Alencar',$author['last_name']);
        $authorPDO->delete(2);
        $author = $authorPDO->readByCode(2);
        $this->assertEmpty($author);
        truncateTable('authors');
    }    
    // book tests
    /**
     * @coversNothing
     */
    #[CoversNothing()]
    public function testSaveBookInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $bookPDO = new BookPDO();
        $authorPDO->save('Herbert','George','Wells');
        $this->assertTrue($bookPDO->save('The Time Machine',1));
        truncateTable('books');
        truncateTable('authors');
    }
  
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadBookInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $bookPDO = new BookPDO();        
        $authorPDO->save('Johann','Wolfgang','Von Goethe');
        $bookPDO->save('Fausto',1);
        $book = $bookPDO->readByCode(1);
        $this->assertStringContainsString('Fausto',$book['title']);
        truncateTable('books');
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testReadBooksInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $bookPDO = new BookPDO();        
        $authorPDO->save('Agatha','Mary','Christie');
        $bookPDO->save('Murder on the Orient Express',1);
        $bookPDO->save('Death on the Nile',1);
        $bookPDO->save('Halloween Party',1); 
        $books = $bookPDO->readBooks();
        $this->assertCount(3,$books);
        $this->assertStringContainsString('Orient',$books[0]['title']);
        truncateTable('books');
        truncateTable('authors');
    }
    
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testUpdateBookInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $bookPDO = new BookPDO();        
        $authorPDO->save('Honoré','de','Balzac');
        $bookPDO->save('La Vendetta',1);
        $bookPDO->save('Le Contrat de mariage',1);
        $bookPDO->save('La Femme de trente ans',1);
        $book = $bookPDO->readByCode(2);
        $this->assertStringContainsString('mariage',$book['title']);
        $bookPDO->update(2,[
            'title' => 'Un début dans la vie'
        ]);
        $book = $bookPDO->readByCode(2);
        $this->assertStringContainsString('vie',$book['title']);
        truncateTable('books');
        truncateTable('authors');
    }
   
    /**
     * @coversNothing
     */    
    #[CoversNothing()]
    public function testDeleteBookInDatabase()
    {
        $authorPDO = new AuthorPDO();
        $bookPDO = new BookPDO();        
        $authorPDO->save('José','de','Alencar');
        $bookPDO->save('O Guarani',1);
        $bookPDO->save('Iracema',1);
        $bookPDO->save('Ubirajara',1);
        $book = $bookPDO->readByCode(2);
        $this->assertStringContainsString('Iracema',$book['title']);
        $bookPDO->delete(2);
        $book = $bookPDO->readByCode(2);
        $this->assertEmpty($book);
        truncateTable('books');
        truncateTable('authors');
    }
}
