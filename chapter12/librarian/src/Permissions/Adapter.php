<?php
namespace Librarian\Permissions;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Adapter
{
    private $strategy;

    public function __construct()
    {
        $permissionsStrategy = Config::get("permissions_strategy");
        $permissionsFormat = Config::get("permissions_format");
        $className = 'Librarian\Permissions\\Strategy\\' . ucfirst($permissionsStrategy . '\\' . ucfirst($permissionsFormat));
        $format = new $className();        
        $className = 'Librarian\Permissions\\Strategy\\' . ucfirst($permissionsStrategy);
        $this->strategy = new $className($format);
    }

    public function isAllowed(string $resource, string $permission): bool
    {
        return $this->strategy->isAllowed($resource,$permission);
    }

}