<?php

namespace Templater\Format;

use Illuminate\Support\Collection;

class Format {

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

        if ($len == 11 && starts_with($number, 1)) {
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

    protected function normalizeCollection($collection)
    {
        if (!method_exists($collection, 'toArray')) {
            return new Collection($collection);
        }

        return $collection;
    }

    protected function collectKeys($collection, $key)
    {
        $collection = $this->normalizeCollection($collection);

        return $collection->map(function($item) use ($key) {
            if (method_exists($item, 'present')) {
                return $item->present()->{$key};
            }

            return $item->{$key};
        });
    }

    public function toList($collection, $key)
    {
        return $this->listize($this->collectKeys($collection, $key));
    }

    public function toListLinks($collection, $key, $route, ...$route_params)
    {
        $params       = [];
        $list         = [];
        $extras       = [];

        if (is_array(last($route_params))) {
            $extras = array_pop($route_params);
        }

        $append_plain  = $this->getPlainTextExtras($extras, 'append_plain', $collection);
        $prepend_plain = $this->getPlainTextExtras($extras, 'prepend_plain', $collection);

        foreach ($route_params as $param) {
            $params[] = $this->collectKeys($collection, $param)->toArray();
        }

        foreach ($this->collectKeys($collection, $key) as $key => $item) {

            $str = '';

            if ($str_prepend = $this->getPlainTextReplaced($extras, 'prepend_plain', $prepend_plain, $key)) {
                $str .= $str_prepend;
            }

            $str .= '<a href="' . route($route, array_fetch($params, $key)) . '">';
            $str .= $item . '</a>';

            if ($str_append = $this->getPlainTextReplaced($extras, 'append_plain', $append_plain, $key)) {
                $str .= $str_append;
            }

            $list[] = $str;
        }

        return $this->listize($list);
    }

    protected function listize($list)
    {
        $list = $this->normalizeCollection($list);

        switch($list->count()) {
            case 0:
                return '';
            break;

            case 1:
                return $list->first();
            break;

            case 2:
                return implode(' and ', $list->toArray());
            break;

            default:
                $list[$list->count() - 1] = 'and ' . $list->last();
                return implode(', ', $list->toArray());
            break;
        }
    }

    protected function getPlainTextExtras($extras, $key, $collection)
    {
        if (!array_get($extras, $key)) {
            return [];
        }

        $plain_text_vars = [];

        preg_match_all('/{(.*)}/', $extras[$key], $matches);

        $vars = array_map('trim', $matches[1]);

        foreach ($vars as $var) {
            $plain_text_vars[$var] = $this->collectKeys($collection, $var);
        }

        return $plain_text_vars;
    }

    protected function getPlainTextReplaced($extras, $extra_key, $plain_values, $key)
    {
        $str_append = array_get($extras, $extra_key);

        foreach ($plain_values as $var => $values) {
            if ($values[$key] === null) {
                // If we come accross a null value, this string is invalid, just kill it.
                return null;
            }

            $str_append = preg_replace('/{(.*)' . $var . '(.*)}/', $values[$key], $str_append);
        }

        return $str_append;
    }

}
