<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\VendorProfile;
use App\Services\KilakonaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VendorProfileController extends Controller
{
    public function myProfile(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendorProfile;

        if (! $vendor) {
            return response()->json(['data' => null, 'applied' => false]);
        }

        return response()->json([
            'data'    => new VendorResource($vendor),
            'applied' => true,
        ]);
    }

    public function apply(Request $request): JsonResponse
    {
        if ($request->user()->vendorProfile) {
            return response()->json(['message' => 'You have already applied.'], 422);
        }

        $data = $request->validate([
            'business_name' => 'required|string|max:120',
            'description'   => 'nullable|string|max:1000',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'region'        => 'required|string|max:80',
            'district'      => 'nullable|string|max:80',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
        ]);

        $vendor = $request->user()->vendorProfile()->create(array_merge($data, [
            'is_approved' => false,
            'is_open'     => false,
            'rating'      => 0,
        ]));

        // Notify vendor via SMS to wait for admin approval
        try {
            $user = $request->user();
            (new KilakonaService())->send(
                $user->phone,
                "Asante {$user->first_name}! Ombi lako la kuwa muuzaji kwa \"{$vendor->business_name}\" limepokelewa. " .
                "Tafadhali subiri hadi dakika 10 hadi admin akikagua na kukuidhinisha akaunti yako. " .
                "Utapata ujumbe mwingine ukikubaliwa."
            );
        } catch (\Throwable $e) {
            Log::error('Vendor application SMS failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Application submitted. Awaiting admin approval.',
            'data'    => new VendorResource($vendor),
        ], 201);
    }

    public function update(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendorProfile;

        if (! $vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        $data = $request->validate([
            'business_name' => 'sometimes|string|max:120',
            'description'   => 'nullable|string|max:1000',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'region'        => 'sometimes|string|max:80',
            'district'      => 'nullable|string|max:80',
            'ward'          => 'nullable|string|max:80',
            'street'        => 'nullable|string|max:120',
            'is_open'       => 'sometimes|boolean',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
        ]);

        $vendor->update($data);

        return response()->json(['data' => new VendorResource($vendor->fresh())]);
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $vendor = $request->user()->vendorProfile;

        if (! $vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        $request->validate(['logo' => 'required|image|max:2048']);

        if ($vendor->logo) {
            Storage::disk('public')->delete($vendor->logo);
        }

        $path = $request->file('logo')->store('vendors/logos', 'public');
        $vendor->update(['logo' => $path]);

        return response()->json([
            'message' => 'Logo updated.',
            'data'    => new VendorResource($vendor->fresh()),
        ]);
    }
}
