# ridona
## Read big files and database tables with millions of rows without memory concern
Processing big file like CSV file,TXT file and.. always been a problem in php nad there are two major problem<br>
* Memory limitaion
* Using Poor techniques for reading 
Now i think i created a good library to fix the problem so now on you can read file in gigabytes, database tables with millions of rows, yeah baby!
---
first install ridona via composer:
```
composer require hosseinmousavi/ridona
```
or simple include 'src/autoload.php';
```php
require 'src/autoload.php';
```
### Reading Files (text based)
for reading file there are two modes: 
* Reading entire file at once (using file_get_contents) with by_entire()
  if you use this method you load the entire file into memory and maybe encounter memory exhaustion problem
  
 ```php
$file    = new ridona\File('./tests/files/lorem_ipsum_1.txt');
$content = $file->by_entire();
```
you can pass a directory or a file path to File(), in File() constructor we pass direction to glob() to fetch files of the directory
 ```php
$file    = new ridona\File('./tests/files');
$content = $file->by_entire();
```
take that you have a directory of files and want to extract emails from those files, for this case we can use Extract::do to extract that
 ```php
$emails = [];
$files   = new ridona\File('./tests/files');
foreach ($file->by_chunk() as $chunk) {
  $matches = ridona\Extract::do($chunk, ridona\RegexPattern::$email);
  $emails  = array_merge($emails, $matches);
}
var_dump($emails);
```
you can use any regex, just pass it as second parameter to Extract::do()

* Reading file part by part with by_chunk()
  with this method you can read big files part to part, you can pass chunk_size in method, the default value is 10,000 byte
```php
$file = new ridona\File('./tests/files/lorem_ipsum_1.txt');
foreach ($file->by_chunk() as $chunk) {
  do....
}
```
 whereas by_entire() you should use by_chunk() in foreach,for or while loops
 this is another example to read file of a directory by chunk method:
```php
$file = new ridona\File('./tests/files');
foreach ($file->by_chunk() as $chunk) {
  do....
}
```
### Reading Database Tables



