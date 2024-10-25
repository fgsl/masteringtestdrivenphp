<?php
namespace Librarian\Auth;
use Librarian\Util\Config;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Adapter
{
    private $format;
    private string $identity;
    private string $credential;
    private $strategy;

    public function __construct()
    {
        $authStrategy = Config::get("auth_strategy");
        $className = 'Librarian\Auth\\Strategy\\' . ucfirst($authStrategy);
        $this->strategy = new $className();
        $authFormat = Config::get("auth_format");
        $className = 'Librarian\Auth\\' . ucfirst($authFormat);
        $this->format = new $className($this->strategy);
    }

    public function setIdentity(string $identity): Adapter
    {
        $this->identity = $identity;
        return $this;
    }

    public function setCredential(string $credential): Adapter
    {
        $this->credential = $credential;
        return $this;
    }

    public function authenticate(): void
    {
        $this->format->authenticate($this->identity,$this->credential);
    }

    public function isValid(): bool
    {
        return $this->format->isValid();
    }

    public function hasIdentity(): bool
    {
        return $this->strategy->hasIdentity();
    }

    public function getIdentity(): string
    {
        return $this->strategy->getIdentity();
    }

    public function clearIdentity(): void
    {
        $this->strategy->clearIdentity();
    }
}