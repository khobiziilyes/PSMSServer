<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use App\Models\Phone;
use App\Models\Accessory;

class SearchController extends Controller {
	public function index(Request $request, $type) {
		$validatedData = Validator::make($request->input(), [
            'query' => 'required|regex:/^[\w\d ]+$/|min:3',
            'items' => 'required|boolean'
        ])->validate();

        $query = $validatedData['query'];
        
        return $this->getProducts($query, $type, $validatedData['items']);
    }

    public function getProducts($query, $type, $withItems) {
        $list = collect([]);

        if (in_array($type, ['all', 'accessory'])) $list = $list->merge($this->getAccessories($query, $withItems));
        if (in_array($type, ['all', 'phone'])) $list = $list->merge($this->getPhones($query, $withItems));
        
        $list->each(function($item) {
            $item->makeVisible(['isPhone']);
        });

        return $list;
    }

    public function getAccessories($searchQuery, $withItems) {
    	return $this->baseQuery(Accessory::query(), $searchQuery, $withItems)->get();
    }

    public function getPhones($searchQuery, $withItems) {
    	$list = $this->baseQuery(Phone::query(), $searchQuery, $withItems);
        
        if (!$withItems && $list->doesntExist()) {
            $devices = $this->fetchDevices($searchQuery);
            if (count($devices) === 0) return [];
            
            Phone::insert($devices);
        }
        
        return $list->get();
    }

    public function baseQuery($query, $searchQuery, $withItems) {
    	$builder = $query->whereLike('name', $searchQuery)->select('id', 'name', 'brand');
        if ($withItems) $builder->with('items:id,itemable_id,itemable_type,delta');

        return $builder;
    }

    public function fetchDevices($term) {
        try {
            $response = Http::timeout(5)->get('https://www.droidafrica.net/wp-admin/admin-ajax.php?action=aps-search&num=999&search=' . urlencode($term));
            
            if ($response->successful()) {
                $response = $response->json();
                
                if (!is_array($response) || isset($response['not'])) return [];

                $devices = array_map(function($HTML) {
                    return $this->formatDevice($HTML);
                }, $response);

                $devices = Arr::except($devices, ['more']);

                return $devices;
            }
        } catch (\Exception $ex) {}

        return [];
    }

    public function formatDevice($HTML) {
        preg_match_all('/<a .+?>.+?<\/a>/', $HTML, $links);
        $links = $links[0] ?? [];
        
        if (count($links) < 4) return null;

        preg_match('/>(.+?)</', $links[1], $PhoneName);
        preg_match('/strong>(.+?)</', $links[2], $BrandName);
        
        //preg_match('/<img src="(.+?wp-content\/uploads\/.+?)"/', $links[0], $image);
        //preg_match('/gadget\/(.+?)\//', $links[1], $PhoneLink);       
        //preg_match('/brand\/(.+?)\//', $links[2], $BrandLink);

        $time = now();

        return [
            'name' => $PhoneName[1],
            'brand' => $BrandName[1],
            'store_id' => 0,
            'created_by_id' => 0,
            'updated_by_id' => 0,
            'created_at' => $time,
            'updated_at' => $time
            
            //'link' => $PhoneLink[1],
            //'brandLink' => $BrandLink[1],
            //'image' => $image[1]
        ];
    }

    public function getDeviceSpecs($endPoint) {
        $response = Http::get('https://www.droidafrica.net/gadget/' . $endPoint);
        $response = $response->body();
        $response = str_replace("\n", '', $response);
        $response = str_replace("\r", '', $response);

        
        preg_match_all('/aps-group-title">(.+?)<(.+?)<\/table/', $response, $allTables);
        
        $Specs = [];

        for ($i = 0; $i < count($allTables[1]); $i++)
            $Specs[trim($allTables[1][$i])] = $allTables[2][$i];
        
        $Specs = array_map(function($HTML) {
            preg_match_all('/<strong class=".*?>(.+?)</', $HTML, $titles);
            $titles = $titles[1];
            
            preg_match_all('/"aps-1co">(.+?)<\/span/', $HTML, $values);
            $values = $values[1];

            $Whatever = [];
            for ($i = 0; $i < count($titles); $i++){
                $value = $values[$i];
                if (!Str::contains($value, ['<', '>'])) $Whatever[trim($titles[$i])] = str_replace('<br />', "\n", $value);
            }

            return $Whatever;
        }, $Specs);

        return $Specs;
    }
}