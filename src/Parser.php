<?php
/*
Copyright 2021 Hendrik Weiler
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
Version: 0.6.1
*/

namespace wml;

/**
 * This class provides the parsing of wml type text
 *
 * @class \wml\Parser
 * @author Hendrik Weiler
 */
class Parser
{
    /**
     * Returns the endIndex and the text of the given depth level and above
     *
     * @param $text string The reference of the text
     * @param $from int The index to start at
     * @param $depth int The depth level
     * @return array
     */
    protected static function getObjectText(&$text, $from, $depth) {
        $depthLevel = 0;
        $textPart = '';
        for($i=$from;$i < strlen($text); ++$i) {
            $key = $text[$i];
            if($key == "\n") {
                $depthLevel = 0;
            }
            if($key == "\t") {
                $depthLevel++;
            } else {
                if($key != "\n") {
                    if ($depthLevel < $depth) {
                        return array(
                            'endIndex' => $i-1,
                            'text' => $textPart
                        );
                    }
                }
            }
            $textPart .= $key;
        }
        return array(
            'endIndex' => $i-1,
            'text' => $textPart
        );
    }

    /**
     * Parses the text and fill the object
     *
     * @param $object array The reference to the object for input
     * @param $text string The text to parse
     * @param $depth int The depth level
     */
    protected static function parseObject(&$object, $text, $depth) {

        $currentObjKey = '';
        $currentObjValue = '';
        $currentObjClass = '';
        $currentArrayValue = '';
        $inValue = false;
        $inObject = false;
        $objClass = false;
        $inArray = false;
        $inComment = false;
        $inMultipleRowsMode = false;
        $inMultipleRowsModeArray = false;
        $inArrayDepth = 0;
        for($i=0;$i < strlen($text); ++$i) {

            $key = $text[$i];

            if($inComment) {
                if($key == "\n") {
                    $inComment = false;
                }
            } else if($inArray) {

                if($inMultipleRowsModeArray) {

                    if($key == '\\' && $text[$i+1] == '"') {
                        continue;
                    }
                    if($key == '"' && $text[$i-1] != '\\') {
                        $object[$currentObjKey][] = trim($currentObjValue);
                        $inMultipleRowsModeArray = false;
                        continue;
                    }
                    $currentObjValue .= $key;

                } else {
                    if($key == '{') {
                        $inArrayDepth++;
                    }
                    if($key == '"' && $inArrayDepth<=0) {
                        $inMultipleRowsModeArray = true;
                        $currentObjValue = '';
                        continue;
                    }
                    $isLastChar = $text[$i+1] == '}' && $inArrayDepth<=0;
                    if($key == ',' && $inArrayDepth<=0 || $isLastChar) {
                        if($isLastChar) $currentArrayValue .= $key;
                        $value = trim($currentArrayValue);
                        if(preg_match('#^{#',$value)) {
                            $index = count($object[$currentObjKey]);
                            $split = explode("\n",$value);
                            array_shift($split);
                            $parseableValue = implode("\n", $split);
                            $firstValue = array_shift($split);
                            $tabDepth = substr_count($firstValue, "\t");
                            $res = static::getObjectText($parseableValue, 0,$tabDepth);
                            static::parseObject($object[$currentObjKey][$index], $res['text'],$tabDepth);

                        } else {
                            if(empty($value)) continue;
                            $object[$currentObjKey][] = $value;
                        }
                        $currentArrayValue = '';
                    } else {
                        $currentArrayValue .= $key;
                    }

                    if($key == '}') {
                        if($inArrayDepth<=0) {
                            $currentObjValue = '';
                            $currentArrayValue = '';
                            $inArray = false;
                            $inObject = false;
                            $inValue = false;
                            $currentObjKey = '';
                            for($j=$i; $j < strlen($text);++$j) {
                                if($text[$j] == "\n") {
                                    break;
                                }
                                ++$i;
                            }
                        }
                        $inArrayDepth--;
                    }
                }

            } else if($inObject) {
                $res = static::getObjectText($text, $i,$depth+1);
                static::parseObject($object[$currentObjKey], $res['text'],$depth+1);
                $inObject = false;
                $objClass = false;
                $inValue = false;
                $inComment = false;
                $i = $res['endIndex'];
                $currentObjKey = '';
                $currentObjValue = '';
            } else if($inMultipleRowsMode) {
                if($key == '\\' && $text[$i+1] == '"') {
                    continue;
                }
                if($key == '"' && $text[$i-1] != '\\') {
                    $object[$currentObjKey] = trim($currentObjValue);
                    $currentObjKey = '';
                    $currentObjValue = '';
                    $inValue = false;
                    $inMultipleRowsMode = false;
                    continue;
                }
                $currentObjValue .= $key;
            } else if($objClass) {
                if($key != ' ' && $key != "\n") {
                    $currentObjClass .= $key;
                }
                if($key == "\n") {
                    $inObject = true;
                    $objClass = false;
                    $object[$currentObjKey] = array(
                        '__class__' => $currentObjClass
                    );
                    $currentObjClass = '';
                }
            } else if(!$inValue) {
                if($key == ':') {
                    $objClass = true;
                }
                if($key == ' ') {
                    $inValue = true;
                    continue;
                }
                if($key == '#') {
                    $inComment = true;
                    continue;
                }
                if($key == "\n") {
                    if($currentObjKey == '') continue;
                    $inObject = true;
                    $object[$currentObjKey] = array(
                        '__class__' => ''
                    );
                } else {
                    if($key != "\t") {
                        $currentObjKey .= $key;
                    }
                }
            } else {
                if($key == '{') {
                    $object[$currentObjKey] = array();
                    $inArray = true;
                    $inArrayDepth = 0;
                    continue;
                }
                if($key == '"') {
                    $inMultipleRowsMode = true;
                    continue;
                }
                if($key == ':') {
                    $objClass = true;
                }
                if($key == "\n" || $i == strlen($text)-1) {
                    if($currentObjKey == '') continue;
                    $object[$currentObjKey] = $currentObjValue;
                    $currentObjKey = '';
                    $currentObjValue = '';
                    $inValue = false;
                    continue;
                }
                $currentObjValue .= $key;
            }

        }
    }

    /**
     * Parses the text
     *
     * @param $text string The text to parse
     * @return array
     */
    public static function parse($text) {
        $object = array();
        static::parseObject($object,$text,0);
        return $object;
    }
}