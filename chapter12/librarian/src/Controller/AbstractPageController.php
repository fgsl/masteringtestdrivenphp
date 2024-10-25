<?php
namespace Librarian\Controller;
use Librarian\Auth\Adapter as AuthAdapter;
use Librarian\Permissions\Adapter as PermissionAdapter;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
abstract class AbstractPageController
{
    private string $controller;

    public function run(string $action): void
    {
        session_start();
        $tokens = (explode('\\',get_class($this)));
        $this->controller = lcfirst(end($tokens));

        $authAdapter = new AuthAdapter();
        if (!$authAdapter->hasIdentity() && 
            $action != 'login' &&
            !($action == 'index' && $this->controller == 'index')
        )
        {
            $this->redirect('index','index');
        }

        $permissionAdapter = new PermissionAdapter();
        if ($this->controller !== 'index' && !$permissionAdapter->isAllowed($this->controller,$action))
        {
            $this->redirect('index','menu');
        }

        $this->$action();
        require __DIR__ . '/../../view/layout.phtml'; 
    }

    protected function redirect(string $controller, string $action, array $args = [])
    {
        $parameters = '';
        foreach($args as $arg => $value){
            $parameters .= "&$arg=$value";
        }
        header("Location: index.php?c=$controller&a=$action" . $parameters);
        exit;
    } 
}
