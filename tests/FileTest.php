<?php

ini_set('memory_limit', '-1');

class FileTest extends PHPUnit_Framework_TestCase 
{

    public function testReadingDirectoryEntireMode()
    {
        $emails = [];
        $files  = new Ridona\File('./tests/files');

        $contents = $files->by_entire();
        $this->assertInternalType('string', $contents);
    }

    public function testReadingSingleFileChunkMode()
    {
        $emails = [];
        $file   = new Ridona\File('./tests/files/lorem_ipsum_1.txt');

        foreach ($file->by_chunk() as $chunk) {
            
        }
    }

    public function testReadSingleFileEntireMode()
    {
        $emails = [];
        $file   = new Ridona\File('./tests/files/lorem_ipsum_1.txt');

        $content = $file->by_entire();

        $this->assertInternalType('string', $content);
        $this->assertNotEmpty($content);

    }
    public function testFileTypeIsValide()
    {
        $file     = new Ridona\File('./tests/files/lorem_ipsum_1.txt');
        $is_valid = $file->is_filetype_valid('./tests/files/lorem_ipsum_1.txt');
        $this->assertTrue($is_valid);
    }
    public function testGetFileType()
    {
        $file      = new Ridona\File('./tests/files/lorem_ipsum_1.txt');
        $file_type = $file->get_filetype('./tests/files/lorem_ipsum_1.txt');
        $this->assertEquals('text/plain', $file_type);
    }
    public function testFileConstructorBuildFileNamesFromDir()
    {
        $file = new Ridona\File('./tests/files');
        $this->assertNotEmpty($file->filenames);
    }
    public function testFileConstructorBuildFileNames()
    {
        $file = new Ridona\File('./tests/files/lorem_ipsum_1.txt');
        $this->assertNotEmpty($file->filenames);
    }

}
