<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverApplicationController extends Controller
{
    public function show()
    {
        return view('become-driver');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:100',
            'phone'               => 'required|string|max:20|unique:driver_applications,phone',
            'email'               => 'nullable|email|max:100',
            'national_id_number'  => 'required|string|max:50',
            'driver_license'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'application_photo'   => 'required|file|mimes:jpg,jpeg,png|max:3072',
            'national_id_image'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'phone.unique' => 'This phone number has already submitted an application.',
        ]);

        $licensePath = $request->file('driver_license')
            ->store('driver-applications/licenses', 'public');

        $photoPath = $request->file('application_photo')
            ->store('driver-applications/photos', 'public');

        $idImagePath = $request->file('national_id_image')
            ->store('driver-applications/national-ids', 'public');

        DriverApplication::create([
            'full_name'              => $data['full_name'],
            'phone'                  => $data['phone'],
            'email'                  => $data['email'] ?? null,
            'national_id_number'     => $data['national_id_number'],
            'driver_license_path'    => $licensePath,
            'application_photo_path' => $photoPath,
            'national_id_image_path' => $idImagePath,
            'status'                 => 'pending',
        ]);

        return redirect()->route('become-driver')
            ->with('success', 'Application submitted! We will review it and contact you via SMS.');
    }
}
