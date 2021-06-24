<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use App\Models\User;

class UsersController extends Controller {
    public function getWorkers($request) {
    	return User::where('id', '!=', $request->user()->id);
    }

    public function index(Request $request) {
    	$paginator = $this->getWorkers($request)->paginateAuto(false);
    	return $this->paginatorResponse($paginator);
	}

	public function update(Request $request, User $user) {
		$CRUD_PERMISSIONS = config('app.CRUD_PERMISSIONS');
		
		$validationRules = $CRUD_PERMISSIONS->flatMap(function($permission) {
			return [$permission => 'required|boolean'];
		})->toArray();

		$validatedData = Validator::make($request->input(), $validationRules)->validate();

		foreach ($validatedData as $permission => $value) $user->{$value ? 'allow' : 'disallow'}($permission);

		return $user;
	}

	public function destroy() {
		// SoftDelete user
	}

	public function store() {
		// Create new user (Respect maxWorkers Limit)
	}

	public function show() {
		// Users Stats
	}
}