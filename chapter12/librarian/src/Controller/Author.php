<?php
namespace Librarian\Controller;
use Librarian\Model\AuthorRowSet;
use Librarian\Model\Filesystem\AuthorPlainTextFinder;
use Librarian\Model\Filesystem\AuthorCSVFinder;
use Librarian\Model\Filesystem\AuthorJSONFinder;
use Librarian\Model\ORM\AuthorFinder;
use Librarian\Model\ODM\AuthorCollectionFinder;
use Librarian\Util\Config;
use Librarian\Model\FinderInterface;
use Librarian\Model\Author as AuthorModel;

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
class Author extends AbstractPageController
{
    use CRUDControllerTrait;

    protected AuthorModel $author;

    public function edit(): void
    {
        $code = $_GET['code'] ?? 0;
        $this->author = $this->getByCode($code);
    }

    public function save(): void
    {
        $code = $_POST['code'] ?? 0;
        $firstName = $_POST['first_name'] ?? null;
        $middleName = $_POST['middle_name'] ?? null;
        $lastName = $_POST['last_name'] ?? null;
        $message = 'The record has not been recorded';
        if ($firstName == null || $middleName == null || $lastName == null){
            $message = 'No data, no recording';
        }
        if ($code == 0 && $this->insert($lastName,$middleName,$firstName)) {
            $message = 'Record saved successfully!';
        }
        if ($code <> 0 && $this->update($code,$lastName,$middleName,$firstName)) {
            $message = 'Record updated successfully!';
        }
        $this->redirect('author','message',['message' => base64_encode($message)]); 
    }

    protected function insert($lastName, $middleName, $firstName): bool
    {
        $dataGateway = $this->getDataGateway();
        try {
            return (new $dataGateway())->save(new AuthorModel(0,$firstName,$middleName,$lastName));
        } catch (\Exception $e){
            return false;
        }
    }
    
    protected function update($code, $lastName, $middleName, $firstName): bool
    {
        $storageFormat = Config::get('storage_format');
    
        $data = [
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'first_name' => $firstName
        ];
        $dataGateway = $this->getDataGateway();
        try {
            return (new $dataGateway())->update($code, [
                'last_name' => $lastName, 
                'middle_name' => $middleName, 
                'first_name' => $firstName]
            );
        } catch (\Exception $e){
            return false;
        }        
    }

    protected function listAuthorsInTable(): string
    {
        try {
            $authors = $this->getAuthorFinder()->readAll();        
        } catch(\Exception $e) {
            $authors = new AuthorRowSet();
        }
        $html = '';
        foreach($authors as $author){
            $html.='<tr>';
            $html.='<td><a href="index.php?c=author&a=edit&code=' . $author->code . '">' . $author->code . '</a><td>';
            $html.="<td>{$author->firstName} {$author->middleName} {$author->lastName}<td>";
            $html.='<td><a href="index.php?c=author&a=delete&code=' . $author->code . '">remove</a><td>';
            $html.='</tr>';
        }
        return $html;
    }
    
    public function getAuthorFinder(): FinderInterface
    {
        $storageFormat = Config::get('storage_format');
        switch($storageFormat){
            case 'txt':
                return new AuthorPlainTextFinder();
            case 'csv':
                return new AuthorCSVFinder();
            case 'json':
                return new AuthorJSONFinder();
            case 'rdb':
                return new AuthorFinder();
            case 'ddb':
                return new AuthorCollectionFinder();
        }
        throw new \Exception('invalid storage format');
    }    
}
