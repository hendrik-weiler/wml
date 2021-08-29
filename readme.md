# wml

This library provides a markup language for reading and writing in php.

### Files

- ```src/Parser.php``` - The class to parse the text
- ```src/Writer.php``` - The class to write data to text

### Examples

Examples can be found in the ```tests``` folder. 
In the ```read.php``` or ```write.php``` file is the usage example in php.

###### Basic usage:

```
# Simple key, value
Key Value
# Object
Object
    MyObjectKey MyValue
# Lists
List {
    1,2,3,4
}
# List with objects
List2 {
    {
        MyListObjKey MyValue
        MyListObj
            MyObjKey MyValue
    }
}
# You can stack objects and lists
Object1
    Object2
        Key Value
    List { 1,2,3,4 }
# Its possible to mark objects with a classname for
# later processing of the data
User : User
    Username Max
    LastLogin 2021-02-15
```

The output in php:

```
array(2) {
  ["children"]=>
  array(6) {
    ["Key"]=>
    string(5) "Value"
    ["Object"]=>
    array(2) {
      ["children"]=>
      array(1) {
        ["MyObjectKey"]=>
        string(7) "MyValue"
      }
      ["class"]=>
      string(0) ""
    }
    ["List"]=>
    array(4) {
      [0]=>
      string(1) "1"
      [1]=>
      string(1) "2"
      [2]=>
      string(1) "3"
      [3]=>
      string(1) "4"
    }
    ["List2"]=>
    array(1) {
      [0]=>
      array(1) {
        ["children"]=>
        array(2) {
          ["MyListObjKey"]=>
          string(7) "MyValue"
          ["MyListObj"]=>
          array(2) {
            ["children"]=>
            array(1) {
              ["MyObjKey"]=>
              string(7) "MyValue"
            }
            ["class"]=>
            string(0) ""
          }
        }
      }
    }
    ["Object1"]=>
    array(2) {
      ["children"]=>
      array(2) {
        ["Object2"]=>
        array(2) {
          ["children"]=>
          array(1) {
            ["Key"]=>
            string(5) "Value"
          }
          ["class"]=>
          string(0) ""
        }
        ["List"]=>
        array(4) {
          [0]=>
          string(1) "1"
          [1]=>
          string(1) "2"
          [2]=>
          string(1) "3"
          [3]=>
          string(1) "4"
        }
      }
      ["class"]=>
      string(0) ""
    }
    ["User"]=>
    array(2) {
      ["children"]=>
      array(2) {
        ["Username"]=>
        string(3) "Max"
        ["LastLogin"]=>
        string(9) "2021-02-1"
      }
      ["class"]=>
      string(4) "User"
    }
  }
  ["class"]=>
  string(0) ""
}
```