<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Alias email internal lama yang harus diarahkan ke akun internal resmi.
     */
    private const INTERNAL_EMAIL_ALIASES = [
        'supervisormaharani@gmail.com' => 'supervisor@maharani.com',
        'marketingmaharani@gmail.com' => 'marketing@maharani.com',
        'ownermaharani@gmail.com' => 'owner@maharani.com',
    ];

    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('pages.login');
    }

    /**
     * Menampilkan halaman register.
     */
    public function showRegister()
    {
        return view('pages.register');
    }

    /**
     * Memproses login manual dengan validasi email dan password.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = $this->canonicalizeEmail($validated['email']);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            // Jika email belum pernah terdaftar, arahkan ke halaman register (wajib daftar dulu).
            return redirect()
                ->route('register')
                ->with('error', 'Akun belum terdaftar. Silakan register terlebih dahulu.')
                ->withInput(['email' => $email]);
        }

        if (!$user->password || !Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'password salah'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect($this->redirectByRole($user));
    }

    /**
     * Memproses registrasi user baru dengan default role customer.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $this->normalizeEmail($validated['email']);

        if ($this->isReservedInternalEmail($email)) {
            return back()->withErrors([
                'email' => 'Email ini khusus akun internal. Silakan login dengan akun yang dibuat supervisor.',
            ])->withInput();
        }

        User::create([
            'name' => $validated['name'],
            'email' => $email,
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'provider' => null,
            'provider_id' => null,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Akun telah didaftarkan. Silakan login dengan email dan password yang sudah dibuat.');
    }

    /**
     * Melakukan logout user dan membersihkan sesi login.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('landing')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Menentukan tujuan redirect setelah login berdasarkan role user.
     */
    protected function redirectByRole(User $user): string
    {
        if ($user->role === 'customer') {
            return route('customer.home');
        }

        if ($user->role === 'supervisor') {
            return '/supervisor/dashboard';
        }

        if ($user->role === 'marketing') {
            return '/marketing/dashboard';
        }

        if ($user->role === 'owner') {
            return '/owner/dashboard';
        }

        return route('home.public');
    }

    /**
     * Menyamakan format email agar pencarian login konsisten.
     */
    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * Mengubah alias email internal lama ke email internal resmi.
     */
    private function canonicalizeEmail(string $email): string
    {
        $email = $this->normalizeEmail($email);

        return self::INTERNAL_EMAIL_ALIASES[$email] ?? $email;
    }

    /**
     * Mencegah email internal resmi/alias dipakai registrasi customer.
     */
    private function isReservedInternalEmail(string $email): bool
    {
        return in_array($email, array_keys(self::INTERNAL_EMAIL_ALIASES), true)
            || in_array($email, array_values(self::INTERNAL_EMAIL_ALIASES), true);
    }
}
