<?php

namespace App\Http\Controllers;

use App\Rules\ValidCin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    public function createManager(Request $request)
    {
        if (! auth()->user() || ! auth()->user()->isAdministrator()) {
            abort(403);
        }
        $validated = $request->validate([
            'manager_firstname' => 'required|string|max:255',
            'manager_lastname' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:users,email',
            'manager_cin' => ['required', 'string', 'unique:users,cin', new ValidCin],
            'manager_password' => 'required|string|min:8|confirmed',
        ], [], [
            'manager_password' => 'password',
            'manager_password_confirmation' => 'password confirmation',
        ]);

        $user = new \App\Models\User;
        $user->firstname = $validated['manager_firstname'];
        $user->lastname = $validated['manager_lastname'];
        $user->email = $validated['manager_email'];
        $user->cin = $validated['manager_cin'];
        $user->role = 'manager';
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['manager_password']);
        $user->save();

        return redirect()->route('profile.show')->with('manager_status', 'Manager account created successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('status', 'Password changed successfully!');
    }
}
