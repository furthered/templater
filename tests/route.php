<?php

namespace Templater\Format;

function route($route, $params)
{
    $params = implode('/', $params);

    return '/' . implode('/', [$route, $params]);
}
