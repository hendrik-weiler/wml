<?php

require_once 'src/Writer.php';
require_once 'src/Parser.php';

use \wml\Parser as Parser;
use \wml\Writer as Writer;

$text = file_get_contents('tests/test4.wml');

$parsed = Parser::parse($text);

print '<pre>';

var_dump($parsed);

$res = Writer::write($parsed);

print($res);

$parsed2 = Parser::parse($res);

var_dump('reparsed',$parsed2);