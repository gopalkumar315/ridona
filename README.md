# ridona
## Read big files and database tables with millions of rows without memory concern
Processing big file like CSV file,TXT file and...always been a problem in php nad there are two major problem.
* Memory limitaion
* Using Poor techniques for reading 
Now i think i created a good library to fix the problem so now on you can read file in gigabytes, database tables with millions of rows, yeah baby!
## ridona advantages
* Read big text files like csv,txt,...
* Read Database tables with million records
* Tuned with php generators and other techniques to use lowest memory as possible
* Search through all rows of all tables in database
---
first install ridona via composer:
```
composer require hosseinmousavi/ridona
```
then include composer autoload file:
```php
require 'vendor/autoload.php';
```
or simple include 'src/autoload.php';
```php
require 'src/autoload.php';
```
---
### Reading Files
_Only text file like txt,csv... are supported now and other formats like doc,docx,html,pdf will be support in future versions._
for reading files there are three modes: 
* Reading entire file at once (using file_get_contents) with by_entire() method.
  if you use this method you load the entire file into memory and maybe encounter memory exhaustion problem.
 ```php
$file    = new ridona\File('./tests/files/lorem_ipsum_1.txt');
$content = $file->by_entire();
```
you can pass a directory or a file path to File(), in File() constructor we pass direction to glob() to fetch files of the directory
 ```php
$file    = new ridona\File('./tests/files');
$content = $file->by_entire();
```
take this, you have a directory of files and want to extract emails from those files, for this case we can use Extract::do to extract that. the Extract::do() method accepts an array for input and an regex pattern
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
* Reading file part by part with by_chunk() method.
  with this method you can read big files part to part, you can pass chunk_size in method, the default value is 10,000 bytes, it means in evey iteration you get 10,000 bytes of the file
```php
$file = new ridona\File('./tests/files/lorem_ipsum_1.txt');
foreach ($file->by_chunk() as $chunk) {
  ....do
}
```
 unlike by_entire() method you should use by_chunk() in foreach,for or while loops
 this is another example to read files of a directory by chunk method:
```php
$file = new ridona\File('./tests/files');
foreach ($file->by_chunk() as $chunk) {
  ....do
}
```
* Reading file by line
  in this method we read files line by line using php file() method, so the file will load into an array at once and maybe encounter memory exhaustion problem.
```php
$file = new ridona\File('./tests/files');
foreach ($file->by_line() as $line) {
  ....do
}
```
_by_line() method do not accept any arguments and read each line in evey iteration._
--
### Reading Database Tables
like files we can read tables in two mode:
* Read entire table at once
  in this method we use 'select * from table_name' to fetch table then iterate over pdo fetch(PDO::FETCH_NUM) method and return one row per iteration also there might be memory exhaustion problem.
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');
foreach ($database->query('select * from table_name')->by_entire() as $row) {
  ....do
}
```
there is a very cool feature here and it is reading all tables with table() method, see below:
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
foreach ($database->tables()->by_entire() as $row) {
  ....do
}
```
also you can the tables you want
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
foreach ($database->tables(['table_name1','table_name2'])->by_entire() as $row) {
  do...
}
```
we can extract for example emails from databse:
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
  'select * from table_name limit 10000 offset 10000' and so on.
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
foreach ($database->query('select * from table_name')->by_chunk() as $row) {
  ....do
}
```
you can pass limit value as chunk_size to by_chunk() method and be carefull that you can't use limit or offset commands in chunk mode because we use it already.
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
$emails=[];
foreach ($database->tables()->by_chunk() as $row) {
  ....do
}
```
or
```php
$database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '', 'nrpmem97_01');
$emails=[];
foreach ($database->tables(['table_name1','table_name2'])->by_chunk() as $row) {
  ....do
}
```
---
### Considerations
1. Only MySQL database tested with this library, and i need your زooperation to see other databases compatibility.<br>
2. if your file or databse is very big in both cases use by_chunk() method nad if you encounter memory problem decrease chunk_size.<br>
3. Because this library use php generators in by_chunk(),by_line() methods when parsing files and by_entire(),by_chunk() in 4. tables you only can use returned content in foreach,while and for loops and not in array_walk,array_reduce,array_map and array_filter.<br>
