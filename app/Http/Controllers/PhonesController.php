<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\baseController;

use App\Models\Phone;

class PhonesController extends baseController {
    protected $theClass = Phone::class;
    protected $modelName = 'phones';
    protected $whiteListOrderBy = ['name', 'brand'];
    
    public function search(Request $request) {
        $validatedData = Validator::make($request->input(), [
            'query' => 'required|regex:/^[\w\d ]+$/|min:3'
        ])->validate();

        $query = $validatedData['query'];
        $devicesDB = Phone::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])->select('name', 'id');
        
        if ($devicesDB->count() === 0) {
            $devices = $this->searchDevices($query);
            if (count($devices) === 0) return [];
            
            Phone::insert($devices);
        }
        
        return $devicesDB->get(); //->makeHidden('isPhone');
    }

    public function searchDevices($term) {
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
            'type_id' => 0,
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
