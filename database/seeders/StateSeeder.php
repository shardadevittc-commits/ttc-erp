<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        // Get India country ID
        $india = DB::table('countries')
            ->where('iso_code', 'IN')
            ->first();

        if (!$india) {
            $this->command->error(
                'India not found in countries table. Please run CountrySeeder first.'
            );

            return;
        }

        $states = [
            // States
            [
                'name' => 'Andhra Pradesh',
                'code' => 'AP',
            ],
            [
                'name' => 'Arunachal Pradesh',
                'code' => 'AR',
            ],
            [
                'name' => 'Assam',
                'code' => 'AS',
            ],
            [
                'name' => 'Bihar',
                'code' => 'BR',
            ],
            [
                'name' => 'Chhattisgarh',
                'code' => 'CG',
            ],
            [
                'name' => 'Goa',
                'code' => 'GA',
            ],
            [
                'name' => 'Gujarat',
                'code' => 'GJ',
            ],
            [
                'name' => 'Haryana',
                'code' => 'HR',
            ],
            [
                'name' => 'Himachal Pradesh',
                'code' => 'HP',
            ],
            [
                'name' => 'Jharkhand',
                'code' => 'JH',
            ],
            [
                'name' => 'Karnataka',
                'code' => 'KA',
            ],
            [
                'name' => 'Kerala',
                'code' => 'KL',
            ],
            [
                'name' => 'Madhya Pradesh',
                'code' => 'MP',
            ],
            [
                'name' => 'Maharashtra',
                'code' => 'MH',
            ],
            [
                'name' => 'Manipur',
                'code' => 'MN',
            ],
            [
                'name' => 'Meghalaya',
                'code' => 'ML',
            ],
            [
                'name' => 'Mizoram',
                'code' => 'MZ',
            ],
            [
                'name' => 'Nagaland',
                'code' => 'NL',
            ],
            [
                'name' => 'Odisha',
                'code' => 'OD',
            ],
            [
                'name' => 'Punjab',
                'code' => 'PB',
            ],
            [
                'name' => 'Rajasthan',
                'code' => 'RJ',
            ],
            [
                'name' => 'Sikkim',
                'code' => 'SK',
            ],
            [
                'name' => 'Tamil Nadu',
                'code' => 'TN',
            ],
            [
                'name' => 'Telangana',
                'code' => 'TS',
            ],
            [
                'name' => 'Tripura',
                'code' => 'TR',
            ],
            [
                'name' => 'Uttar Pradesh',
                'code' => 'UP',
            ],
            [
                'name' => 'Uttarakhand',
                'code' => 'UK',
            ],
            [
                'name' => 'West Bengal',
                'code' => 'WB',
            ],

            // Union Territories
            [
                'name' => 'Andaman and Nicobar Islands',
                'code' => 'AN',
            ],
            [
                'name' => 'Chandigarh',
                'code' => 'CH',
            ],
            [
                'name' => 'Dadra and Nagar Haveli and Daman and Diu',
                'code' => 'DH',
            ],
            [
                'name' => 'Delhi',
                'code' => 'DL',
            ],
            [
                'name' => 'Jammu and Kashmir',
                'code' => 'JK',
            ],
            [
                'name' => 'Ladakh',
                'code' => 'LA',
            ],
            [
                'name' => 'Lakshadweep',
                'code' => 'LD',
            ],
            [
                'name' => 'Puducherry',
                'code' => 'PY',
            ],
        ];

        foreach ($states as $state) {
            DB::table('states')->updateOrInsert(
                [
                    'country_id' => $india->id,
                    'code' => $state['code'],
                ],
                [
                    'name' => $state['name'],
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info(
            count($states) . ' Indian states/union territories seeded successfully.'
        );
    }
}
