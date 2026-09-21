<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Sale',
                'short_name' => 'sale',
                'description' => 'Sales management, customer orders and inquiries',
                'status' => 1,
            ],
            [
                'name' => 'Purchase',
                'short_name' => 'purchase',
                'description' => 'Purchase orders, vendor management and raw material procurement',
                'status' => 1,
            ],
            [
                'name' => 'Gate',
                'short_name' => 'gate',
                'description' => 'Gate entry, vehicle inward/outward passes and driver verification',
                'status' => 1,
            ],
            [
                'name' => 'Weight',
                'short_name' => 'weight',
                'description' => 'Weighbridge management, gross and tare weight measurement',
                'status' => 1,
            ],
            [
                'name' => 'Unloader',
                'short_name' => 'unloader',
                'description' => 'Material unloading, scrap yard inspection and deduction entries',
                'status' => 1,
            ],
            [
                'name' => 'Dispatch',
                'short_name' => 'dispatch',
                'description' => 'Finished goods dispatch, delivery challans and vehicle loading',
                'status' => 1,
            ],
            [
                'name' => 'Lab',
                'short_name' => 'lab',
                'description' => 'Quality control, chemical/tensile lab testing and test certificates',
                'status' => 1,
            ],
            [
                'name' => 'Production',
                'short_name' => 'production',
                'description' => 'Plant manufacturing, mill operations and ERW pipe production',
                'status' => 1,
            ],
            [
                'name' => 'Lab Production',
                'short_name' => 'lab_production',
                'description' => 'Laboratory quality analysis and production operations',
                'status' => 1,
            ],
            [
                'name' => 'Account',
                'short_name' => 'account',
                'description' => 'Accounts, invoices, GST e-way billing and payments',
                'status' => 1,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['short_name' => $role['short_name']],
                $role
            );
        }
    }
}
