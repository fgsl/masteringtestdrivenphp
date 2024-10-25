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
            $json = file_get_contents(__DIR__ . '/../../' . self::getConfigFileName());
            $config = (array) json_decode($json);
            self::$config = $config;
        }
        $value = self::$config[$key];
        if (!self::isTestEnvironment() && str_contains(strtolower($value),'mock')){
            throw new \InvalidArgumentException('No mock out of the test environment');
        }
        return is_object($value) ? (array) $value : $value;
    }

    private static function getConfigFileName(): string
    {
        $configFileName = 'book.config.json';
        if (self::isTestEnvironment()){
            $configFileName = 'book.config.test.json';
        }
        return $configFileName;
    }

    public static function isTestEnvironment(): bool
    {
        return (defined('LIBRARIAN_TEST_ENVIRONMENT') && LIBRARIAN_TEST_ENVIRONMENT) ||
        getenv('LIBRARIAN_TEST_ENVIRONMENT');
    }

    public static function override($key, $value): void
    {
        self::$overridedValues[$key] = $value;
    }

    public static function change($key, $value)
    {
        $configFileName = self::getConfigFileName();
        $json = file_get_contents(__DIR__ . '/../../' . $configFileName);
        $config = (array) json_decode($json);
        $config[$key] = $value;
        $object = (object) $config;
        $json = json_encode($object);
        file_put_contents($configFileName, $json);
    }

    public static function clear()
    {        
        self::$config = null;
        self::$overridedValues = [];
    }

    public static function getPathFile(string $key): mixed
    {
        $file = self::get($key);
        return __DIR__ . '/../../' . $file;
    }
}
