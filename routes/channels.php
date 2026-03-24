<?php

use App\Models\Admin; // تأكد أن المسار إلى موديل Admin صحيح
use Illuminate\Support\Facades\Broadcast;

// القناة الخاصة للمشرفين (تتطلب المصادقة وتطابق ID)
Broadcast::channel('admin.orders.{id}', function ($admin, $id) {
    // 1. التأكد من وجود مستخدم مسجل دخوله
    if (!$admin) {
        return false;
    }

    // 2. التأكد من أن المستخدم هو Admin
    if (!$admin instanceof Admin) {
        return false;
    }

    // 3. التحقق من تطابق ID المشرف الحالي مع الـ ID في مسار القناة
    return (int) $admin->id === (int) $id;
});

// قناة المستخدمين العاديين (اتركها إذا كنت تستخدمها)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

