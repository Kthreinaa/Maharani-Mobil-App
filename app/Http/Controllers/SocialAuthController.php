<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    private ?string $googleFailureMessage = null;

    /**
     * URL endpoint OAuth Google untuk proses authorization code flow.
     */
    private const GOOGLE_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * URL endpoint OAuth Google untuk tukar authorization code menjadi access token.
     */
    private const GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    /**
     * URL endpoint user info Google untuk mengambil profil user setelah token didapat.
     */
    private const GOOGLE_USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    /**
     * Redirect user ke halaman autentikasi Google menggunakan OAuth native.
     */
    public function redirectToGoogle(Request $request)
    {
        $googleConfig = $this->googleConfig();

        if (!$googleConfig['client_id'] || !$googleConfig['client_secret'] || !$googleConfig['redirect']) {
            return redirect('/login')->withErrors([
                'email' => 'Konfigurasi Google login belum lengkap.',
            ]);
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => $googleConfig['client_id'],
            'redirect_uri' => $googleConfig['redirect'],
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect()->away(self::GOOGLE_AUTH_URL.'?'.$query);
    }

    /**
     * Menangani callback Google: validasi state, ambil token, ambil profil, lalu login/register user.
     */
    public function handleGoogleCallback(Request $request)
    {
        if ($request->filled('error')) {
            return redirect('/login')->withErrors([
                'email' => 'Login Google dibatalkan atau gagal.',
            ]);
        }

        $savedState = $request->session()->pull('google_oauth_state');
        $incomingState = $request->query('state');

        if (!$savedState || !$incomingState || !hash_equals($savedState, $incomingState)) {
            return redirect('/login')->withErrors([
                'email' => 'Sesi login Google tidak valid. Silakan ulangi login.',
            ]);
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect('/login')->withErrors([
                'email' => 'Kode otorisasi Google tidak ditemukan.',
            ]);
        }

        $token = $this->exchangeCodeForToken($code);
        if (!$token) {
            return redirect('/login')->withErrors([
                'email' => $this->googleFailureMessage ?: 'Gagal mengambil token Google. Silakan coba lagi.',
            ]);
        }

        $googleUser = $this->fetchGoogleUser($token);
        if (!$googleUser) {
            return redirect('/login')->withErrors([
                'email' => $this->googleFailureMessage ?: 'Gagal mengambil data akun Google.',
            ]);
        }

        $email = $googleUser['email'] ?? null;
        if (!$email) {
            return redirect('/login')->withErrors([
                'email' => 'Email dari akun Google tidak tersedia.',
            ]);
        }

        $name = $googleUser['name'] ?? $googleUser['given_name'] ?? 'Customer Google';
        $providerId = $googleUser['sub'] ?? null;

        if (!$providerId) {
            return redirect('/login')->withErrors([
                'email' => 'ID akun Google tidak ditemukan.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'provider' => 'google',
                'provider_id' => $providerId,
            ]);
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'phone' => null,
                'password' => null,
                'role' => 'customer',
                'provider' => 'google',
                'provider_id' => $providerId,
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect($this->redirectByRole($user));
    }

    /**
     * Menukar authorization code dari Google menjadi access token.
     */
    private function exchangeCodeForToken(string $code): ?string
    {
        $googleConfig = $this->googleConfig();

        try {
            $response = $this->googleHttpClient()
                ->asForm()
                ->post(self::GOOGLE_TOKEN_URL, [
                    'code' => $code,
                    'client_id' => $googleConfig['client_id'],
                    'client_secret' => $googleConfig['client_secret'],
                    'redirect_uri' => $googleConfig['redirect'],
                    'grant_type' => 'authorization_code',
                ]);
        } catch (ConnectionException $exception) {
            $this->googleFailureMessage = $this->resolveGoogleConnectionMessage($exception);

            Log::warning('Google OAuth token exchange failed.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        if (!$response->ok()) {
            return null;
        }

        $accessToken = $response->json('access_token');

        return is_string($accessToken) && $accessToken !== '' ? $accessToken : null;
    }

    /**
     * Mengambil profil user Google memakai access token.
     *
     * @return array<string, mixed>|null
     */
    private function fetchGoogleUser(string $accessToken): ?array
    {
        try {
            $response = $this->googleHttpClient()
                ->withToken($accessToken)
                ->get(self::GOOGLE_USERINFO_URL);
        } catch (ConnectionException $exception) {
            $this->googleFailureMessage = $this->resolveGoogleConnectionMessage($exception);

            Log::warning('Google OAuth userinfo request failed.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        if (!$response->ok()) {
            return null;
        }

        $user = $response->json();

        return is_array($user) ? $user : null;
    }

    /**
     * Mengambil konfigurasi Google OAuth dari config/services.php.
     *
     * @return array{client_id: ?string, client_secret: ?string, redirect: ?string}
     */
    private function googleConfig(): array
    {
        return [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect' => config('services.google.redirect'),
            'ca_bundle' => config('services.google.ca_bundle'),
            'disable_ssl_verification' => (bool) config('services.google.disable_ssl_verification', false),
        ];
    }

    private function googleHttpClient(): PendingRequest
    {
        $googleConfig = $this->googleConfig();

        $client = Http::timeout(20);

        if (!empty($googleConfig['ca_bundle']) && is_string($googleConfig['ca_bundle']) && is_file($googleConfig['ca_bundle'])) {
            return $client->withOptions([
                'verify' => $googleConfig['ca_bundle'],
            ]);
        }

        if ($googleConfig['disable_ssl_verification']) {
            return $client->withoutVerifying();
        }

        return $client;
    }

    private function resolveGoogleConnectionMessage(ConnectionException $exception): string
    {
        if (str_contains($exception->getMessage(), 'cURL error 77')) {
            return 'Google login sementara tidak bisa digunakan karena sertifikat SSL lokal belum terbaca. Silakan cek file CA bundle atau coba lagi setelah konfigurasi Laragon diperbarui.';
        }

        return 'Koneksi ke layanan Google sedang bermasalah. Silakan coba lagi.';
    }

    /**
     * Menentukan tujuan redirect setelah social login berdasarkan role user.
     */
    private function redirectByRole(User $user): string
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
}
