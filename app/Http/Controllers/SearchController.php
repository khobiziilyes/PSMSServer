<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use App\Http\Controllers\ControllersTraits\PhonesAPI;

use App\Models\Phone;
use App\Models\Accessory;

use App\Models\Vendor;
use App\Models\Customer;

class SearchController extends Controller {
    use PhonesAPI;

    public function searchForVendor(Request $request) {
        $searchQuery = $this->getSearchQuery($request, 2);
        return $this->peopleBaseQuery(Vendor::query(), $searchQuery)->get();
    }

    public function searchForCustomer(Request $request) {
        $searchQuery = $this->getSearchQuery($request, 2);
        return $this->peopleBaseQuery(Customer::query(), $searchQuery)->get();
    }

    public function searchForProducts(Request $request) {
        $searchQuery = $this->getSearchQuery($request);
        return $this->getProducts($searchQuery, false);
    }

    public function searchForPhone(Request $request) {
        $searchQuery = $this->getSearchQuery($request);
        return $this->getPhones($searchQuery, false);
    }

    public function searchForPhoneWithItems(Request $request) {
        $searchQuery = $this->getSearchQuery($request);
        return $this->getPhones($searchQuery, true);
    }

    public function searchForAccessoryWithItems(Request $request) {
        $searchQuery = $this->getSearchQuery($request);
        return $this->getAccessories($searchQuery, true);
    }

    public function searchForItems(Request $request) {
        $searchQuery = $this->getSearchQuery($request);
        return $this->getProducts($searchQuery, true);
    }

	public function getSearchQuery(Request $request, $min = 3) {
		$validatedData = Validator::make($request->input(), [
            'query' => 'required|regex:/^[\w\d ]+$/|min:' . $min
        ])->validate();

        return $validatedData['query'];
    }

    public function peopleBaseQuery($builder, $searchQuery) {
        $builder = $builder->whereLike('name', $searchQuery)->select('id', 'name');
        return $builder;
    }

    public function getProducts($searchQuery, $withItems) {
        $list = collect([]);

        $list = $list->merge($this->getPhones($searchQuery, $withItems));
        $list = $list->merge($this->getAccessories($searchQuery, $withItems));

        return $list;
    }

    public function getPhones($searchQuery, $withItems) {
    	$list = $this->productsBaseQuery(Phone::query(), $searchQuery, $withItems);
        
        if (!$withItems && $list->doesntExist()) {
            $devices = $this->fetchDevices($searchQuery);
            if (count($devices) === 0) return [];
            
            Phone::insert($devices);
        }
        
        return $this->appendIsPhone($list->get());
    }

    public function getAccessories($searchQuery, $withItems) {
        $list = $this->productsBaseQuery(Accessory::query(), $searchQuery, $withItems)->get();
        return $this->appendIsPhone($list);
    }

    public function productsBaseQuery($builder, $searchQuery, $withItems) {
        $builder = $builder->whereLike('name', $searchQuery)->select('id', 'name', 'brand');
        if ($withItems) 
            $builder = $builder->whereHas('items')->with('items:id,itemable_id,itemable_type,delta,currentQuantity,defaultPrice');

        return $builder;
    }

    public function appendIsPhone($list) {
        $list->each(function($item) {
            $item->append(['isPhone']);
        });
        
        return $list;
    }
}