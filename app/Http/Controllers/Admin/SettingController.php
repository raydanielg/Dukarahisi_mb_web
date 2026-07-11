<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Show settings page.
     */
    public function index()
    {
        $settings = [
            'maintenance_mode' => Setting::getValue('maintenance_mode', '0'),
            'maintenance_message' => Setting::maintenanceMessage(),
            'app_name' => Setting::getValue('app_name', config('app.name', 'Elimu Store')),
            'app_currency' => Setting::getValue('app_currency', 'TZS'),
            'contact_email' => Setting::getValue('contact_email', ''),
            'contact_phone' => Setting::getValue('contact_phone', ''),
            'support_whatsapp' => Setting::getValue('support_whatsapp', ''),
            'footer_text' => Setting::getValue('footer_text', ''),
            'seo_description' => Setting::getValue('seo_description', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update all settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'app_name' => 'required|string|max:255',
            'app_currency' => 'required|string|max:10',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'support_whatsapp' => 'nullable|string|max:50',
            'footer_text' => 'nullable|string|max:500',
            'seo_description' => 'nullable|string|max:500',
        ]);

        Setting::setValue('maintenance_mode', $validated['maintenance_mode'] ? '1' : '0');
        Setting::setValue('maintenance_message', $validated['maintenance_message'] ?? 'Mfumo upo katika matengenezo. Tutarudi hivi punde.');
        Setting::setValue('app_name', $validated['app_name']);
        Setting::setValue('app_currency', $validated['app_currency']);
        Setting::setValue('contact_email', $validated['contact_email'] ?? '');
        Setting::setValue('contact_phone', $validated['contact_phone'] ?? '');
        Setting::setValue('support_whatsapp', $validated['support_whatsapp'] ?? '');
        Setting::setValue('footer_text', $validated['footer_text'] ?? '');
        Setting::setValue('seo_description', $validated['seo_description'] ?? '');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Settings updated successfully.']);
        }

        return redirect()->route('admin.settings.index')
            ->with('status', 'Settings updated successfully.');
    }

    /**
     * Clear application cache.
     */
    public function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Application cache cleared successfully.']);
        }

        return redirect()->route('admin.settings.index')
            ->with('status', 'Application cache cleared successfully.');
    }
}
