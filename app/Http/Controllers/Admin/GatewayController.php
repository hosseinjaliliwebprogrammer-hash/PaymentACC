<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    /**
     * نمایش لیست همه‌ی گیت‌وی‌ها برای ادمین
     */
    public function index()
    {
        $gateways = Gateway::orderBy('id')->get();
        return view('admin.gateways.index', compact('gateways'));
    }

    /**
     * تغییر وضعیت فعال/غیرفعال گیت‌وی
     */
    public function toggle(Gateway $gateway)
    {
        $gateway->update([
            'is_active' => ! $gateway->is_active,
        ]);

        return back()->with('success', 'وضعیت گیت‌وی با موفقیت تغییر کرد.');
    }
}
