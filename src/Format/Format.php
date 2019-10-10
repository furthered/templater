<?php

namespace Templater\Format;

use Illuminate\Support\Str;

class Format
{
    /**
     * Phone
     *
     * Standardize the phone format, taking into account the extension
     *
     * @param string $phone Ugly phone number
     * @return string
     */
    public function phone($phone)
    {
        $parts  = explode('x', preg_replace('/[^\dx]/', '', strtolower($phone)));
        $number = $parts[0];
        $len    = strlen($number);

        if ($len == 11 && Str::startsWith($number, 1)) {
            $len--;
            $number = substr($number, 1, $len);
        }

        if ($len == 10) {
            $phone = '(' . substr($number, 0, 3) . ') ';
            $phone .= substr($number, 3, 3) . '-';
            $phone .= substr($number, 6, 4);
        }

        if (count($parts) > 1) {
            $phone .= ' ext. ' . $parts[1];
        }

        return $phone;
    }

    public function toList($collection, $key, $char_limit = null)
    {
        $list = new ItemList($collection, $key);

        if ($char_limit) {
            $list->setCharLimit($char_limit);
        }

        return (string) $list;
    }

    public function toListLinks($collection, $key, $route, ...$route_params)
    {
        $list = new ItemList($collection, $key);
        $list->setRoute($route);
        $list->setRouteParams($route_params);

        return (string) $list->links();
    }
}
