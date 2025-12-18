<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'canLogin' => app('router')->has('login'),
            'canRegister' => app('router')->has('register'),
        ]);
    }
}
