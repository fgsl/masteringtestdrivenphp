<?php
namespace Librarian\Controller;
use Librarian\Auth\Adapter;
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Index extends AbstractPageController
{
    public function index(): void
    {
        
    }

    public function login(): void
    {
        error_log(print_r($_POST,true));

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $authAdapter = new Adapter();
        $authAdapter->setIdentity($username)
        ->setCredential($password);
        $authAdapter->authenticate();
        $action = 'index';
        if ($authAdapter->isValid()){
            $action = 'menu';
        }
        $this->redirect('index',$action);
    }

    public function menu(): void
    {

    }

    public function logout()
    {
        $authAdapter = new Adapter();
        $authAdapter->clearIdentity();
        $this->redirect('index','index');
    }
}
