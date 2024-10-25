<?php
namespace Librarian\Permissions\Strategy\Acl;
use Librarian\Auth\Adapter;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Xml
{
    public function getPermissions(): array
    {
        $authAdapter = new Adapter();
        $identity = $authAdapter->getIdentity();

        $file = __DIR__ . '/../../../../data/acl.xml';
        $xml = simplexml_load_file($file);
        $result = $xml->xpath('//user[@name="' . $identity . '"]');
        $resources = [];
        foreach($result[0] as $resource){
            $permissions = (array)$resource->permission;
            $name = (string) $resource->attributes()->name;
            $resources[$name] = $permissions;
        }
        return $resources;
    }
}