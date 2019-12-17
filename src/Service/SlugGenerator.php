<?php


namespace App\Service;


class SlugGenerator
{
    public function clean(String $input) {
        $output = $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        $output = strtoupper($output);
        $output = preg_replace("/[^A-Z]/", "", $output);
        $stringParts = str_split($output);
        sort($stringParts);
        $output = implode('', $stringParts);
        return $output;
    }
}