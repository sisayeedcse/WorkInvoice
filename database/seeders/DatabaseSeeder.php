<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@anamil.ae'],
            [
                'name'              => 'Jahed Aziz',
                'email'             => 'admin@anamil.ae',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Default company settings
        $settings = [
            'company_name'    => 'Al Anamil Al Thahabiah Steel Workshop',
            'company_owner'   => 'Jahed Aziz',
            'company_address' => 'Industrial Area, Kalba, Sharjah',
            'company_phone'   => '0504243212',
            'company_email'   => 'alanamil43@gmail.com',
            'company_tagline' => 'Professional Steel Fabrication Services',
            'currency'        => 'AED',
            'currency_symbol' => 'AED',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'company');
        }

        // Service items
        $items = [
            ['name' => 'Door Lock Installation',      'description' => 'Supply and installation of door lock mechanism',   'default_price' => 250.00,   'unit' => 'Unit',  'category' => 'Door'],
            ['name' => 'Main Gate Fabrication',       'description' => 'Custom fabrication of main entrance steel gate',   'default_price' => 3500.00,  'unit' => 'Unit',  'category' => 'Gate'],
            ['name' => 'Main Gate Repair',            'description' => 'Repair and maintenance of existing gate',          'default_price' => 500.00,   'unit' => 'Job',   'category' => 'Gate'],
            ['name' => 'Steel Staircase Fabrication', 'description' => 'Custom steel staircase with railings',             'default_price' => 8000.00,  'unit' => 'Unit',  'category' => 'Staircase'],
            ['name' => 'Folding Ladder Fabrication',  'description' => 'Foldable steel ladder for attic/storage access',   'default_price' => 1200.00,  'unit' => 'Unit',  'category' => 'Ladder'],
            ['name' => 'Signboard Fabrication',       'description' => 'Steel frame and sign board fabrication',           'default_price' => 800.00,   'unit' => 'Unit',  'category' => 'Signage'],
            ['name' => 'Frame Fitting',               'description' => 'Steel frame installation and fitting',             'default_price' => 600.00,   'unit' => 'Unit',  'category' => 'General'],
            ['name' => 'Grille / Window Guard',       'description' => 'Steel window grille or security guard',            'default_price' => 350.00,   'unit' => 'Sqm',   'category' => 'Security'],
            ['name' => 'Steel Handrail',              'description' => 'Stainless/steel handrail installation per meter',  'default_price' => 200.00,   'unit' => 'Meter', 'category' => 'Railing'],
            ['name' => 'Car Parking Shade',           'description' => 'Steel structure car parking shade',                'default_price' => 5500.00,  'unit' => 'Unit',  'category' => 'Shade'],
            ['name' => 'Steel Door Fabrication',      'description' => 'Custom steel door with frame',                    'default_price' => 1800.00,  'unit' => 'Unit',  'category' => 'Door'],
            ['name' => 'Steel Fence',                 'description' => 'Steel fencing or boundary wall per meter',         'default_price' => 180.00,   'unit' => 'Meter', 'category' => 'Fence'],
            ['name' => 'Welding Repair Work',         'description' => 'General welding and fabrication repair per hour',  'default_price' => 150.00,   'unit' => 'Hour',  'category' => 'General'],
            ['name' => 'Steel Canopy / Awning',       'description' => 'Steel frame canopy or awning structure',           'default_price' => 4500.00,  'unit' => 'Unit',  'category' => 'Shade'],
            ['name' => 'Mezzanine Floor',             'description' => 'Steel mezzanine floor construction',               'default_price' => 12000.00, 'unit' => 'Unit',  'category' => 'Structure'],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['name' => $item['name']], $item);
        }

        // Sample customers
        $customers = [
            ['name' => 'Mohammed Al Rashidi', 'company_name' => 'Al Rashidi Trading LLC', 'phone' => '0501234567', 'email' => 'mohammed@rashidi.ae', 'address' => 'Sharjah Industrial Area'],
            ['name' => 'Ahmed Al Mansouri',   'company_name' => 'Mansouri Contracting',   'phone' => '0509876543', 'email' => 'ahmed@mansouri.ae',   'address' => 'Kalba, Sharjah'],
            ['name' => 'Ibrahim Hassan',      'company_name' => null,                      'phone' => '0556677889', 'email' => 'ibrahim@gmail.com',   'address' => 'Fujairah'],
            ['name' => 'Khalid Al Zaabi',     'company_name' => 'Al Zaabi Properties',    'phone' => '0528899001', 'email' => 'khalid@alzaabi.ae',   'address' => 'Dubai, UAE'],
            ['name' => 'Sultan Al Nuaimi',    'company_name' => 'Gulf Construction Co.',  'phone' => '0504455667', 'email' => 'sultan@gulfcon.ae',   'address' => 'Ajman, UAE'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['phone' => $customer['phone']], $customer);
        }
    }
}
