<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class baseFilter extends ModelFilter {
    protected $drop_id = false;
    public $relations = [];

    public function before($time) {
    	$date = Carbon::createFromTimestamp($time)->toDateTimeString();
    	$this->where('created_at', '<=', $date);
    }

    public function after($time) {
    	$date = Carbon::createFromTimestamp($time)->toDateTimeString();
    	$this->where('created_at', '>=', $date);
    }

    public function user($user_id) {
    	return $this->where('created_by_id', $user_id);
    }
}
