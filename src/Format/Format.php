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
        $params = [];
        $list   = [];

        foreach ($route_params as $param) {
            $params[] = $this->collectKeys($collection, $param)->toArray();
        }

        foreach ($this->collectKeys($collection, $key) as $key => $item) {
            $list[] = '<a href="' . route($route, array_fetch($params, $key)) . '">'
                            . $item . '</a>';
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

}
