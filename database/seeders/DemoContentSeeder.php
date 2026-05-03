<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Category;
use App\Models\City;
use App\Models\Facility;
use App\Models\House;
use App\Models\Interest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyDemoAssets();

        $categories = collect([
            ['name' => 'Modern Minimalist', 'photo' => 'demo/categories/thumbnails-1.png'],
            ['name' => 'Family House', 'photo' => 'demo/categories/thumbnails-2.png'],
            ['name' => 'Smart Living', 'photo' => 'demo/categories/thumbnails-3.png'],
            ['name' => 'Premium Cluster', 'photo' => 'demo/categories/thumbnails-4.png'],
        ])->mapWithKeys(fn (array $data) => [
            $data['name'] => Category::firstOrCreate(['name' => $data['name']], ['photo' => $data['photo']]),
        ]);

        $cities = collect([
            ['name' => 'Jakarta Selatan', 'photo' => 'demo/cities/hero-image.webp'],
            ['name' => 'Tangerang', 'photo' => 'demo/cities/hero-image.webp'],
            ['name' => 'Bekasi', 'photo' => 'demo/cities/hero-image.webp'],
        ])->mapWithKeys(fn (array $data) => [
            $data['name'] => City::firstOrCreate(['name' => $data['name']], ['photo' => $data['photo']]),
        ]);

        $banks = collect([
            ['name' => 'Bank Mandiri', 'photo' => 'demo/banks/mandiri.svg'],
            ['name' => 'Bank BCA', 'photo' => 'demo/banks/bca.svg'],
            ['name' => 'Bank DBS', 'photo' => 'demo/banks/dbs.svg'],
        ])->mapWithKeys(fn (array $data) => [
            $data['name'] => Bank::firstOrCreate(['name' => $data['name']], ['photo' => $data['photo']]),
        ]);

        $facilities = collect([
            ['name' => 'Hospital', 'photo' => 'demo/facilities/hospital-red.svg'],
            ['name' => 'Shopping Area', 'photo' => 'demo/facilities/shopping-bag-green.svg'],
            ['name' => 'Security', 'photo' => 'demo/facilities/security-user-purple.svg'],
            ['name' => 'Public Transport', 'photo' => 'demo/facilities/buildings-blue.svg'],
        ])->mapWithKeys(fn (array $data) => [
            $data['name'] => Facility::firstOrCreate(['name' => $data['name']], ['photo' => $data['photo']]),
        ]);

        $houses = [
            [
                'name' => 'Tedja Green Residence',
                'thumbnail' => 'demo/houses/house-details-1.png',
                'category' => 'Family House',
                'city' => 'Jakarta Selatan',
                'price' => 850000000,
                'bedroom' => 3,
                'bathroom' => 2,
                'land_area' => 96,
                'building_area' => 110,
            ],
            [
                'name' => 'Arkadia Smart Cluster',
                'thumbnail' => 'demo/houses/house-details-2.png',
                'category' => 'Smart Living',
                'city' => 'Tangerang',
                'price' => 625000000,
                'bedroom' => 2,
                'bathroom' => 2,
                'land_area' => 72,
                'building_area' => 80,
            ],
            [
                'name' => 'Cendana Premier House',
                'thumbnail' => 'demo/houses/house-details-3.png',
                'category' => 'Premium Cluster',
                'city' => 'Bekasi',
                'price' => 1150000000,
                'bedroom' => 4,
                'bathroom' => 3,
                'land_area' => 140,
                'building_area' => 160,
            ],
        ];

        foreach ($houses as $data) {
            $house = House::firstOrCreate([
                'name' => $data['name'],
            ], [
                'developer_id' => null,
                'thumbnail' => $data['thumbnail'],
                'certificate' => 'SHM',
                'about' => 'Rumah demo untuk simulasi KPR dengan akses lokasi strategis dan fasilitas keluarga yang lengkap',
                'price' => $data['price'],
                'bedroom' => $data['bedroom'],
                'bathroom' => $data['bathroom'],
                'electric' => 2200,
                'land_area' => $data['land_area'],
                'building_area' => $data['building_area'],
                'category_id' => $categories[$data['category']]->id,
                'city_id' => $cities[$data['city']]->id,
            ]);

            foreach (['demo/houses/house-details-1.png', 'demo/houses/house-details-2.png', 'demo/houses/house-details-3.png', 'demo/houses/house-details-4.png'] as $photo) {
                $house->photos()->firstOrCreate(['photo' => $photo]);
            }

            foreach ($facilities as $facility) {
                $house->facilities()->firstOrCreate(['facility_id' => $facility->id]);
            }

            Interest::firstOrCreate([
                'house_id' => $house->id,
                'bank_id' => $banks['Bank Mandiri']->id,
            ], [
                'interest' => 6,
                'duration' => 10,
            ]);

            Interest::firstOrCreate([
                'house_id' => $house->id,
                'bank_id' => $banks['Bank BCA']->id,
            ], [
                'interest' => 7,
                'duration' => 15,
            ]);
        }
    }

    private function copyDemoAssets(): void
    {
        $copies = [
            'assets/images/thumbnails/thumbnails-1.png' => 'demo/categories/thumbnails-1.png',
            'assets/images/thumbnails/thumbnails-2.png' => 'demo/categories/thumbnails-2.png',
            'assets/images/thumbnails/thumbnails-3.png' => 'demo/categories/thumbnails-3.png',
            'assets/images/thumbnails/thumbnails-4.png' => 'demo/categories/thumbnails-4.png',
            'assets/images/backgrounds/hero-image.webp' => 'demo/cities/hero-image.webp',
            'assets/images/logos/mandiri.svg' => 'demo/banks/mandiri.svg',
            'assets/images/logos/bca.svg' => 'demo/banks/bca.svg',
            'assets/images/logos/dbs.svg' => 'demo/banks/dbs.svg',
            'assets/images/icons/hospital-red.svg' => 'demo/facilities/hospital-red.svg',
            'assets/images/icons/shopping-bag-green.svg' => 'demo/facilities/shopping-bag-green.svg',
            'assets/images/icons/security-user-purple.svg' => 'demo/facilities/security-user-purple.svg',
            'assets/images/icons/buildings-blue.svg' => 'demo/facilities/buildings-blue.svg',
            'assets/images/thumbnails/house-details-1.png' => 'demo/houses/house-details-1.png',
            'assets/images/thumbnails/house-details-2.png' => 'demo/houses/house-details-2.png',
            'assets/images/thumbnails/house-details-3.png' => 'demo/houses/house-details-3.png',
            'assets/images/thumbnails/house-details-4.png' => 'demo/houses/house-details-4.png',
        ];

        foreach ($copies as $from => $to) {
            $source = public_path($from);
            $destination = storage_path('app/public/' . $to);

            if (! File::exists($source) || File::exists($destination)) {
                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            File::copy($source, $destination);
        }
    }
}
