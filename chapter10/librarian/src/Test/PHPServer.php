<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
namespace Librarian\Test;

final class PHPServer 
{
    private $process;
    private static $instance = null;

    private function __construct()
    {}

    public static function getInstance()
    {
        if (self::$instance == null){
            self::$instance = new PHPServer();
        }
        return self::$instance;
    }

    public function start(): bool
    {
        $output = shell_exec('ps -aux | grep "php -S"');
        if (str_contains($output, 'localhost:8008')){
            return true;
        }
        $path = realpath(__DIR__ . '/../../');
        $descriptorspec = array(
            0 => ["pipe", "r"], 
            1 => ["pipe", "w"],
            2 => ["file", "/dev/null", "a"]
         );
        $process = proc_open('nohup php -S localhost:8008 router.php &',$descriptorspec,$path);
        sleep(1);
        if (is_bool($process)){
            return false;
        }
        $this->process = $process;
        return true;
    }

    public function stop(): bool
    {
        $argv = $_SERVER['argv'];
        if (isset($argv[1]) && $argv[1] == '--coverage-html'){
            return false;
        }

        if (is_null($this->process)){
            $output = shell_exec('ps -aux | grep "php -S"');
            if (str_contains($output, 'localhost:8008')){
                do {
                    $output = str_replace('  ',' ',$output, $count);   
                } while ($count > 0);   
                $pieces = explode(' ',$output);
                $pid = $pieces[1];
                shell_exec('kill -9 ' . $pid);
                return true;
            }
        }
        $running = true;
        while ($running){
            $status = proc_get_status($this->process);
            $running = $status['running'];
            proc_terminate($this->process);
        }
        return true;
    }

}
