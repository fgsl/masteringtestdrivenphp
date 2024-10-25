<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Util\Config;
use Librarian\Auth\Adapter as AuthAdapter;

/**
 * @covers AuthAdapter
 * */
#[CoversClass(AuthAdapter::class)]
class AuthenticationTest extends TestCase
{
    public function testAuthenticationWithSession()
    {        
        Config::override('auth_strategy','session');
        $this->doAuthentication();
    }

    public function testAuthenticationWithToken()
    {        
        Config::override('auth_strategy','token');
        $this->doAuthentication();
    }

    private function doAuthentication()
    {
        $username = 'jack';
        $password = 'MySecret123@';
        $authAdapter = new AuthAdapter();
        $authAdapter->setIdentity($username)
        ->setCredential($password);
        $authAdapter->authenticate();        
        $this->assertTrue($authAdapter->hasIdentity());
        $authAdapter->clearIdentity();
    }
}
