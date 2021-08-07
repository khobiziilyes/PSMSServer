<?php 

namespace App\ModelFilters;

use Illuminate\Support\Carbon;
use EloquentFilter\ModelFilter;

class baseFilter extends ModelFilter {
    protected $drop_id = false;
    public $relations = [];

    public function id($value) {
        return $this->where('id', $value);
    }

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

    public function createdBy($id) {
        if ($id == '-1') return $this->doesntHave('created_by_obj');
        return $this->where('created_by_id', $id);
    }

    public function updatedBy($id) {
        if ($id == '-1') return $this->doesntHave('updated_by_obj');
        return $this->where('updated_by_id', $id);
    }
}
