<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
   public function login($social)
   {

       return Socialite::driver($social)->redirect();
   }

   public function callback($social)
   {
       $socialUser = Socialite::driver($social)->user();

       $user=User::updateOrCreate([
           'social_id' => $socialUser->getId(),
           'email' => $socialUser->getEmail(),
       ], [
           'name' => $socialUser->getName(),
           'password'=> bcrypt(uniqid()),
           'email_verified_at' => now(),
           'email' => $socialUser->getEmail(),
           'profile_picture' => $socialUser->getAvatar(),
       ]);
       Auth::guard('web')->login($user,true);
         return redirect()->route('home');

       // هنا يمكنك استخدام معلومات المستخدم المسجل عبر وسائل التواصل الاجتماعي
       // مثل $user->getId(), $user->getEmail(), $user->getName(), إلخ.

       // قم بتسجيل الدخول أو إنشاء مستخدم جديد في قاعدة البيانات
   }
}
