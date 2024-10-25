<?php
namespace Librarian\Auth\Strategy;
use Fgsl\Jwt\Jwt;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Api implements StrategyInterface
{
    public static string $token = '';

    public function hasIdentity(): bool
    {
        if (!isset($_GET['token'])){
            return false;
        }
        return !Jwt::expired($_GET['token']);
    }

    public function getIdentity(): string
    {
        if (!isset($_GET['token'])){
            return '';
        }        
        $payload = Jwt::getPayload($_GET['token']);
        return $payload->sub;
    }

    public function clearIdentity(): void
    {
        unset($_GET['token']);
    }

    public function storeIdentity(string $identity, string $credential): void
    {
        $jwt = new Jwt(['RS256','sha256'], 'JWT', 'librarian.com', 'PT2H'); // 2 hours for expiring
        self::$token = $jwt->getBearerToken($identity,$credential);
    }
}