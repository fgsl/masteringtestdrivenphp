<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Util\Config;
use Librarian\Auth\Adapter as AuthAdapter;
use Librarian\Permissions\Adapter as PermissionAdapter;

/**
 * @covers AuthAdapter
 * @covers PermissionAdapter
 * */
#[CoversClass(AuthAdapter::class)]
#[CoversClass(PermissionAdapter::class)]
class AuthorizationTest extends TestCase
{
    public function testAuthorizationWithAcl()
    {        
        Config::override('permissions_strategy','acl');
        $this->doAuthorization();
    }

    public function testAuthorizationWithRbac()
    {        
        Config::override('permissions_strategy','rbac');
        $this->doAuthorization();
    }

    private function doAuthorization()
    {
        Config::override('auth_strategy','session');
        $username = 'jack';
        $password = 'MySecret123@';
        $authAdapter = new AuthAdapter();
        $authAdapter->setIdentity($username)
        ->setCredential($password);
        $authAdapter->authenticate();
        $permissionAdapter = new PermissionAdapter();        
        $this->assertTrue($permissionAdapter->isAllowed('author','list'));
        $this->assertTrue($permissionAdapter->isAllowed('book','list'));
        $authAdapter->clearIdentity();
    }
}
