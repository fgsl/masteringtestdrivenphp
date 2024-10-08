<?php
namespace Librarian\Filter;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class TagFilter
{
    public function __invoke($value)
    {
        return $this->filter($value);
    }

    public function filter(mixed $value): mixed
    {
        return strip_tags($value);
    }
}
