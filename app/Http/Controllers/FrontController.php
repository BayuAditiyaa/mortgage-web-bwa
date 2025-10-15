<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Category;
use App\Models\Interest;
use Illuminate\Http\Request;
use App\Services\HouseService;
use App\Services\MortgageService;

class FrontController extends Controller
{
    //

    protected $houseService;
    protected $mortgageService;

    public function __construct(HouseService $houseService, MortgageService $mortgageService) {
        $this->houseService = $houseService;
        $this->mortgageService = $mortgageService;
    }


    public function index(){
        $categoriesAndCities = $this->houseService->getCategoriesAndCities();
        return view('front.index', $categoriesAndCities);
    }

    public function search(Request $request){
        $filters = $request->all();
        $houses = $this->houseService->searchHouses($filters);
        return view('front.search', $houses);
    }

    public function category(Category $category) {
        $category->load(['houses']);
        return view('front.category', $category);
    }

    public function details(House $house) {
        $houseDetails = $this->houseService->getHouseDetails($house);
        return view('front.details', $houseDetails);
    }

    public function interest(Interest $interest){
        return view('customer.mortgages.request_mortgage', $interest);
    }

    public function request_interest(Request $request){
        $this->mortgageService->handleInterestRequest($request);
        return redirect()->route('front.request_success');
    }

    public function request_success(){
        $interest = $this->mortgageService->getInterestFromSession();
        if(!$interest) {
            return redirect()->route('front.index')->with('error', 'Invalid request, please try again.');
        }
        return view('customer.mortgages.success_request', compact($interest));
    }
}
