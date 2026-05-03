<?php

namespace App\Services;

use App\Models\City;
use App\Models\House;
use App\Models\Category;

class HouseService {

    public function getCategoriesAndCities() {
        return [
             'categories' => Category::latest()->get(),
             'cities' => City::latest()->get(),
        ];
    }

    public function searchHouses ($filters) {

        $query = House::query()->with(['city', 'category']);

        if(!empty($filters['city'])) {
            $query->where('city_id', $filters['city']);}

        if(!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);}

        if(!empty($filters['keyword'])) {
            $query->where('name', 'like', '%' . $filters['keyword'] . '%');}

        if(!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);}

        if(!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);}

        if(!empty($filters['bedroom'])) {
            $query->where('bedroom', '>=', $filters['bedroom']);}

        $houses = $query->latest()->get();

        $category = ! empty($filters['category']) ? Category::find($filters['category']) : null;
        $city = ! empty($filters['city']) ? City::find($filters['city']) : null;
        
        return compact('houses', 'category', 'city', 'filters');
    }    

    public function getHouseDetails($house){
         
        $house->load(['category', 'city', 'developer', 'photos', 'facilities', 'facilities.facility', 'interests.bank']);
        return $house;
    }
}
