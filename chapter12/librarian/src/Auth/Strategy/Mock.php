<?php
namespace Librarian\Auth\Strategy;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Mock implements StrategyInterface
{
    public function hasIdentity(): bool
    {
        return true;
    }

    public function getIdentity(): string
    {
        return 'jack';
    }

    public function clearIdentity(): void
    {        
    }

    public function storeIdentity(string $identity, string $credential): void
    {
    }
}