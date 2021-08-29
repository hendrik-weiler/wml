<?php

require_once 'src/Parser.php';

use \wml\Parser as Parser;

$text = file_get_contents('tests/readme.wml');

print '<pre>';

print $text;
print '<br><br><br>';

$obj = Parser::parse($text);

var_dump($obj);