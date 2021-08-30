# wml

This library provides a markup language for reading and writing data in php.

### Files

- ```src/Parser.php``` - The class to parse the text
- ```src/Writer.php``` - The class to write data to text

### Examples

Examples can be found in the ```tests``` folder. 
In the ```read.php``` or ```write.php``` file is the usage example in php.

###### Basic usage:

The format is using indents for depth levels like python does.
```
Object1
    Key Value
    Object2
        Key2 Value2
        Object3
            Key3 Value3
            List {
                1,2,3,4
            }
```

###### Examples:
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
array(7) {
  ["Key"]=>
  string(5) "Value"
  ["Object"]=>
  array(2) {
    ["__class__"]=>
    string(0) ""
    ["MyObjectKey"]=>
    string(7) "MyValue"
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
    array(2) {
      ["MyListObjKey"]=>
      string(7) "MyValue"
      ["MyListObj"]=>
      array(2) {
        ["__class__"]=>
        string(0) ""
        ["MyObjKey"]=>
        string(7) "MyValue"
      }
    }
  }
  ["Object1"]=>
  array(3) {
    ["__class__"]=>
    string(0) ""
    ["Object2"]=>
    array(2) {
      ["__class__"]=>
      string(0) ""
      ["Key"]=>
      string(5) "Value"
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
  ["User"]=>
  array(3) {
    ["__class__"]=>
    string(4) "User"
    ["Username"]=>
    string(3) "Max"
    ["LastLogin"]=>
    string(10) "2021-02-15"
  }
  ["Object5"]=>
  array(3) {
    ["__class__"]=>
    string(0) ""
    ["Key"]=>
    string(5) "Value"
    ["Object2"]=>
    array(3) {
      ["__class__"]=>
      string(0) ""
      ["Key2"]=>
      string(6) "Value2"
      ["Object3"]=>
      array(3) {
        ["__class__"]=>
        string(0) ""
        ["Key3"]=>
        string(6) "Value3"
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
    }
  }
}
```