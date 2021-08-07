<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Http\Controllers\ControllersTraits\storeOrUpdateModel;
use App\Rules\PhoneNumber;

class UsersController extends Controller {
	use storeOrUpdateModel;

	protected $theClass = User::class;

	public function indexQuery($request) {
		$user = $request->user();
		
		$user_id = $user->id;
		$owner_id = $user->Store->Group->Owner->id;

		$excepted_ids = $user_id === $owner_id ? [] : [$user_id];

		return User::whereNotIn('id', [0, $owner_id, ...$excepted_ids]);
	}

	public function storeIdRule() {
		$stores_ids = auth()->user()->StoresForWorker()->pluck('id')->toArray();
		return ['required', 'integer', Rule::in($stores_ids)];
	}

	public function getValidationRules($resource_id) {
		$isCreate = is_null($resource_id);

		return [
			'name' => 'required|string|max:30',
			'phone_number' => ['required', Rule::unique('users', 'phone_number')->ignore($resource_id), new PhoneNumber],
			'password' => 'present|' . ($isCreate ? 'required' : 'nullable') . '|string|min:8|max:30',
			'store_id' => $this->storeIdRule()
		];
	}

	public function formatInput($validatedData, $isCreate) {
		$newData = array_filter($validatedData, function($key) {
			return $key !== 'password';
		}, ARRAY_FILTER_USE_KEY);

		$plainPassword = $validatedData['password'];
		if (!is_null($plainPassword)) $newData['password'] = Hash::make($plainPassword);

		return $newData;
	}

    public function index(Request $request) {
    	return $this->indexQuery($request)->get();
	}

	public function store(Request $request) {
		if (User::count() === $request->user()->Group->maxUsers) return response()->json([
	        'message' => 'Maximum reached.'
	    ], 401);

		return $this->storeOrUpdate($request->input());
	}

	public function update(Request $request, $id) {
		$user = $this->indexQuery($request)->findOrFail($id);
		return $this->storeOrUpdate($request->input(), $id);
	}

	public function destroy(Request $request, $id) {
		$user = $this->indexQuery($request)->findOrFail($id);
		$user->delete();

		return ['deleted' => true];
	}

	public function updatePermissions(Request $request, $id) {
		Bouncer::authorize('canUpdatePermissions');
		$user = $this->indexQuery($request)->findOrFail($id);

		$BASIC_PERMISSIONS = config('app.BASIC_PERMISSIONS');
		
		$validationRules = $BASIC_PERMISSIONS->flatMap(function($permission) {
			return [$permission => 'required|boolean'];
		})->toArray();

		$validatedData = Validator::make($request->input(), $validationRules)->validate();

		foreach ($validatedData as $permission => $value) $user->{$value ? 'allow' : 'disallow'}($permission);

		return $user;
	}

	public function switchStore(Request $request) {
		Bouncer::authorize('canSwitchStore');
		
		$validatedData = Validator::make($request->input(), [
			'store_id' => $this->storeIdRule()
		])->validate();

		$request->user()->update($validatedData);

		return ['success' => true];
		// Validate store_id, set store_id
	}
}