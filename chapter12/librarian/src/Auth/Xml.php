<?php
namespace Librarian\Auth;
use Librarian\Util\Config;
use Librarian\Model\User;
use Librarian\Auth\Strategy\StrategyInterface;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Xml
{
    private StrategyInterface $strategy;

    private $valid = false;

    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function authenticate(string $identity, string $credential): void
    {
        $this->valid = false;
        $file = __DIR__ . '/../../data/users.xml';
        $xml = simplexml_load_file($file);
        $result = $xml->xpath('//user[username="' . $identity . '"]');
        if (!is_array($result) || count($result) == 0){
            return;
        }
        $user = new User();
        if (isset($result[0]->password) && $result[0]->password == $user->encrypt($credential)){
            $this->valid = true;
            $this->strategy->storeIdentity($identity,$credential); 
        }
    }

    public function isValid(): bool
    {
        return $this->valid;
    }
}