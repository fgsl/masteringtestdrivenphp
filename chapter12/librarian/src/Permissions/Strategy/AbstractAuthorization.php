<?php
namespace Librarian\Permissions\Strategy;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
abstract class AbstractAuthorization implements StrategyInterface
{
    protected $permissionsFormat;
    protected array $permissions;

    public function __construct($permissionsFormat)
    {
        $this->permissionsFormat = $permissionsFormat;
        $this->permissions = $this->permissionsFormat->getPermissions();
    }

    public function isAllowed(string $resource, string $permission): bool
    {
        if (!isset($this->permissions[$resource])){
            return false;
        }
        return in_array($permission, $this->permissions[$resource]);
    }
}