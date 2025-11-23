<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * تحديث بيانات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::guard('web')->user();

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'editProfileModal');
        }

        // تحديث الاسم
        $user->name = $request->input('name');

        // تحديث الصورة إذا وُجدت
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $imagePath = $request->file('profile_picture')->store('profiles', 'public');
            $user->profile_picture = $imagePath;
        }

        $user->save();

        // ✅ رسالة نجاح


        return redirect()->back()->with('profile_success', __('web.profile_updated_successfully'));
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $user = Auth::guard('web')->user();
        $isSocialUser = !empty($user->social_id);

        $request->validate([
            'current_password' => ($isSocialUser) ? 'nullable' : 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!$isSocialUser) {
            if (!Hash::check($request->current_password, $user->password)) {


                return redirect()->back()->with('error', __('web.current_password_incorrect'));
            }
        }

        $user->password = Hash::make($request->new_password);

        if ($isSocialUser) {
            $user->social_id = null;
        }

        $user->save();

        // ✅ رسالة نجاح


        return redirect()->back()->with('success', __('web.password_changed_successfully'));
    }
}
