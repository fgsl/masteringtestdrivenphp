<?php
const AUTHOR_ROW_LENGTH = 65;
const BOOK_ROW_LENGTH = 89;

require_once 'txt.functions.php';
require_once 'csv.functions.php';
require_once 'json.functions.php';

/**
 * @author Flávio Gomes da Silva Lisboa
 * @license LGPL-3.0 license <https://www.gnu.org/licenses/lgpl-3.0.html.en>
 */
function getConfig()
{
    return require 'book.config.php';
}

function formatField($field, int $length)
{
    return str_pad($field, $length, ' ', STR_PAD_LEFT);
}

//author
function saveAuthor($lastName, $middleName, $firstName)
{
    $fileFormat = getConfig()['file_format'];

    $saved = false;
    switch($fileFormat){
        case 'txt':
            $saved = saveAuthorInPlainText($lastName, $middleName, $firstName);
            break;
        case 'csv':
            $saved = saveAuthorInCSV($lastName, $middleName, $firstName);
            break;
        case 'json':
            $saved = saveAuthorInJSON($lastName, $middleName, $firstName);
    }
    return $saved;
}

function listAuthorsInTable()
{
    $fileFormat = getConfig()['file_format'];
    $authors = [];
    switch($fileFormat){
        case 'txt':
            $authors = readAuthorsInPlainText();
            break;
        case 'csv':
            $authors = readAuthorsInCSV();
            break;
        case 'json':
            $authors = readAuthorsInJSON();
    }
    $html = '';
    foreach($authors as $author){
        $html.='<tr>';
        $html.='<td><a href="author.edit.php?code=' . $author['code'] . '">' . $author['code'] . '</a><td>';
        $html.="<td>{$author['first_name']} {$author['middle_name']} {$author['last_name']}<td>";
        $html.='<td><a href="author.delete.php?code=' . $author['code'] . '">remove</a><td>';
        $html.='</tr>';
    }
    return $html;
}

function listBooksForSelect($code)
{
    $fileFormat = getConfig()['file_format'];
    $authors = [];
    switch($fileFormat){
        case 'txt':
            $authors = readAuthorsInPlainText();
            break;
        case 'csv':
            $authors = readAuthorsInCSV();
            break;
        case 'json':
            $authors = readAuthorsInJSON();
    }
    $html = '';
    foreach($authors as $author){
        $authorName = $author['first_name'] . ' ' . $author['middle_name'] . ' ' . $author['last_name'];
        $html.='<option value="' . $author['code'] . '"' . ($author['code'] == $code ? ' selected ' : '') . '>' . $authorName . '</option>';
    }
    return $html;
}

function getAuthorByCode($code)
{
    $fileFormat = getConfig()['file_format'];

    $author = [];
    switch($fileFormat){
        case 'txt':
            $author = readAuthorInPlainTextByCode($code);
            break;
        case 'csv':
            $author = readAuthorInCSVByCode($code);
            break;
        case 'json':
            $author = readAuthorInJSONByCode($code);
    }
    if (empty($author)){
        $author = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => ''
        ];
    }
    return $author;
}

function updateAuthor($code, $lastName, $middleName, $firstName)
{
    $fileFormat = getConfig()['file_format'];

    $data = [
        'last_name' => $lastName,
        'middle_name' => $middleName,
        'first_name' => $firstName
    ];

    $saved = false;
    switch($fileFormat){
        case 'txt':
            $saved = updateAuthorInPlainText($code, $data);
            break;
        case 'csv':
            $saved = updateAuthorInCSV($code, $data);
            break;
        case 'json':
            $saved = updateAuthorInJSON($code, $data);
    }
    return $saved;
}

function deleteAuthor($code)
{
    $fileFormat = getConfig()['file_format'];

    $deleted = false;
    switch($fileFormat){
        case 'txt':
            $deleted = deleteAuthorInPlainText($code);
            break;
        case 'csv':
            $deleted = deleteAuthorInCSV($code);
            break;
        case 'json':
            $deleted = deleteAuthorInJSON($code);
    }
    return $deleted;
}

//book
function saveBook($title, $authorCode)
{
    $fileFormat = getConfig()['file_format'];

    $saved = false;
    switch($fileFormat){
        case 'txt':
            $saved = saveBookInPlainText($title, $authorCode);
            break;
        case 'csv':
            $saved = saveBookInCSV($title, $authorCode);
            break;
        case 'json':
            $saved = saveBookInJSON($title, $authorCode);
    }
    return $saved;
}

function listBooksInTable()
{
    $fileFormat = getConfig()['file_format'];
    $books = [];
    switch($fileFormat){
        case 'txt':
            $books = readBooksInPlainText();
            break;
        case 'csv':
            $books = readBooksInCSV();
            break;
        case 'json':
            $books = readBooksInJSON();
    }
    $html = '';
    foreach($books as $book){
        $html.='<tr>';
        $html.='<td><a href="book.edit.php?code=' . $book['code'] . '">' . $book['code'] . '</a><td>';
        $html.="<td>{$book['title']}<td>";
        $author = getAuthorByCode($book['author_code']);
        $authorName = $author['first_name'] . ' ' . $author['middle_name'] . ' ' . $author['last_name'];
        $html.='<td>' . $authorName . '<td>';
        $html.='<td><a href="book.delete.php?code=' . $book['code'] . '">remove</a><td>';
        $html.='</tr>';
    }
    return $html;
}

function getBookByCode($code)
{
    $fileFormat = getConfig()['file_format'];

    $book = [];
    switch($fileFormat){
        case 'txt':
            $book = readBookInPlainTextByCode($code);
            break;
        case 'csv':
            $book = readBookInCSVByCode($code);
            break;
        case 'json':
            $book = readBookInJSONByCode($code);
    }
    if (empty($book)){
        $book = [
            'title' => '',
            'author_code' => 0
        ];
    }
    return $book;
}

function updateBook($code, $title, $authorCode)
{
    $fileFormat = getConfig()['file_format'];

    $data = [
        'title' => $title,
        'author_code' => $authorCode
    ];

    $saved = false;
    switch($fileFormat){
        case 'txt':
            $saved = updateBookInPlainText($code, $data);
            break;
        case 'csv':
            $saved = updateBookInCSV($code, $data);
            break;
        case 'json':
            $saved = updateBookInJSON($code, $data);
    }
    return $saved;
}

function deleteBook($code)
{
    $fileFormat = getConfig()['file_format'];

    $deleted = false;
    switch($fileFormat){
        case 'txt':
            $deleted = deleteBookInPlainText($code);
            break;
        case 'csv':
            $deleted = deleteBookInCSV($code);
            break;
        case 'json':
            $deleted = deleteBookInJSON($code);
    }
    return $deleted;
}



function getPathForFile(string $entity)
{
    $fileFormat = getConfig()['file_format'];

    $path = '';
    switch($fileFormat){
        case 'txt':
            $path = getConfig()[$entity . '_plaintext_filepath'];
            break;
        case 'csv':
            $path = getConfig()[$entity . '_csv_filepath'];
            break;
        case 'json':
            $path = getConfig()[$entity . '_json_filepath'];
    }
    return $path;
}

function prepareFile(string $entity)
{
    $path = getPathForFile($entity);
    if (!file_exists($path)){
        $handle = fopen($path,'w');
        fclose($handle);
    }
}

