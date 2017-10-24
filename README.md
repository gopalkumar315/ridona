# ridona
## Read big files and database tables with millions of rows without memory concern.
Processing big files like CSV,TXT and...always been a problem in php and there are two major problem.
* Memory limitaion.
* Using poor techniques for reading.<br>


**Now with `ridona` you can read files in gigabytes, database tables with millions of rows, yeah baby!** 
## ridona advantages
* Read big text files.
* Read Database tables with millions of records.
* Tuned with php generators and other techniques to use lowest memory as possible.
* Search through all rows of all tables in database.
---
Install ridona via composer:
```
composer require hosseinmousavi/ridona:dev-master
```
Then include composer autoload file:
```php
require 'vendor/autoload.php';
```
Or just include the autoloader
```php
require 'src/autoload.php';
```
---
### Reading Files
**Only text file like txt,csv... are supported now and other formats like doc,docx,html,pdf will be support in future versions.**


For reading files there are three modes
* by_chunk() <br>
  With this method you can read big files part to part, you can pass `chunk_size`, the default value is 10,000 bytes, it means in evey iteration you get 10,000 bytes of the file
```php
$file = new ridona\File('./tests/files/lorem_ipsum_1.txt');
foreach ($file->by_chunk() as $chunk) {
  ....do
}
```
**you should use `by_chunk()` only in `foreach,for or while loops`.<br>**

For example read files of a directory by chunk.
```php
$files = new ridona\File('./tests/files');
foreach ($file->by_chunk() as $chunk) {
  ....do
}
```
take this, you have a directory of files and want to extract emails from those files, for this case we can use `Extract::do` to extract that. the `Extract::do()` accepts an array or a string for input and an regex pattern then extract data from input array or string with `preg_match_all()`.<br>
 ```php
$emails = [];
$files   = new ridona\File('./tests/files');
foreach ($file->by_chunk() as $chunk) {
  $matches = ridona\Extract::do($chunk, ridona\RegexPattern::$email);
  $emails  = array_merge($emails, $matches);
}
var_dump($emails);
```
**Please have in mind when you want to extract string from chunks using regex, in every iteration you get specific size of the file and becasue of that maybe some part of the string you want to find be incomplete and regex could'nt find it so use large  `chunk_size` or use  `entire_mode()`**<br>
You can use any regex, just pass it as second parameter to `Extract::do()`.<br>
You can also define more regexes in `Pattern.php` file.<br>
* by_entire()<br>
  If you use this method you load the entire file into memory at once (using `file_get_contents()`) and maybe encounter memory exhaustion problem. this method don't accept any arguments.
 ```php
$file    = new ridona\File('./tests/files/lorem_ipsum_1.txt');
$content = $file->by_entire();
```
You can pass a directory or a file path to `File()`.
 ```php
$file    = new ridona\File('./tests/files');
$content = $file->by_entire();
```
* by_line()<br>
  In this method we read files line by line using php `file()`, so the file will load into an array at once and maybe encounter memory exhaustion problem.
```php
$file = new ridona\File('./tests/files');
foreach ($file->by_line() as $line) {
  ....do
}
```
**`by_line()` do not accept any arguments and read each line in evey iteration.**

---
### Reading database tables
Like files we can read tables in two mode.
* by_chunk()<br>
  We use database limit, offset commands to read table part by part, the default chunk size is 10000 so
  the first executed command is like `select * from table_name limit 10000 offset 0` and second one is
  `select * from table_name limit 10000 offset 10000` and so on.
```php
$database = new ridona\Database('mysql:dbname=database_name;host=127.0.0.1', 'db_user','db_pass');
foreach ($database->query('select * from table_name')->by_chunk() as $row) {
  ....do
}
```
You can pass limit value as `chunk_size` to `by_chunk()` and be carefull that you can't use limit or offset commands in chunk mode because we use it already.<br>
In `query()` we use pdo `query()` so it's possible to write any query with prepared statements and pass values array as second parameter.
**Values should be in type of array**.
```php
$database = new ridona\Database('mysql:dbname=database_name;host=127.0.0.1', 'db_user','db_pass');
$emails=[];
foreach ($database->query('select * from table_name where id > ?',['503'])->by_chunk() as $row) {
  ....do
}
```
* by_entire()<br>
  In this method we use `select * from table_name` to fetch table then iterate over pdo `fetch(PDO::FETCH_NUM)` and return one row per iteration also there might be memory exhaustion problem. this method don't accept any arguments.
```php
$database = new ridona\Database('mysql:dbname=database_name;host=127.0.0.1', 'db_user','db_pass');
foreach ($database->query('select * from table_name')->by_entire() as $row) {
  ....do
}
```
---
### Read all tables in database
There is a very cool feature here and it is reading all tables with `table()`, see below.
```php
$database = new ridona\Database('mysql:dbname=database_name;host=127.0.0.1', 'db_user','db_pass');
foreach ($database->tables()->by_entire() as $row) {
  ....do
}
```
Also you can specify the tables you want.
```php
$database = new ridona\Database('mysql:dbname=database_name;host=127.0.0.1', 'db_user','db_pass');
foreach ($database->tables(['table_name1','table_name2'])->by_entire() as $row) {
  do...
}
```
**We can read all tables in database using `by_entire()` or `by_chunk()`.**<br>

We can extract for example emails from database.
```php
$database = new ridona\Database('mysql:dbname=database_name;host=127.0.0.1', 'db_user','db_pass');
$emails=[];
foreach ($database->tables(['table_name1','table_name2'])->by_chunk(15000) as $row) {
  $matches = ridona\Extract::do($row, ridona\RegexPattern::$email);
  $emails  = array_merge($emails, $matches);
}
```
Cool ha!<br>
See tests folder for more examples.

---
### Considerations
1. Only MySQL database tested with this library and i need your cooperation to see other databases compatibility.<br>
2. if your file or database is very big in both cases use `by_chunk()` and if you encounter memory problem decrease chunk_size.<br>
3. Because this library use php generators in `by_chunk()`,`by_line()` when parsing files and `by_entire(),by_chunk()` in tables you only can use returned content in `foreach`,`while` and `for` loops and not in `array_walk,array_reduce,array_map` and `array_filter`.<br>