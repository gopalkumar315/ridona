<?php
namespace Ridona;

abstract class Parse
{
    abstract public function by_entire();
    abstract public function by_chunk($chunk_size);
    public function extract($pattern)
    {
        return Extract::start($pattern);
    }
    public function get_filetype($filename)
    {
        return mime_content_type($filename);
    }
}
