<?php
namespace ridona;

class File extends Parse
{

    public $filenames;
    public $valid_filetypes = [
        'text/plain', 'text/html',
    ];

    public function __construct($input)
    {
        $filenames = [];

        if (is_file($input)) {
            $filenames = array($input);
        } elseif (is_dir($input)) {
            $filenames = $this->get_dir_files($input);
        }
        $this->filenames=$filenames;
        // $this->filenames = array_filter($filenames, function ($filename) {
        //     return $this->is_filetype_valid($filename);
        // });

        if (empty($this->filenames)) {
            throw new NoFileSelectedException('The file or directory not exists');
        }
    }

    public function get_dir_files($dirname)
    {
        if (is_dir($dirname)) {
            foreach (glob(Sanitize::sanitize_dirname($dirname) . '/*') as $filename) {
                $filenames[] = $filename;
            }
        }
        return $filenames;
    }
    public function by_entire()
    {
        foreach ($this->filenames as $filename) {
            $file = file_get_contents($filename);
            return $file;
        }
    }

    public function by_chunk($chunk_size = 10000)
    {
        foreach ($this->filenames as $filename) {
            $file = fopen($filename, 'rb');
            while (!feof($file)) {
                $line = fgets($file, $chunk_size);
                yield $line;
            }
            fclose($file);
        }
    }
    public function by_line()
    {
        foreach ($this->filenames as $filename) {
            $lines = file($filename);
            foreach ($lines as $line) {
                yield $line;
            }
        }

    }
    public function is_filetype_valid($filename)
    {
        if (in_array($this->get_filetype($filename), $this->valid_filetypes)) {
            return true;
        }
        return false;

    }

}
