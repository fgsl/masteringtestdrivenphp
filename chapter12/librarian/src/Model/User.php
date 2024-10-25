<?php
namespace Librarian\Model;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class User 
{
    public function encrypt($text)
    {
        return md5(strrev(soundex($text))) . 'SH4Z4M';
    }
}
