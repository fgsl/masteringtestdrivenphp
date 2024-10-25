<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Util\Config;

/**
 * @covers Config
*/
#[CoversClass(Config::class)]
class ConfigTest extends TestCase
{
    public function testConfig()
    {
        Config::clear();
        Config::change('storage_format','csv');
        $this->assertEquals('csv', Config::get('storage_format'));
        Config::change('storage_format','txt');
        $this->assertEquals('csv', Config::get('storage_format'));
        Config::clear();
        $this->assertEquals('txt', Config::get('storage_format'));
        Config::override('storage_format','json');
        $this->assertEquals('json', Config::get('storage_format'));
    }
}
