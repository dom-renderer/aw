<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomePageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'banner_carousel',
                'ordering' => 1,
                'value' => [
                    'visible' => true,
                    'is_editable' => true,
                    'slides' => [
                        [
                            'image' => '',
                            'heading' => '',
                            'description' => '',
                            'redirect' => '',
                            'has_button' => false,
                            'button_title' => ''
                        ],
                        [
                            'image' => '',
                            'heading' => '',
                            'description' => '',
                            'redirect' => '',
                            'has_button' => false,
                            'button_title' => ''
                        ],
                        [
                            'image' => '',
                            'heading' => '',
                            'description' => '',
                            'redirect' => '',
                            'has_button' => false,
                            'button_title' => ''
                        ]
                    ],
                ]
            ],
            [
                'key' => 'top_categories_grid',
                'ordering' => 2,
                'value' => [
                    'visible' => true,
                    'is_editable' => true,
                    'categories' => [
                        [
                            'title' => 'Food & Beverages',
                            'link' => '',
                            'items' => [1, 2, 3, 4] //product_id
                        ],
                        [
                            'title' => 'Home & Kitchen',
                            'link' => '',
                            'items' => [11, 14, 111, 45]
                        ],
                        [
                            'title' => 'Beauty & Personal Care',
                            'link' => '',
                            'items' => [21, 18]
                        ]
                    ]
                ]
            ],
            [
                'key' => 'top_categories_linear',
                'ordering' => 3,
                'value' => [
                    'visible' => true,
                    'is_editable' => true,
                    'categories' => [
                        [
                            'title' => 'Food & Beverages',
                            'link' => '',
                            'items' => [1, 2, 3, 4] //product_id
                        ],
                        [
                            'title' => 'Home & Kitchen',
                            'link' => '',
                            'items' => [11, 14, 111, 45]
                        ],
                        [
                            'title' => 'Beauty & Personal Care',
                            'link' => '',
                            'items' => [21, 18]
                        ]
                    ]
                ]
            ],
            [
                'key' => 'top_selling_products',
                'ordering' => 4,
                'value' => [
                    'visible' => true,
                    'is_editable' => true,
                    'products' => [1, 2, 3, 4] //product_id
                ]
            ],
            [
                'key' => 'recently_viewed',
                'ordering' => 5,
                'value' => [
                    'visible' => true,
                    'is_editable' => true,
                ]
            ],
            [
                'key' => 'newsletter_subscription',
                'ordering' => 6,
                'value' => [
                    'visible' => true,
                    'is_editable' => true,
                ]
            ]
        ];

        foreach ($settings as $setting) {
            \App\Models\HomePageSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
