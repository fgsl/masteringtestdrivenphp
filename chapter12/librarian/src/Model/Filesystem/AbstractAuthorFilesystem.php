<?php
namespace Librarian\Model\Filesystem;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
abstract class AbstractAuthorFilesystem
{
    const ROW_LENGTH = 65;
    const NAME_LENGTH = 20;        

    use FilesystemTrait;
}
