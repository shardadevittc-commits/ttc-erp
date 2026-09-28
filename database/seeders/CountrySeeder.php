<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Afghanistan',
                'iso_code' => 'AF',
                'phone_code' => '+93',
                'status' => true,
            ],
            [
                'name' => 'Albania',
                'iso_code' => 'AL',
                'phone_code' => '+355',
                'status' => true,
            ],
            [
                'name' => 'Algeria',
                'iso_code' => 'DZ',
                'phone_code' => '+213',
                'status' => true,
            ],
            [
                'name' => 'Argentina',
                'iso_code' => 'AR',
                'phone_code' => '+54',
                'status' => true,
            ],
            [
                'name' => 'Australia',
                'iso_code' => 'AU',
                'phone_code' => '+61',
                'status' => true,
            ],
            [
                'name' => 'Austria',
                'iso_code' => 'AT',
                'phone_code' => '+43',
                'status' => true,
            ],
            [
                'name' => 'Bangladesh',
                'iso_code' => 'BD',
                'phone_code' => '+880',
                'status' => true,
            ],
            [
                'name' => 'Belgium',
                'iso_code' => 'BE',
                'phone_code' => '+32',
                'status' => true,
            ],
            [
                'name' => 'Brazil',
                'iso_code' => 'BR',
                'phone_code' => '+55',
                'status' => true,
            ],
            [
                'name' => 'Canada',
                'iso_code' => 'CA',
                'phone_code' => '+1',
                'status' => true,
            ],
            [
                'name' => 'China',
                'iso_code' => 'CN',
                'phone_code' => '+86',
                'status' => true,
            ],
            [
                'name' => 'Denmark',
                'iso_code' => 'DK',
                'phone_code' => '+45',
                'status' => true,
            ],
            [
                'name' => 'Egypt',
                'iso_code' => 'EG',
                'phone_code' => '+20',
                'status' => true,
            ],
            [
                'name' => 'Finland',
                'iso_code' => 'FI',
                'phone_code' => '+358',
                'status' => true,
            ],
            [
                'name' => 'France',
                'iso_code' => 'FR',
                'phone_code' => '+33',
                'status' => true,
            ],
            [
                'name' => 'Germany',
                'iso_code' => 'DE',
                'phone_code' => '+49',
                'status' => true,
            ],
            [
                'name' => 'Greece',
                'iso_code' => 'GR',
                'phone_code' => '+30',
                'status' => true,
            ],
            [
                'name' => 'Hong Kong',
                'iso_code' => 'HK',
                'phone_code' => '+852',
                'status' => true,
            ],
            [
                'name' => 'India',
                'iso_code' => 'IN',
                'phone_code' => '+91',
                'status' => true,
            ],
            [
                'name' => 'Indonesia',
                'iso_code' => 'ID',
                'phone_code' => '+62',
                'status' => true,
            ],
            [
                'name' => 'Ireland',
                'iso_code' => 'IE',
                'phone_code' => '+353',
                'status' => true,
            ],
            [
                'name' => 'Italy',
                'iso_code' => 'IT',
                'phone_code' => '+39',
                'status' => true,
            ],
            [
                'name' => 'Japan',
                'iso_code' => 'JP',
                'phone_code' => '+81',
                'status' => true,
            ],
            [
                'name' => 'Malaysia',
                'iso_code' => 'MY',
                'phone_code' => '+60',
                'status' => true,
            ],
            [
                'name' => 'Mexico',
                'iso_code' => 'MX',
                'phone_code' => '+52',
                'status' => true,
            ],
            [
                'name' => 'Nepal',
                'iso_code' => 'NP',
                'phone_code' => '+977',
                'status' => true,
            ],
            [
                'name' => 'Netherlands',
                'iso_code' => 'NL',
                'phone_code' => '+31',
                'status' => true,
            ],
            [
                'name' => 'New Zealand',
                'iso_code' => 'NZ',
                'phone_code' => '+64',
                'status' => true,
            ],
            [
                'name' => 'Nigeria',
                'iso_code' => 'NG',
                'phone_code' => '+234',
                'status' => true,
            ],
            [
                'name' => 'Norway',
                'iso_code' => 'NO',
                'phone_code' => '+47',
                'status' => true,
            ],
            [
                'name' => 'Pakistan',
                'iso_code' => 'PK',
                'phone_code' => '+92',
                'status' => true,
            ],
            [
                'name' => 'Philippines',
                'iso_code' => 'PH',
                'phone_code' => '+63',
                'status' => true,
            ],
            [
                'name' => 'Poland',
                'iso_code' => 'PL',
                'phone_code' => '+48',
                'status' => true,
            ],
            [
                'name' => 'Portugal',
                'iso_code' => 'PT',
                'phone_code' => '+351',
                'status' => true,
            ],
            [
                'name' => 'Qatar',
                'iso_code' => 'QA',
                'phone_code' => '+974',
                'status' => true,
            ],
            [
                'name' => 'Russia',
                'iso_code' => 'RU',
                'phone_code' => '+7',
                'status' => true,
            ],
            [
                'name' => 'Saudi Arabia',
                'iso_code' => 'SA',
                'phone_code' => '+966',
                'status' => true,
            ],
            [
                'name' => 'Singapore',
                'iso_code' => 'SG',
                'phone_code' => '+65',
                'status' => true,
            ],
            [
                'name' => 'South Africa',
                'iso_code' => 'ZA',
                'phone_code' => '+27',
                'status' => true,
            ],
            [
                'name' => 'South Korea',
                'iso_code' => 'KR',
                'phone_code' => '+82',
                'status' => true,
            ],
            [
                'name' => 'Spain',
                'iso_code' => 'ES',
                'phone_code' => '+34',
                'status' => true,
            ],
            [
                'name' => 'Sri Lanka',
                'iso_code' => 'LK',
                'phone_code' => '+94',
                'status' => true,
            ],
            [
                'name' => 'Sweden',
                'iso_code' => 'SE',
                'phone_code' => '+46',
                'status' => true,
            ],
            [
                'name' => 'Switzerland',
                'iso_code' => 'CH',
                'phone_code' => '+41',
                'status' => true,
            ],
            [
                'name' => 'Thailand',
                'iso_code' => 'TH',
                'phone_code' => '+66',
                'status' => true,
            ],
            [
                'name' => 'Turkey',
                'iso_code' => 'TR',
                'phone_code' => '+90',
                'status' => true,
            ],
            [
                'name' => 'United Arab Emirates',
                'iso_code' => 'AE',
                'phone_code' => '+971',
                'status' => true,
            ],
            [
                'name' => 'United Kingdom',
                'iso_code' => 'GB',
                'phone_code' => '+44',
                'status' => true,
            ],
            [
                'name' => 'United States',
                'iso_code' => 'US',
                'phone_code' => '+1',
                'status' => true,
            ],
            [
                'name' => 'Vietnam',
                'iso_code' => 'VN',
                'phone_code' => '+84',
                'status' => true,
            ],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                [
                    'iso_code' => $country['iso_code'],
                ],
                [
                    'name' => $country['name'],
                    'phone_code' => $country['phone_code'],
                    'status' => $country['status'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
