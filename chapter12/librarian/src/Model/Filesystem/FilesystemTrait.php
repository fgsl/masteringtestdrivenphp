<?php
namespace Librarian\Model\Filesystem;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
trait FilesystemTrait
{
    const CODE_LENGTH = 4;

    public function formatField($field, int $length)
    {
        return str_pad($field, $length, ' ', STR_PAD_LEFT);
    }   
}
