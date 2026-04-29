<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the bidan's profile form.
     */
    public function edit(Request $request): View
    {
        return view('bidan.profile.edit', [
            'user' => $request->user(),
        ]);
    }
}
