<?php
namespace Ridona;

class Extract
{
    public static function do($input, $pattern) {
        $all_matches = [];
        if (!is_array($input)) {
            $input = array($input);
        }

        array_walk($input, function ($value) use (&$all_matches, $pattern) {
            $result = preg_match_all($pattern, $value, $matches);
            if ($result) {
                $all_matches = array_merge($all_matches, $matches[0]);
            }});

        return $all_matches;
    }
}
