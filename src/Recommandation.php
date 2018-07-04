<?php

class Recommandation
{

    public static function getForProduct($name, $history)
    {
        $containsProductNamed = function ($name) {
            return function ($order) use ($name) {
                // Is the product part of this order?
                foreach ($order['products'] as $product) {
                    if ($product['name'] === $name) {
                        return true;
                    }
                }
                return false;
            };
        };

        $ordersContainingProduct = array_filter($history, $containsProductNamed($name));

        $otherOrderedProducts = array_filter(
            flatten(array_map(get('products'), $ordersContainingProduct)),
            isDifferentFrom($name, get('name'))
        );


        $sumQty = function ($products) {
            return array_sum(array_map(get('qty'), $products));
        };

        $countersPerProduct = array_map(
            $sumQty,
            array_reduce($otherOrderedProducts, groupBy('name'), [])
        );
        $results = array_keys($countersPerProduct);

        // Sort results with most purchased first
        $qtyPurchased = function ($name) use ($countersPerProduct) {
            return $countersPerProduct[$name];
        };

        $byQtyPurchasedDesc = udesc($qtyPurchased);


        usort($results, $byQtyPurchasedDesc);

        return $results;
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