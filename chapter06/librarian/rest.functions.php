<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
function getPutData()
{
    $stdin = fopen('php://input', 'r');
    $putData = '';
    while($data = fread($stdin, 1024))
        $putData .= $data;
    fclose($stdin);
    $rows = explode('&',$putData);
    $fields = [];
    foreach($rows as $row)
    {
        $tokens = explode('=', $row);
        $fields[$tokens[0]] = $tokens[1];
    }
    return $fields;
}
