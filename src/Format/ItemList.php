<?php

namespace Templater\Format;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ItemList
{
    protected $items;

    protected $key;

    protected $char_limit;

    protected $end_chars = '&hellip;';

    protected $comma = ', ';

    protected $and = ' and ';

    protected $route;

    protected $route_params;

    public function __construct($items, $key)
    {
        $this->items = $items;
        $this->key   = $key;
    }

    public function links()
    {
        $params          = [];
        $list            = [];
        $extras          = [];
        $new_list_length = null;

        if (is_array(last($this->route_params))) {
            $extras = array_pop($this->route_params);
        }

        $append_plain  = $this->getPlainTextExtras($extras, 'append_plain', $this->items);
        $prepend_plain = $this->getPlainTextExtras($extras, 'prepend_plain', $this->items);
        $attr          = Arr::get($extras, 'attr', '');

        foreach ($this->route_params as $param) {
            $params[] = $this->collectKeys($this->items, $param)->toArray();
        }

        $items = $this->collectKeys($this->items, $this->key);

        if (array_key_exists('truncate', $extras)) {
            $this->setCharLimit($extras['truncate']);
            $new_list_length = $this->getTruncatedList($items);
            $this->setCharLimit(null);
        }

        foreach ($items as $key => $item) {
            $str = '';

            if ($str_prepend = $this->getPlainTextReplaced($extras, 'prepend_plain', $prepend_plain, $key)) {
                $str .= $str_prepend;
            }

            $str .= '<a href="' . route($this->route, Arr::pluck($params, $key)) . '" ' . $attr . '>';
            $str .= $item . '</a>';

            if ($str_append = $this->getPlainTextReplaced($extras, 'append_plain', $append_plain, $key)) {
                $str .= $str_append;
            }

            $list[] = $str;
        }

        return $this->listize($list, $new_list_length);
    }

    public function setCharLimit($char_limit)
    {
        $this->char_limit = $char_limit;

        return $this;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function setRouteParams($route_params)
    {
        $this->route_params = $route_params;

        return $this;
    }

    protected function strLen($str)
    {
        return mb_strlen(strip_tags($str), 'UTF-8');
    }

    protected function getPlainTextExtras($extras, $key, $collection)
    {
        if (!Arr::get($extras, $key)) {
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
        $str_append = Arr::get($extras, $extra_key);

        foreach ($plain_values as $var => $values) {
            if ($values[$key] === null) {
                // If we come accross a null value, this string is invalid, just kill it.
                return null;
            }

            $str_append = preg_replace('/{(.*)' . $var . '(.*)}/', $values[$key], $str_append);
        }

        return $str_append;
    }

    protected function normalizeCollection($collection)
    {
        if (! $collection instanceof Collection) {
            return new Collection($collection);
        }

        return $collection;
    }

    protected function collectKeys($collection, $key)
    {
        $collection = $this->normalizeCollection($collection);

        return $collection->map(function ($item) use ($key) {
            if (is_array($item)) {
                return $item[$key];
            }

            if (method_exists($item, 'present')) {
                return $item->present()->{$key};
            }

            return $item->{$key};
        });
    }

    protected function listize($list, $new_list_length = null)
    {
        $list = $this->normalizeCollection($list);

        if ($new_list_length !== null) {
            $final_list = $list->slice(0, $new_list_length);
        } else {
            $final_list = $list;
        }

        switch ($list->count()) {
            case 0:
                return '';
                break;

            case 1:
                return $final_list->first();
                break;

            case 2:
                return implode($this->and, $final_list->toArray());
                break;

            default:
                if ($final_list->count() > 2) {
                    $final_list[$final_list->count() - 1] = ltrim($this->and) . $final_list->last();
                }

                return implode($this->comma, $final_list->toArray());
                break;
        }
    }

    protected function getTruncatedList($list)
    {
        $new_list_length = null;
        $end_char_count  = $this->strLen(html_entity_decode($this->end_chars));
        $substr_count    = $this->char_limit - $end_char_count;
        $list_count      = $list->count();
        $total_chars     = 0;

        foreach ($list as $key => $item) {
            $separater_char_count = $this->getSeparaterCharCount($list_count, $key);
            $item_char_count      = $this->strLen($item);

            // If we're still under the limit just move on
            if ($item_char_count + $separater_char_count + $total_chars < $this->char_limit) {
                // Add onto the total chars used so far and keep going
                $total_chars += $item_char_count + $separater_char_count;

                continue;
            }

            $new_list_length = $key + 1;

            // If both together are longer, see if the string by itself is as well
            if ($item_char_count + $total_chars >= $this->char_limit) {
                if ($total_chars === 0) {
                    // If we're in the first item and hit the limit, it's just the char limit
                    $item_substr_count = $this->char_limit - $end_char_count;
                } else {
                    $item_substr_count = $this->char_limit - $total_chars - $end_char_count;
                }

                $list[$key] = rtrim(substr($item, 0, $item_substr_count)) . $this->end_chars;

                break;
            }

            if ($key + 1 !== $list_count) {
                $list[$key] = $item . $this->end_chars;
            }

            // Regardless, stop looping. We've hit the limit.
            break;
        }

        return $new_list_length;
    }

    protected function getSeparaterCharCount($list_count, $key)
    {
        if ($list_count === 1) {
            return 0;
        }

        if ($list_count === 2) {
            if ($key === 0) {
                return $this->strLen($this->and);
            }

            return 0;
        }

        if ($key === $list_count - 2) {
            return $this->strLen($this->comma . ltrim($this->and));
        }

        if ($key === $list_count - 1) {
            return 0;
        }

        return $this->strLen($this->comma);
    }

    public function __toString()
    {
        $list = $this->collectKeys($this->items, $this->key);

        if ($this->char_limit === null) {
            return $this->listize($list);
        }

        $test_list = $this->listize(clone $list);

        if ($this->strLen($test_list) <= $this->char_limit) {
            return $test_list;
        }

        $new_list_length = $this->getTruncatedList($list);

        return $this->listize($list, $new_list_length);
    }
}
