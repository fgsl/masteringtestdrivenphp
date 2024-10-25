<?php
namespace Librarian\Auth\Strategy;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Session implements StrategyInterface
{
    public function hasIdentity(): bool
    {
        return isset($_SESSION['USERNAME']);
    }

    public function getIdentity(): string
    {
        return $_SESSION['USERNAME'] ?? '';
    }

    public function clearIdentity(): void
    {
        unset($_SESSION['USERNAME']);
    }

    public function storeIdentity(string $identity, string $credential): void
    {
        $_SESSION['USERNAME'] = $identity;
    }
}