<?php

class Recommandation
{

    public static function getForProduct($name, $history)
    {
        $results = [];
        $countersPerProduct = [];

        foreach ($history as $order) {
            // Is the product part of this order?
            $found = false;
            foreach ($order['products'] as $product) {
                if ($product['name'] === $name) {
                    $found = true;
                }
            }

            if ($found) {
                // If Yes, let’s add the other products to $results
                foreach ($order['products'] as $product) {
                    // Prevent adding the product itself
                    if ($product['name'] !== $name) {
                        // Only add the recommended product once
                        if (!in_array($product['name'], $results)) {
                            $results[] = $product['name'];
                        }

                        // Maintain another counter of occurences to sort by qty later
                        if (!array_key_exists($product['name'], $countersPerProduct)) {
                            $countersPerProduct[$product['name']] = 0;
                        }
                        $countersPerProduct[$product['name']] += $product['qty'];
                    }
                }
            }
        }

        // Sort results with most purchased first
        usort($results, function ($a, $b) use ($countersPerProduct) {
            return $countersPerProduct[$b] <=> $countersPerProduct[$a];
        });

        return $results;
    }
}