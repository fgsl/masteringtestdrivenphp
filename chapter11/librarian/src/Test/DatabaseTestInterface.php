<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
namespace Librarian\Test;

interface DatabaseTestInterface
{
    // author tests
    public function testSaveAuthor();

    public function testReadAuthor();

    public function testReadAuthors();
    
    public function testUpdateAuthor();
    
    public function testDeleteAuthor();
    // book tests
  
    public function testReadBook();
    
    public function testReadBooks();
    
    public function testUpdateBook();
   
    public function testDeleteBook();
}
