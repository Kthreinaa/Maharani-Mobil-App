<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerSettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('customer.settings', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('customer.settings.edit')
            ->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}
