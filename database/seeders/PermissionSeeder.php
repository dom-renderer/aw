<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resourcePermissionsScaffolding = [
            [
                'name' => ' Listing',
                'slug' => '.index'
            ],
            [
                'name' => ' Add',
                'slug' => '.create'
            ],
            [
                'name' => ' Save',
                'slug' => '.store'
            ],
            [
                'name' => ' Edit',
                'slug' => '.edit'
            ],
            [
                'name' => ' Update',
                'slug' => '.update'
            ],
            [
                'name' => 'View',
                'slug' => '.show'
            ],
            [
                'name' => ' Delete',
                'slug' => '.destroy'
            ]
        ];

        $resourcePermissions = [
            [
                'name' => 'Users',
                'slug' => 'users'
            ],
            [
                'name' => 'Roles',
                'slug' => 'roles'
            ],
            [
                'name' => 'Customers',
                'slug' => 'customers'
            ],
            [
                'name' => 'Suppliers',
                'slug' => 'suppliers'
            ],
            [
                'name' => 'Customer Location',
                'slug' => 'customer-locations'
            ],
            [
                'name' => 'Warehouses',
                'slug' => 'warehouses'
            ],
            [
                'name' => 'Locations',
                'slug' => 'locations'
            ],
            [
                'name' => 'Categories',
                'slug' => 'categories'
            ],
            [
                'name' => 'Products',
                'slug' => 'products'
            ],
            [
                'name' => 'Brands',
                'slug' => 'brands'
            ]
        ];

        $extraPermissions = [
            [
                'name' => 'Home Page Settings Listing',
                'slug' => 'home-page-settings.index'
            ],
            [
                'name' => 'Home Page Settings Update',
                'slug' => 'home-page-settings.update'
            ],
            [
                'name' => 'View Stock History',
                'slug' => 'products.get-variant-stock-history'
            ],
            [
                'name' => 'Adjust Stock',
                'slug' => 'products.adjust-stock'
            ]
        ];

        $permissions = [];

        foreach ($resourcePermissions as $rP) {
            foreach ($resourcePermissionsScaffolding as $scaffold) {
                $permissions[] = [
                    'name' => $rP['name'] . $scaffold['name'],
                    'slug' => $rP['slug'] . $scaffold['slug']
                ];
            }
        }

        $permissions = array_merge($permissions, $extraPermissions);

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}
