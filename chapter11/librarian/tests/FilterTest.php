<?php
/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Librarian\Filter\TagFilter;
use Librarian\Model\Author;
use Librarian\Model\Book;

/**
 * @covers TagFilter
 * @covers Author
 * @covers Book
*/
#[CoversClass(TagFilter::class)]
#[CoversClass(Author::class)]
#[CoversClass(Book::class)]
class FilterTest extends TestCase
{
    public function testAuthorNamesWithTags()
    {
        $firstName = '<b>José</b>';
        $middleName = '<i>do</i>';
        $lastName = '<h1>Telhado</h1>';

        $author = new Author(0,$firstName,$middleName,$lastName);
        $this->assertEquals('José',$author->firstName);
        $this->assertEquals('do',$author->middleName);
        $this->assertEquals('Telhado',$author->lastName);
    }

    public function testBookTitleWithTags()
    {
        $title = "<script>for(var i=0;i<10;i++){window.alert('hi');}</script>";
        $book = new Book(0,$title);
        $this->assertEquals('for(var i=0;i',$book->title);
    }    
}
