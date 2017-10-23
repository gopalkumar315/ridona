<?php
use PHPUnit\Framework\TestCase;

ini_set('memory_limit', '-1');

class DatabaseTest extends TestCase
{

    public function testGetDatabaseName()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');
        $this->assertEquals('nrpmem97_01', $database->database_name);
    }
    public function testReadManyTablesChunkMode()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');

        foreach ($database->tables(['wp_users', 'wp_options'])->by_chunk() as $row) {
            print_r($row);
        }
    }

    public function testReadManyTablesEntireMode()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');

        foreach ($database->tables(['wp_users', 'wp_options'])->by_entire() as $row) {
            print_r($row);
        }
    }
    public function testReadDatabaseAllTablesChunkMode()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');
        foreach ($database->tables()->by_chunk(10000) as $row) {
            $this->assertInternalType('array', $row);
        }
    }
    public function testReadDatabaseAllTablesEntireMode()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');

        foreach ($database->tables()->by_entire() as $row) {
            $this->assertInternalType('array', $row);
        }

    }
    public function testReadTableEntireMode()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');
        foreach ($database->query('select * from wp_options')->by_entire() as $row) {
            $this->assertInternalType('array', $row);
        }

    }

    public function testReadTableChunkMode()
    {
        $database = new ridona\Database('mysql:dbname=nrpmem97_01;host=127.0.0.1', 'root', '');
        foreach ($database->query('select * from wp_options')->by_chunk(50000) as $row) {
            $this->assertInternalType('array', $row);
        }
    }

}
