<?php
namespace Librarian\Auth\Strategy;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
interface StrategyInterface
{
    public function hasIdentity(): bool;
    public function getIdentity(): string;
    public function clearIdentity(): void;
    public function storeIdentity(string $identity, string $credential): void;
}