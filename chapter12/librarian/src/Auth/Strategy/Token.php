<?php
namespace Librarian\Auth\Strategy;
use Fgsl\Jwt\Jwt;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Token implements StrategyInterface
{
    private ?String $bearerToken = null;

    public function hasIdentity(): bool
    {
        if (!isset($_COOKIE['BEARER_TOKEN']) && $this->bearerToken == null){
            return false;
        }
        $bearerToken = $_COOKIE['BEARER_TOKEN'] ?? $this->bearerToken; 
        return !Jwt::expired($bearerToken);
    }

    public function getIdentity(): string
    {
        if (!isset($_COOKIE['BEARER_TOKEN'])){
            return '';
        }        
        $payload = Jwt::getPayload($_COOKIE['BEARER_TOKEN']);
        return $payload->sub;
    }

    public function clearIdentity(): void
    {
        setcookie('BEARER_TOKEN','',time()-3600);
    }

    public function storeIdentity(string $identity, string $credential): void
    {
        $jwt = new Jwt(['RS256','sha256'], 'JWT', 'librarian.com', 'PT2H'); // 2 hours for expiring
        $bearerToken = $jwt->getBearerToken($identity,$credential);
        $this->bearerToken = $bearerToken; 
        setcookie('BEARER_TOKEN',$bearerToken,time()+7200);
    }
}