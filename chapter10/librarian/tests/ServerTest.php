<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Test\PHPServer;

/**
 * @covers PHPServer
 */
#[CoversClass(PHPServer::class)]
class ServerTest extends TestCase
{
    public function testServer()
    {
        $server = PHPServer::getInstance();
        $this->assertInstanceOf(PHPServer::class, $server);
        $anotherServer = PHPServer::getInstance();
        $this->assertEquals(spl_object_id($server),spl_object_id($anotherServer));
    }
}
