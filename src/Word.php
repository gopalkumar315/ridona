<?php
namespace Ridona;

class Word extends File
{
    public function __construct($input)
    {
        parent::__construct($input);
        array_walk($this->filenames, function (&$filename) {
            $filename = $this->readZippedXML($filename);
        });
        $this->filenames = array_filter($this->filenames, 'empty');
    }
    public function __destruct()
    {
        array_walk($this->filenames, function ($filename) {
            unlink($filename);
        });
    }
    private function readZippedXML($archiveFile, $dataFile = "word/document.xml")
    {
// Create new ZIP archive
        $zip = new ZipArchive;
// Open received archive file
        if (true === $zip->open($archiveFile)) {
            // If done, search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                // If found, read it to the string
                $data = $zip->getFromIndex($index);
                // Close archive file
                $zip->close();
                // Load XML from a string
                // Skip errors and warnings
                $xml = new DOMDocument();
                $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                // Return data without XML formatting tags
                $data       = strip_tags($xml->saveXML());
                $is_created = file_put_contents($filename = sys_get_temp_dir() . '/' . md5($input . time()) . '.tmp');
                if (is_created === false) {
                    $filename = '';
                }

                return $filename;
            }

            $zip->close();
        }

// In case of failure return empty string
        return "";
    }
}
