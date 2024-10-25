<?php
namespace Librarian\Model;
use Librarian\Filter\TagFilter;
use Librarian\Validator\NameValidator;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Author extends AbstractCode
{
    public string $firstName;
    public string $middleName;
    public string $lastName;

    public function __construct(int $code = 0, string $firstName = '', string $middleName = '', string $lastName = '')
    {
        $filter = new TagFilter();
        $this->code = $code;
        $this->firstName = $filter($firstName);
        $this->middleName = $filter($middleName);
        $this->lastName = $filter($lastName);
    }

    public function isValid(): bool
    {
        $validated = new NameValidator();
        return $validated($this->firstName) && $validated($this->middleName) && $validated($this->lastName);
    }
}
