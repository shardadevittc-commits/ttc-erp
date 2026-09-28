<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------php artisan db:seed --class=CitySeeder
        | Indian Cities
        |--------------------------------------------------------------------------
        | state_code must match the code in states table.
        |--------------------------------------------------------------------------
        */

        $cities = [

            /*
            |--------------------------------------------------------------------------
            | Andhra Pradesh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'AP', 'name' => 'Visakhapatnam'],
            ['state_code' => 'AP', 'name' => 'Vijayawada'],
            ['state_code' => 'AP', 'name' => 'Guntur'],
            ['state_code' => 'AP', 'name' => 'Nellore'],
            ['state_code' => 'AP', 'name' => 'Kurnool'],
            ['state_code' => 'AP', 'name' => 'Rajahmundry'],
            ['state_code' => 'AP', 'name' => 'Tirupati'],
            ['state_code' => 'AP', 'name' => 'Kadapa'],
            ['state_code' => 'AP', 'name' => 'Anantapur'],

            /*
            |--------------------------------------------------------------------------
            | Arunachal Pradesh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'AR', 'name' => 'Itanagar'],
            ['state_code' => 'AR', 'name' => 'Naharlagun'],
            ['state_code' => 'AR', 'name' => 'Tawang'],
            ['state_code' => 'AR', 'name' => 'Pasighat'],

            /*
            |--------------------------------------------------------------------------
            | Assam
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'AS', 'name' => 'Guwahati'],
            ['state_code' => 'AS', 'name' => 'Dibrugarh'],
            ['state_code' => 'AS', 'name' => 'Silchar'],
            ['state_code' => 'AS', 'name' => 'Jorhat'],
            ['state_code' => 'AS', 'name' => 'Nagaon'],
            ['state_code' => 'AS', 'name' => 'Tinsukia'],

            /*
            |--------------------------------------------------------------------------
            | Bihar
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'BR', 'name' => 'Patna'],
            ['state_code' => 'BR', 'name' => 'Gaya'],
            ['state_code' => 'BR', 'name' => 'Bhagalpur'],
            ['state_code' => 'BR', 'name' => 'Muzaffarpur'],
            ['state_code' => 'BR', 'name' => 'Purnia'],
            ['state_code' => 'BR', 'name' => 'Darbhanga'],
            ['state_code' => 'BR', 'name' => 'Begusarai'],

            /*
            |--------------------------------------------------------------------------
            | Chhattisgarh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'CG', 'name' => 'Raipur'],
            ['state_code' => 'CG', 'name' => 'Bhilai'],
            ['state_code' => 'CG', 'name' => 'Bilaspur'],
            ['state_code' => 'CG', 'name' => 'Korba'],
            ['state_code' => 'CG', 'name' => 'Durg'],
            ['state_code' => 'CG', 'name' => 'Rajnandgaon'],

            /*
            |--------------------------------------------------------------------------
            | Goa
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'GA', 'name' => 'Panaji'],
            ['state_code' => 'GA', 'name' => 'Vasco da Gama'],
            ['state_code' => 'GA', 'name' => 'Margao'],
            ['state_code' => 'GA', 'name' => 'Mapusa'],

            /*
            |--------------------------------------------------------------------------
            | Gujarat
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'GJ', 'name' => 'Ahmedabad'],
            ['state_code' => 'GJ', 'name' => 'Surat'],
            ['state_code' => 'GJ', 'name' => 'Vadodara'],
            ['state_code' => 'GJ', 'name' => 'Rajkot'],
            ['state_code' => 'GJ', 'name' => 'Bhavnagar'],
            ['state_code' => 'GJ', 'name' => 'Jamnagar'],
            ['state_code' => 'GJ', 'name' => 'Gandhinagar'],
            ['state_code' => 'GJ', 'name' => 'Junagadh'],
            ['state_code' => 'GJ', 'name' => 'Anand'],
            ['state_code' => 'GJ', 'name' => 'Bharuch'],

            /*
            |--------------------------------------------------------------------------
            | Haryana
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'HR', 'name' => 'Gurugram'],
            ['state_code' => 'HR', 'name' => 'Faridabad'],
            ['state_code' => 'HR', 'name' => 'Panipat'],
            ['state_code' => 'HR', 'name' => 'Ambala'],
            ['state_code' => 'HR', 'name' => 'Hisar'],
            ['state_code' => 'HR', 'name' => 'Karnal'],
            ['state_code' => 'HR', 'name' => 'Rohtak'],
            ['state_code' => 'HR', 'name' => 'Sonipat'],
            ['state_code' => 'HR', 'name' => 'Yamunanagar'],

            /*
            |--------------------------------------------------------------------------
            | Himachal Pradesh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'HP', 'name' => 'Shimla'],
            ['state_code' => 'HP', 'name' => 'Dharamshala'],
            ['state_code' => 'HP', 'name' => 'Solan'],
            ['state_code' => 'HP', 'name' => 'Mandi'],
            ['state_code' => 'HP', 'name' => 'Kullu'],
            ['state_code' => 'HP', 'name' => 'Manali'],

            /*
            |--------------------------------------------------------------------------
            | Jharkhand
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'JH', 'name' => 'Ranchi'],
            ['state_code' => 'JH', 'name' => 'Jamshedpur'],
            ['state_code' => 'JH', 'name' => 'Dhanbad'],
            ['state_code' => 'JH', 'name' => 'Bokaro'],
            ['state_code' => 'JH', 'name' => 'Deoghar'],
            ['state_code' => 'JH', 'name' => 'Hazaribagh'],

            /*
            |--------------------------------------------------------------------------
            | Karnataka
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'KA', 'name' => 'Bengaluru'],
            ['state_code' => 'KA', 'name' => 'Mysuru'],
            ['state_code' => 'KA', 'name' => 'Mangaluru'],
            ['state_code' => 'KA', 'name' => 'Hubballi'],
            ['state_code' => 'KA', 'name' => 'Dharwad'],
            ['state_code' => 'KA', 'name' => 'Belagavi'],
            ['state_code' => 'KA', 'name' => 'Kalaburagi'],
            ['state_code' => 'KA', 'name' => 'Shivamogga'],
            ['state_code' => 'KA', 'name' => 'Tumakuru'],

            /*
            |--------------------------------------------------------------------------
            | Kerala
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'KL', 'name' => 'Thiruvananthapuram'],
            ['state_code' => 'KL', 'name' => 'Kochi'],
            ['state_code' => 'KL', 'name' => 'Kozhikode'],
            ['state_code' => 'KL', 'name' => 'Thrissur'],
            ['state_code' => 'KL', 'name' => 'Kollam'],
            ['state_code' => 'KL', 'name' => 'Kannur'],
            ['state_code' => 'KL', 'name' => 'Alappuzha'],

            /*
            |--------------------------------------------------------------------------
            | Madhya Pradesh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'MP', 'name' => 'Bhopal'],
            ['state_code' => 'MP', 'name' => 'Indore'],
            ['state_code' => 'MP', 'name' => 'Jabalpur'],
            ['state_code' => 'MP', 'name' => 'Gwalior'],
            ['state_code' => 'MP', 'name' => 'Ujjain'],
            ['state_code' => 'MP', 'name' => 'Sagar'],
            ['state_code' => 'MP', 'name' => 'Dewas'],
            ['state_code' => 'MP', 'name' => 'Satna'],

            /*
            |--------------------------------------------------------------------------
            | Maharashtra
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'MH', 'name' => 'Mumbai'],
            ['state_code' => 'MH', 'name' => 'Pune'],
            ['state_code' => 'MH', 'name' => 'Nagpur'],
            ['state_code' => 'MH', 'name' => 'Nashik'],
            ['state_code' => 'MH', 'name' => 'Aurangabad'],
            ['state_code' => 'MH', 'name' => 'Thane'],
            ['state_code' => 'MH', 'name' => 'Navi Mumbai'],
            ['state_code' => 'MH', 'name' => 'Kolhapur'],
            ['state_code' => 'MH', 'name' => 'Solapur'],
            ['state_code' => 'MH', 'name' => 'Amravati'],
            ['state_code' => 'MH', 'name' => 'Sangli'],
            ['state_code' => 'MH', 'name' => 'Nanded'],

            /*
            |--------------------------------------------------------------------------
            | Manipur
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'MN', 'name' => 'Imphal'],
            ['state_code' => 'MN', 'name' => 'Thoubal'],
            ['state_code' => 'MN', 'name' => 'Bishnupur'],

            /*
            |--------------------------------------------------------------------------
            | Meghalaya
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'ML', 'name' => 'Shillong'],
            ['state_code' => 'ML', 'name' => 'Tura'],
            ['state_code' => 'ML', 'name' => 'Jowai'],

            /*
            |--------------------------------------------------------------------------
            | Mizoram
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'MZ', 'name' => 'Aizawl'],
            ['state_code' => 'MZ', 'name' => 'Lunglei'],
            ['state_code' => 'MZ', 'name' => 'Champhai'],

            /*
            |--------------------------------------------------------------------------
            | Nagaland
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'NL', 'name' => 'Kohima'],
            ['state_code' => 'NL', 'name' => 'Dimapur'],
            ['state_code' => 'NL', 'name' => 'Mokokchung'],

            /*
            |--------------------------------------------------------------------------
            | Odisha
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'OD', 'name' => 'Bhubaneswar'],
            ['state_code' => 'OD', 'name' => 'Cuttack'],
            ['state_code' => 'OD', 'name' => 'Rourkela'],
            ['state_code' => 'OD', 'name' => 'Berhampur'],
            ['state_code' => 'OD', 'name' => 'Sambalpur'],
            ['state_code' => 'OD', 'name' => 'Puri'],

            /*
            |--------------------------------------------------------------------------
            | Punjab
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'PB', 'name' => 'Ludhiana'],
            ['state_code' => 'PB', 'name' => 'Amritsar'],
            ['state_code' => 'PB', 'name' => 'Jalandhar'],
            ['state_code' => 'PB', 'name' => 'Patiala'],
            ['state_code' => 'PB', 'name' => 'Bathinda'],
            ['state_code' => 'PB', 'name' => 'Mohali'],
            ['state_code' => 'PB', 'name' => 'Hoshiarpur'],
            ['state_code' => 'PB', 'name' => 'Pathankot'],
            ['state_code' => 'PB', 'name' => 'Moga'],
            ['state_code' => 'PB', 'name' => 'Batala'],
            ['state_code' => 'PB', 'name' => 'Khanna'],

            /*
            |--------------------------------------------------------------------------
            | Rajasthan
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'RJ', 'name' => 'Jaipur'],
            ['state_code' => 'RJ', 'name' => 'Jodhpur'],
            ['state_code' => 'RJ', 'name' => 'Udaipur'],
            ['state_code' => 'RJ', 'name' => 'Kota'],
            ['state_code' => 'RJ', 'name' => 'Ajmer'],
            ['state_code' => 'RJ', 'name' => 'Bikaner'],
            ['state_code' => 'RJ', 'name' => 'Alwar'],
            ['state_code' => 'RJ', 'name' => 'Bharatpur'],

            /*
            |--------------------------------------------------------------------------
            | Sikkim
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'SK', 'name' => 'Gangtok'],
            ['state_code' => 'SK', 'name' => 'Namchi'],
            ['state_code' => 'SK', 'name' => 'Gyalshing'],

            /*
            |--------------------------------------------------------------------------
            | Tamil Nadu
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'TN', 'name' => 'Chennai'],
            ['state_code' => 'TN', 'name' => 'Coimbatore'],
            ['state_code' => 'TN', 'name' => 'Madurai'],
            ['state_code' => 'TN', 'name' => 'Tiruchirappalli'],
            ['state_code' => 'TN', 'name' => 'Salem'],
            ['state_code' => 'TN', 'name' => 'Tiruppur'],
            ['state_code' => 'TN', 'name' => 'Erode'],
            ['state_code' => 'TN', 'name' => 'Vellore'],
            ['state_code' => 'TN', 'name' => 'Thoothukudi'],
            ['state_code' => 'TN', 'name' => 'Thanjavur'],

            /*
            |--------------------------------------------------------------------------
            | Telangana
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'TS', 'name' => 'Hyderabad'],
            ['state_code' => 'TS', 'name' => 'Warangal'],
            ['state_code' => 'TS', 'name' => 'Nizamabad'],
            ['state_code' => 'TS', 'name' => 'Karimnagar'],
            ['state_code' => 'TS', 'name' => 'Khammam'],
            ['state_code' => 'TS', 'name' => 'Ramagundam'],

            /*
            |--------------------------------------------------------------------------
            | Tripura
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'TR', 'name' => 'Agartala'],
            ['state_code' => 'TR', 'name' => 'Dharmanagar'],
            ['state_code' => 'TR', 'name' => 'Udaipur'],

            /*
            |--------------------------------------------------------------------------
            | Uttar Pradesh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'UP', 'name' => 'Lucknow'],
            ['state_code' => 'UP', 'name' => 'Kanpur'],
            ['state_code' => 'UP', 'name' => 'Ghaziabad'],
            ['state_code' => 'UP', 'name' => 'Agra'],
            ['state_code' => 'UP', 'name' => 'Varanasi'],
            ['state_code' => 'UP', 'name' => 'Prayagraj'],
            ['state_code' => 'UP', 'name' => 'Meerut'],
            ['state_code' => 'UP', 'name' => 'Noida'],
            ['state_code' => 'UP', 'name' => 'Bareilly'],
            ['state_code' => 'UP', 'name' => 'Aligarh'],
            ['state_code' => 'UP', 'name' => 'Moradabad'],
            ['state_code' => 'UP', 'name' => 'Gorakhpur'],
            ['state_code' => 'UP', 'name' => 'Mathura'],
            ['state_code' => 'UP', 'name' => 'Saharanpur'],

            /*
            |--------------------------------------------------------------------------
            | Uttarakhand
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'UK', 'name' => 'Dehradun'],
            ['state_code' => 'UK', 'name' => 'Haridwar'],
            ['state_code' => 'UK', 'name' => 'Haldwani'],
            ['state_code' => 'UK', 'name' => 'Rishikesh'],
            ['state_code' => 'UK', 'name' => 'Nainital'],
            ['state_code' => 'UK', 'name' => 'Roorkee'],

            /*
            |--------------------------------------------------------------------------
            | West Bengal
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'WB', 'name' => 'Kolkata'],
            ['state_code' => 'WB', 'name' => 'Howrah'],
            ['state_code' => 'WB', 'name' => 'Durgapur'],
            ['state_code' => 'WB', 'name' => 'Asansol'],
            ['state_code' => 'WB', 'name' => 'Siliguri'],
            ['state_code' => 'WB', 'name' => 'Bardhaman'],

            /*
            |--------------------------------------------------------------------------
            | Andaman and Nicobar Islands
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'AN', 'name' => 'Port Blair'],

            /*
            |--------------------------------------------------------------------------
            | Chandigarh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'CH', 'name' => 'Chandigarh'],

            /*
            |--------------------------------------------------------------------------
            | Dadra and Nagar Haveli and Daman and Diu
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'DH', 'name' => 'Daman'],
            ['state_code' => 'DH', 'name' => 'Diu'],
            ['state_code' => 'DH', 'name' => 'Silvassa'],

            /*
            |--------------------------------------------------------------------------
            | Delhi
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'DL', 'name' => 'New Delhi'],
            ['state_code' => 'DL', 'name' => 'Delhi'],

            /*
            |--------------------------------------------------------------------------
            | Jammu and Kashmir
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'JK', 'name' => 'Srinagar'],
            ['state_code' => 'JK', 'name' => 'Jammu'],
            ['state_code' => 'JK', 'name' => 'Anantnag'],
            ['state_code' => 'JK', 'name' => 'Baramulla'],

            /*
            |--------------------------------------------------------------------------
            | Ladakh
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'LA', 'name' => 'Leh'],
            ['state_code' => 'LA', 'name' => 'Kargil'],

            /*
            |--------------------------------------------------------------------------
            | Lakshadweep
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'LD', 'name' => 'Kavaratti'],

            /*
            |--------------------------------------------------------------------------
            | Puducherry
            |--------------------------------------------------------------------------
            */
            ['state_code' => 'PY', 'name' => 'Puducherry'],
            ['state_code' => 'PY', 'name' => 'Karaikal'],
            ['state_code' => 'PY', 'name' => 'Mahe'],
            ['state_code' => 'PY', 'name' => 'Yanam'],
        ];

        $inserted = 0;

        foreach ($cities as $city) {

            $state = DB::table('states')
                ->where('code', $city['state_code'])
                ->first();

            if (!$state) {
                $this->command->warn(
                    "State not found: {$city['state_code']} - {$city['name']}"
                );

                continue;
            }

            DB::table('cities')->updateOrInsert(
                [
                    'state_id' => $state->id,
                    'name' => $city['name'],
                ],
                [
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $inserted++;
        }

        $this->command->info(
            "{$inserted} cities seeded successfully."
        );
    }
}
