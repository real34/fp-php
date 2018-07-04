<?php

class Recommandation
{

    public static function getForProduct($name)
    {
        return function ($history) use ($name) {
            $ordersWithProduct = array_filter($history, function ($order) use ($name) {
                return in_array($name, array_column($order['products'], 'name'));
            });

            $productsMachin = flatten(array_map(function ($order) use ($name) {
                // If Yes, let’s add the other products to $results
                return array_filter($order['products'], function ($product) use ($name) {
                    return $product['name'] !== $name;
                });
            }, $ordersWithProduct));

            $countersPerProduct = array_reduce($productsMachin, function ($acc, $product) {
                // Maintain another counter of occurences to sort by qty later
                if (!array_key_exists($product['name'], $acc)) {
                    $acc[$product['name']] = 0;
                }
                $acc[$product['name']] += $product['qty'];
                return $acc;
            }, []);

            arsort($countersPerProduct);

            return array_keys($countersPerProduct);
        };
    }
}

function flatten($arrays)
{
    return array_reduce($arrays, 'array_merge', []);
}