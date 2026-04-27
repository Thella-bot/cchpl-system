<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MemberProfileController extends Controller
{
    /**
     * Show the member's profile edit form.
     */
    public function edit()
    {
        return view('member.profile', ['user' => auth()->user()]);
    }

    /**
     * Update contact details (name, phone, organization).
     *
     * Email is intentionally not updatable here — changing a verified
     * email address requires re-verification to prevent account hijacking.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'organization' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return back()->with('success', '✅ Your profile has been updated.');
    }

    /**
     * Update the member's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'The current password you entered is incorrect.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '✅ Password updated successfully.');
    }
}
