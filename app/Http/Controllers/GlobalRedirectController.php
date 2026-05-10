<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalRedirectController extends Controller
{
    /**
     * Handle the root redirect.
     */
    public function __invoke(Request $request)
    {
        // For development, we default to Ghana/English
        return redirect()->route('home', [
            'country' => 'gh',
            'lang'    => 'en'
        ]);
    }
}
