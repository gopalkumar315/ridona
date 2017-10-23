# ridona
## Read big files and MySQL tables with millions of rows without memory concern
Processing big files like CSV,TXT files and... always been a problem in php and there are two major problems:<br>
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
for reading file there are there modes: 
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
  with this method you can read big files part to part, you can pass chunk_size in method, the default value is 10,000 byte.in evey iteration you get 10,000 bytes of the file
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
* Reading file by line
  in this method we read files line by line using php file() method, so the file will load into an array at once and maybe encounter memory exhaustion problem
```php
$file = new ridona\File('./tests/files');
foreach ($file->by_line() as $line) {
  do....
}
```
by_line() method do not accept any arguments and read each line in evey iteration
  
### Reading Database Tables
like files we can read tables in two mode:
* Read entire table at once
  in this method we use 'select * from table_name' to fetch table then iterate over pdo fetch(PDO::FETCH_NUM) and return one row per iteration
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');
foreach ($database->query('select * from table_name')->by_entire() as $row) {
  do...
}
```
there might be memory exhaustion problem
there is a very cool feature here and it is reading all tables with table() method, see below:
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
foreach ($database->tables()->by_entire() as $row) {
  do...
}
```
when using this method you should pass databse name as 4th parameter to Database object
also you can the tables you want
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
foreach ($database->tables(['table_name1','table_name2'])->by_entire() as $row) {
  do...
}
```
we can extract emails from databse rows:
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
$emails=[];
foreach ($database->tables(['table_name1','table_name2'])->by_entire() as $row) {
  $matches = ridona\Extract::do($row, ridona\RegexPattern::$email);
  $emails  = array_merge($emails, $matches);
}
```
cool ha!

* Read table by chunk
  we use database limit, offset commands to read table part by part, the default chunk size is 1000 so
  the first executed command is like 'select * from table_name limit 10000 offset 0' and second one is
  'select * from table_name limit 10000 offset 10000' and so on
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
foreach ($database->query('select * from table_name')->by_chunk() as $row) {
  do...
}
```
you can pass limit value as chunk_size to by_chunk() method and be carefull that you can't use limit or offset commadns in chunk mode
because we use it already.
we can read tables like entire mode here in chunk mode
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
$emails=[];
foreach ($database->tables()->by_chunk() as $row) {
  do...
}
```
or
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
$emails=[];
foreach ($database->tables(['table_name1','table_name2'])->by_chunk() as $row) {
  do...
}
```



