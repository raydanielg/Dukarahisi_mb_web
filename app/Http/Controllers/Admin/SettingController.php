<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show settings page.
     */
    public function index()
    {
        $maintenanceMode = Setting::getValue('maintenance_mode', '0');
        $maintenanceMessage = Setting::maintenanceMessage();

        return view('admin.settings.index', compact('maintenanceMode', 'maintenanceMessage'));
    }

    /**
     * Update maintenance mode settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        Setting::setValue('maintenance_mode', $validated['maintenance_mode'] ? '1' : '0');
        Setting::setValue('maintenance_message', $validated['maintenance_message'] ?? 'Mfumo upo katika matengenezo. Tutarudi hivi punde.');

        return redirect()->route('admin.settings.index')
            ->with('status', 'Settings updated successfully.');
    }
}
