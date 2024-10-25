<?php
namespace Librarian\Validator;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class TitleValidator
{
    public function __invoke($value)
    {
        return $this->isValid($value);
    }

    public function isValid(mixed $value): bool
    {
        $length = strlen($value);
        return $length>=3 && $length<=80;
    }
}
