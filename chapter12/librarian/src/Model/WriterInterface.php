<?php
namespace Librarian\Model;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
interface WriterInterface
{
    public function update(int $code, array $data): bool;
    public function delete(int $code): bool;
}
