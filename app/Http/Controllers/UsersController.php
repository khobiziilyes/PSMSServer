<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller {
    public function index() {
		// Users list
	}

	public function destroy() {
		// SoftDelete user
	}

	public function store() {
		// Create new user (Respect maxWorkers Limit)
	}

	public function update() {
		// Update user - Including Authorizations & setAdmin & setPass
	}

	public function show() {
		// Users Calcs
	}
}