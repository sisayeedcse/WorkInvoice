<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $company = [
            'name'             => Setting::get('company_name', config('company.name')),
            'owner'            => Setting::get('company_owner', config('company.owner')),
            'address'          => Setting::get('company_address', config('company.address')),
            'phone'            => Setting::get('company_phone', config('company.phone')),
            'email'            => Setting::get('company_email', config('company.email')),
            'tagline'          => Setting::get('company_tagline', config('company.tagline')),
            'currency'         => Setting::get('currency', config('company.currency')),
            'currency_symbol'  => Setting::get('currency_symbol', config('company.currency_symbol')),
            'terms'            => Setting::get('default_terms', config('company.terms')),
            'logo'             => Setting::get('company_logo', null),
        ];

        return view('settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $fields = [
            'company_name', 'company_owner', 'company_address',
            'company_phone', 'company_email', 'company_tagline',
            'currency', 'currency_symbol', 'default_terms',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->$field, 'company');
            }
        }

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::set('company_logo', $path, 'company');
        }

        // Handle logo removal
        if ($request->has('remove_logo') && $request->remove_logo) {
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('company_logo', null, 'company');
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
