<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {


        return view('authentication.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$request->input('g-recaptcha-response')) {
            dd('Recaptcha Token is missing from the request.');
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
        ]);

        $data = $response->json();

        if ($data['success'] == false) {
            throw ValidationException::withMessages(['g-recaptcha-response' => 'Invalid ReCAPTCHA']);
        }

        // التحقق من البيانات
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        // 🟢 احفظ session_id قبل تسجيل الدخول
        $oldSessionId = session()->getId();

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // تسجيل الدخول
        Auth::login($user);

        // 🟡 Laravel بيعمل regenerate session بعد login تلقائياً
        // لذلك يجب البحث بالـ session القديم
        $cart = Cart::where('session_id', $oldSessionId)->first();

        if ($cart) {
            $cart->user_id = $user->id;
            $cart->session_id = null; // بقت cart مستخدم مش ضيف
            $cart->save();
        }

        return redirect(route('dashboard', absolute: false));
    }
}
