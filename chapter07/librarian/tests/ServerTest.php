<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Librarian\Test\PHPServer;

class ServerTest extends TestCase
{
    /**
     * @coversNothing
     */
    #[CoversNothing()]    
    public function testServer()
    {
        $server = PHPServer::getInstance();
        $this->assertInstanceOf(PHPServer::class, $server);
        $anotherServer = PHPServer::getInstance();
        $this->assertEquals(spl_object_id($server),spl_object_id($anotherServer));
    }
}
