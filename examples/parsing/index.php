<?php

require_once '../../src/Writer.php';
require_once '../../src/Parser.php';

use \wml\Parser as Parser;
use \wml\Writer as Writer;

$dataFromJSON = json_decode(file_get_contents('data.json'),true);

print '<pre>';

$wml = Writer::write($dataFromJSON);

file_put_contents('data.wml', $wml);

$dataFromWML = Parser::parse($wml);

var_dump($dataFromWML);