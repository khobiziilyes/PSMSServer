<?php 

namespace App\ModelFilters;

use Illuminate\Support\Carbon;
use EloquentFilter\ModelFilter;

class baseFilter extends ModelFilter {
    protected $drop_id = false;
    public $relations = [];

    public function createdBefore($timestamp) {
    	return $this->timeFilter('created_at', $timestamp);
    }

    public function createdAfter($timestamp) {
        return $this->timeFilter('created_at', $timestamp, false);
    }

    public function updatedBefore($timestamp) {
        return $this->timeFilter('updated_at', $timestamp);
    }

    public function updatedAfter($timestamp) {
        return $this->timeFilter('updated_at', $timestamp, false);
    }

    public function deletedBefore($timestamp) {
        return $this->timeFilter('deleted_at', $timestamp);
    }

    public function deletedAfter($timestamp) {
        return $this->timeFilter('deleted_at', $timestamp, false);
    }

    public function timeFilter($field, $timestamp, $before = true) {
        $date = Carbon::createFromTimestamp($timestamp)->toDateTimeString();
        $this->where($field, ($before ? '<' : '>') . '=', $date);
    }

    public function createdBy($name) {
        return $this->whereHas('created_by_obj', function($query) use($name) {
            return $query->where('name', $name);
        });
    }

    public function updatedBy($name) {
        return $this->whereHas('updated_by_obj', function($query) use($name) {
            return $query->where('name', $name);
        });
    }
}
