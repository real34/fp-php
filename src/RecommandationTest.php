<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class RecommandationTest extends TestCase
{
    public function testShouldReturnNoRecommandationWhenNoHistory()
    {
        $actual = Recommandation::getForProduct('Fairphone', []);
        $this->assertEquals([], $actual);
    }

    public function testShouldReturnProductsOfOtherOrdersContainingTheInitial()
    {
        $actual = Recommandation::getForProduct('Fairphone', [
            [
                'id' => 'order-1',
                'products' => [
                    [
                        'name' => 'Fairphone',
                        'qty' => 2
                    ],
                    [
                        'name' => 'iPhone X',
                        'qty' => 1
                    ],
                ]
            ],
            [
                'id' => 'order-2',
                'products' => [
                    [
                        'name' => 'iPhone X',
                        'qty' => 1
                    ],
                    [
                        'name' => 'Galaxy S6',
                        'qty' => 3
                    ],
                ]
            ]
        ]);

        $expected = ['iPhone X'];

        $this->assertEquals($expected, $actual);
    }

    public function testShouldReturnMostOrderedProductsFirst()
    {
        $actual = Recommandation::getForProduct('Fairphone', [
            [
                'id' => 'order-1',
                'products' => [
                    [
                        'name' => 'Fairphone',
                        'qty' => 2
                    ],
                    [
                        'name' => 'iPhone X',
                        'qty' => 1
                    ],
                    [
                        'name' => '3310',
                        'qty' => 1
                    ],
                ]
            ],
            [
                'id' => 'order-2',
                'products' => [
                    [
                        'name' => 'Fairphone',
                        'qty' => 1
                    ],
                    [
                        'name' => 'iPhone X',
                        'qty' => 1
                    ],
                    [
                        'name' => 'Galaxy S6',
                        'qty' => 3
                    ],
                ]
            ]
        ]);

        $expected = ['Galaxy S6', 'iPhone X', '3310'];

        $this->assertEquals($expected, $actual);
    }
}
