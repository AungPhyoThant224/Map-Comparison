<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Customer;
use App\Models\CustomerPhoto;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Business Types
        $restaurantType = BusinessType::firstOrCreate([
            'name' => 'Restaurant'
        ], ['marker_icon' => 'restaurant-marker.png']);

        $hotelType = BusinessType::firstOrCreate([
            'name' => 'Hotel'
        ], ['marker_icon' => 'hotel-marker.png']);

        $cafeType = BusinessType::firstOrCreate([
            'name' => 'Cafe'
        ], ['marker_icon' => 'cafe-marker.png']);

        // 2. Customer Data Array (Yangon Center)
        $locations = [
            [
                'type' => $restaurantType,
                'name' => 'YKKO (GMP Wholesale Center)',
                'lat' => 16.786309, 'lng' => 96.160411,
                'rating' => 4.2, 'reviews' => 150,
                'photos' => [
                    'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800',
                    'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $hotelType,
                'name' => 'Rose Garden Hotel',
                'lat' => 16.7905, 'lng' => 96.1585,
                'rating' => 4.5, 'reviews' => 1250,
                'photos' => [
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800',
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800',
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $cafeType,
                'name' => 'Yangon Tea House',
                'lat' => 16.7725, 'lng' => 96.1610,
                'rating' => 4.8, 'reviews' => 890,
                'photos' => [
                    'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $restaurantType,
                'name' => '999 Shan Noodle Shop',
                'lat' => 16.7785, 'lng' => 96.1590,
                'rating' => 4.6, 'reviews' => 2100,
                'photos' => [
                    'https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=800',
                    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $hotelType,
                'name' => 'Pan Pacific Yangon',
                'lat' => 16.7820, 'lng' => 96.1540,
                'rating' => 4.7, 'reviews' => 450,
                'photos' => [
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $cafeType,
                'name' => 'Easy Cafe',
                'lat' => 16.7930, 'lng' => 96.1500,
                'rating' => 4.3, 'reviews' => 120,
                'photos' => [
                    'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $restaurantType,
                'name' => 'Feel Myanmar Food',
                'lat' => 16.7980, 'lng' => 96.1450,
                'rating' => 4.4, 'reviews' => 3400,
                'photos' => [
                    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800',
                    'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $hotelType,
                'name' => 'The Strand Yangon',
                'lat' => 16.7700, 'lng' => 96.1620,
                'rating' => 4.9, 'reviews' => 560,
                'photos' => [
                    'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $restaurantType,
                'name' => 'Min Lan Seafood',
                'lat' => 16.8050, 'lng' => 96.1550,
                'rating' => 4.1, 'reviews' => 780,
                'photos' => [
                    'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=800'
                ]
            ],
            [
                'type' => $cafeType,
                'name' => 'Nourish Cafe',
                'lat' => 16.8020, 'lng' => 96.1650,
                'rating' => 4.5, 'reviews' => 230,
                'photos' => [
                    'https://images.unsplash.com/photo-1445116572660-236099ec97a0?auto=format&fit=crop&w=800',
                    'https://images.unsplash.com/photo-1466633364863-567df47a7fe5?auto=format&fit=crop&w=800'
                ]
            ],
        ];

        // 3. Seed to Database
        foreach ($locations as $loc) {
            $customer = Customer::create([
                'business_type_id' => $loc['type']->id,
                'name' => $loc['name'],
                'address' => 'Yangon, Myanmar',
                'latitude' => $loc['lat'],
                'longitude' => $loc['lng'],
                'rating' => $loc['rating'],
                'review_count' => $loc['reviews'],
            ]);

            foreach ($loc['photos'] as $index => $url) {
                CustomerPhoto::create([
                    'customer_id' => $customer->id,
                    'photo_path' => $url, // Storing full URL
                    'is_primary' => $index === 0
                ]);
            }
        }
    }
}
