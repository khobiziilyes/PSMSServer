<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GroupScope implements Scope {
	public function apply(Builder $builder, Model $model) {
        $user = auth()->user();
        
        if ($user) return $builder->whereHas('Store', function($query) use($user) {
            $query->whereIn('group_id', [$user->Store->Group->id, 0]);
        });

        return $builder;
    }
}