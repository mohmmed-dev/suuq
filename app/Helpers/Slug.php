<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Slug
{

    public static function uniqueSlug($slug,$table)
    {
        $slug= self::createSlug($slug);

        $items= DB::table($table)->select('slug')->whereRaw("slug like '$slug%'")->count();

        $count= $items;

        return $slug.($count>0?'-'.($count+1):'');
    }

    public static function createSlug($str)
    {
        $string = trim($str);
        $string = mb_strtolower($string, 'UTF-8');

        $string = preg_replace("/[\s\-_]+/", ' ', $string) ;
        $string = preg_replace("/[\s\_]/", '-', $string);

        $string = rawurldecode($string);
        $string = self::cleanArabicSlug($string);

        return $string;
    }

    protected static function cleanArabicSlug($text)
{
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $text);
    $cleaned = preg_replace('/\s+/', ' ', $cleaned);
    $cleaned = Str::of($cleaned)->replace(['|', '/', '\\', '،', ':', '؛', '?', '!', '.', ',', '(', ')', '[', ']', '&', '+', '='], '-')->value();
    $cleaned = str_replace(' ', '-', $cleaned);
    $cleaned = preg_replace('/-+/', '-', $cleaned);
    $cleaned = trim($cleaned, '-');
    return strtolower($cleaned);
}


}
