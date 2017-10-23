<?php
use PHPUnit\Framework\TestCase;

ini_set('memory_limit', '-1');

class FileTest extends TestCase
{

    public function testExtractingEmailsFromSingleFile()
    {
        $emails = [];
        $file   = new ridona\File('./tests/files/lorem_ipsum_1.txt');
        foreach ($file->by_chunk() as $chunk) {
            $matches = ridona\Extract::do($chunk, ridona\RegexPattern::$email);
            $emails  = array_merge($emails, $matches);
        }
        var_dump($emails);

    }
    public function testReadingDirectoryEntireMode()
    {
        $files = new ridona\File('./tests/files');

        $contents = $files->by_entire();
        $this->assertInternalType('string', $contents);
    }

    public function testReadingSingleFileChunkMode()
    {
        $file = new ridona\File('./tests/files/lorem_ipsum_1.txt');
        foreach ($file->by_chunk() as $chunk) {

        }
    }

    public function testReadSingleFileEntireMode()
    {
        $file    = new ridona\File('./tests/files/lorem_ipsum_1.txt');
        $content = $file->by_entire();
        $this->assertInternalType('string', $content);
        $this->assertNotEmpty($content);

    }
    public function testFileTypeIsValide()
    {
        $file     = new ridona\File('./tests/files/lorem_ipsum_1.txt');
        $is_valid = $file->is_filetype_valid('./tests/files/lorem_ipsum_1.txt');
        $this->assertTrue($is_valid);
    }
    public function testGetFileType()
    {
        $file      = new ridona\File('./tests/files/lorem_ipsum_1.txt');
        $file_type = $file->get_filetype('./tests/files/lorem_ipsum_1.txt');
        $this->assertEquals('text/plain', $file_type);
    }
    public function testFileConstructorBuildFileNamesFromDir()
    {
        $file = new ridona\File('./tests/files');
        $this->assertNotEmpty($file->filenames);
    }
    public function testFileConstructorBuildFileNames()
    {
        $file = new ridona\File('./tests/files/lorem_ipsum_1.txt');
        $this->assertNotEmpty($file->filenames);
    }

}
