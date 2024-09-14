<?php
namespace Librarian\Util;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Config
{
    private static ?array $config = null;
    private static array $overridedValues = [];

    public static function get(string $key): mixed
    {
        if (isset(self::$overridedValues[$key])){
            return self::$overridedValues[$key];
        }
        if (self::$config == null){
            self::$config = require __DIR__ . '/../../' . self::getConfigFileName();
        }        
        return self::$config[$key];
    }

    private static function getConfigFileName(): string
    {
        $configFileName = 'book.config.php';
        if ((defined('LIBRARIAN_TEST_ENVIRONMENT') && LIBRARIAN_TEST_ENVIRONMENT) ||
        getenv('LIBRARIAN_TEST_ENVIRONMENT')){
            $configFileName = 'book.config.test.php';
        }
        return $configFileName;
    }

    public static function override($key, $value): void
    {
        self::$overridedValues[$key] = $value;
    }

    public static function change($current, $new)
    {
        $configFileName = __DIR__ . '/../../' . self::getConfigFileName();
        $content = file_get_contents($configFileName);
        $content = str_replace($current, $new, $content);
        file_put_contents($configFileName, $content);
    }
}
