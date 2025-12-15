<?php

namespace App\Http\Controllers\Auth;

use App\Models\Branch; // Import Branch model
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PendingUserRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        $branches = Branch::all(['id', 'name']);
        return Inertia::render('auth/Register', [
            'branches' => $branches,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'account_status' => User::STATUS_PENDING,
            'account_type' => User::TYPE_EXTERNAL,
        ]);

        $user->assignRole('External');

        event(new Registered($user));

        $approvers = User::permission('users.manage')
            ->whereKeyNot($user->getKey())
            ->get();

        Notification::send($approvers, new PendingUserRegistration($user));

        Auth::login($user);

        $request->session()->regenerate();

        return to_route('onboarding.pending');
    }
}
