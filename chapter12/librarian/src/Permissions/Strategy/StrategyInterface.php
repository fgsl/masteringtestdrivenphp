<?php
namespace Librarian\Permissions\Strategy;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
interface StrategyInterface
{
    public function isAllowed(string $resource, string $permission): bool;
}