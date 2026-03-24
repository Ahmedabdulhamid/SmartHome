<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\Cart;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('authentication.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request): RedirectResponse
{
    logger()->info('LOGIN STARTED', [
        'email' => $request->email,
        'session_id' => session()->getId(),
    ]);

    // 1) Recaptcha check
    if (!$request->input('g-recaptcha-response')) {
        logger()->warning('Recaptcha token missing');
        throw ValidationException::withMessages([
            'g-recaptcha-response' => 'Recaptcha Token is missing.'
        ]);
    }

    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => env('RECAPTCHA_SECRET_KEY'),
        'response' => $request->input('g-recaptcha-response'),
    ]);

    $data = $response->json();

    logger()->info('Recaptcha response', $data);

    if (!isset($data['success']) || $data['success'] == false) {
        logger()->warning('Recaptcha failed');
        throw ValidationException::withMessages([
            'g-recaptcha-response' => 'Invalid ReCAPTCHA'
        ]);
    }

    // 2) حفظ session القديمة
    $oldSessionId = session()->getId();



    // 3) تسجيل الدخول
    try {
        $request->authenticate();

    } catch (\Exception $e) {

        throw $e;
    }

    // 4) regenerate session
    $request->session()->regenerate();


    // 5) دمج cart
    $cart = Cart::where('session_id', $oldSessionId)->first();

    if ($cart) {

        $cart->user_id = auth()->guard('web')->user()->id;
        $cart->session_id = null;
        $cart->save();
    }

    logger()->info('LOGIN FINISHED SUCCESSFULLY');

    return redirect()->intended(route('home', absolute: false));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1) احفظ user id قبل تسجيل الخروج
        $userId = auth()->id();

        // 2) احفظ session القديمة
        $oldSessionId = session()->getId();

        // 3) تسجيل الخروج
        Auth::guard('web')->logout();

        // 4) إلغاء session
        $request->session()->invalidate();

        // 5) عمل session جديدة
        $request->session()->regenerateToken();

        // 6) اجيب السيشن الجديدة بعد logout
        $newSessionId = session()->getId();

        // 7) لو المستخدم كان عنده cart → رجعها ليه كـ guest
        $cart = Cart::where('user_id', $userId)->latest()->first();

        if ($cart) {
            $cart->session_id = $newSessionId; // رجع cart للسيشن الجديدة
            $cart->user_id = null;             // رجعها Guest cart
            $cart->save();
        }

        return redirect('/');
    }
}
