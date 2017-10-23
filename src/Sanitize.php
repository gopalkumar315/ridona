<?php
namespace ridona;

class Sanitize
{
    public static function remove_non_printable_characters($input)
    {
        $input = preg_replace('/[^\PC\s]/u', '', $input);
        return $input;
    }
    public static function trim_email_bad_characters($input)
    {
        $input = trim($input, '/\\"\'');
        return $input;
    }
    public static function sanitize_dirname($input)
    {
        $input = rtrim($input, '/\\"\'');
        return $input;
    }
}
