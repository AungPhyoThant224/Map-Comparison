<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Customer;
use App\Models\CustomerPhoto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantType = BusinessType::create([
            'name' => 'Restaurant',
            'marker_icon' => 'restaurant-marker.svg'
        ]);

        $hotelType = BusinessType::create([
            'name' => 'Hotel',
            'marker_icon' => 'hotel-marker.svg'
        ]);

        $ykko = Customer::create([
            'business_type_id' => $restaurantType->id,
            'name' => 'YKKO (GMP Wholesale Center)',
            'phone' => '09 123 456 789',
            'email' => 'info@ykko.com.mm',
            'website' => 'ykko.com.mm',
            'address' => 'Q5P9+CWX, Myanma Gone Yi St, Yangon',
            'latitude' => 16.786309,
            'longitude' => 96.160411,
            'rating' => 4.0,
            'review_count' => 2,
        ]);

        CustomerPhoto::create([
            'customer_id' => $ykko->id,
            'photo_path' => 'ykko-main.jpg',
            'is_primary' => true
        ]);

        Customer::create([
            'business_type_id' => $hotelType->id,
            'name' => 'Rose Garden Hotel',
            'phone' => '01 371 992',
            'address' => 'Upper Pansodan Road, Yangon',
            'latitude' => 16.7905,
            'longitude' => 96.1585,
            'rating' => 4.5,
            'review_count' => 1250,
        ]);
    }
}
