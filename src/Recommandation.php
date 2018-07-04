<?php

class Recommandation
{

    public static function getForProduct($name, $history)
    {
        $countersPerProduct = array_map(
            function ($products) {
                return array_sum(array_map(get('qty'), $products));
            },
            array_reduce(array_filter(
                flatten(array_map(get('products'), array_filter($history, function ($order) use ($name) {
                    return in_array($name, array_map(
                        get('name'),
                        get('products')($order)
                    ));
                }))),
                isDifferentFrom($name, get('name'))
            ), groupBy('name'), [])
        );

        arsort($countersPerProduct);
        return array_keys($countersPerProduct);
    }

}

function isDifferentFrom($from, $getValue)
{
    return function ($item) use ($from, $getValue) {
        return $getValue($item) !== $from;
    };
}

function flatten($array): array
{
    return array_reduce($array, 'array_merge', []);
}

function groupBy($key): Closure
{
    return function ($acc, $array) use ($key) {
        $acc[get($key)($array)][] = $array;
        return $acc;
    };
}

function get($key): Closure
{
    return function ($array) use ($key) {
        return $array[$key];
    };
}

function udesc($getValue): Closure
{
    return function ($a, $b) use ($getValue) {
        return desc($getValue($b), $getValue($a));
    };
}

function desc($comparedB, $comparedA): int
{
    return $comparedB <=> $comparedA;
}

function id($x)
{
    return $x;
}

function compose(...$functions)
{
    return array_reduce(
        $functions,
        function ($carry, $item) {
            return function ($x) use ($carry, $item) {
                return $item($carry($x));
            };
        },
        'id'
    );
}