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
 * This class will write wml text from parsed data
 *
 * @class \wml\Writer
 * @author Hendrik Weiler
 */
class Writer
{

	/**
	 * Check if the array has non-numeric keys
	 *
	 * @param array $array The array to check
	 * @return bool
	 */
	protected static function hasStringKeys(array $array) {
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}

    /**
     * Writes a wml object from an array object
     *
     * @param $string string The reference to the wml string
     * @param $object array The input object
     * @param $depth int The depth level
     */
    protected static function writeObject(&$string, $object, $depth) {
        foreach($object as $key => $value) {
            for($i=0; $i < $depth ; ++$i) {
                $string .= "\t";
            }
            if(is_array($value) && static::hasStringKeys($value)) {
                $string .= $key;
                if(!empty($value['__class__'])) {
                    $string .= ' : ' . $value['__class__'];
                }
                $string .= "\n";

                static::writeObject($string, $value, $depth+1);
            } else if(is_array($value) && !static::hasStringKeys($value)) {
                $string .= $key . ' {';
                $string .= "\n";

                for($j=0;$j < $depth +1 ; $j++) $string .= "\t";

                $array = array();

                foreach($value as $arrVal) {
                    if(is_array($arrVal)) {
                        $tempVal = '';
                        $tempVal .= "{\n";
                        static::writeObject($tempVal, $arrVal, $depth+2);
                        for($j=0;$j < $depth +1 ; $j++) $tempVal .= "\t";
                        $tempVal .= "}\n";
                        $array[] = $tempVal;
                    } else {
                        $array[] = '"' . preg_replace('#"#','\"',$arrVal) . '"';
                    }
                }

                $inbetweenString = '';
                for($j=0;$j < $depth +1; $j++) $inbetweenString .= "\t";
                $inbetweenString .= ", \n";
                for($j=0;$j < $depth +1; $j++) $inbetweenString .= "\t";
                $string .= implode($inbetweenString, $array);

                $string .= "\n";
                for($j=0;$j < $depth ; $j++) $string .= "\t";

                $string .= '}' . "\n";
            } else {
				if($key == '__class__') continue;
                $string .= $key . ' "' . preg_replace('#"#','\"',$value) . "\"\n";
            }
        }

    }

    /**
     * Writes a wml text from an input object
     *
     * @param $object array The input object
     * @return string
     */
    public static function write($object)
    {
        $result = '';

        static::writeObject($result, $object, 0);

        return $result;
    }
}