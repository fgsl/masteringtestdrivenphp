<?php
namespace Librarian\Model;
use Librarian\Filter\TagFilter;
use Librarian\Validator\TitleValidator;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Book extends AbstractCode
{
    public string $title;
    public ?Author $author;
    
    public function __construct(int $code = 0, string $title = '', Author $author = null)
    {
        $filter = new TagFilter();
        $this->code = $code;
    	$this->title = $filter($title);
    	$this->author = is_null($author) ? new Author() : $author;
    }

    public function isValid(): bool
    {
        $validated = new TitleValidator();
        return $validated($this->title);
    }
}
