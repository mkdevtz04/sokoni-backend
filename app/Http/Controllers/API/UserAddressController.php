<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->latest('is_default')
            ->latest()
            ->get();

        return response()->json(['data' => $addresses]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label'          => 'nullable|string|max:50',
            'recipient_name' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'region'         => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'nullable|string|max:100',
            'street'         => 'nullable|string|max:200',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'is_default'     => 'boolean',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($user, $data) {
            if (! empty($data['is_default'])) {
                $user->addresses()->update(['is_default' => false]);
            }

            // Make first address default automatically
            if ($user->addresses()->count() === 0) {
                $data['is_default'] = true;
            }

            $address = $user->addresses()->create($data);

            return response()->json(['data' => $address], 201);
        });
    }

    public function update(Request $request, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'label'          => 'nullable|string|max:50',
            'recipient_name' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'region'         => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'nullable|string|max:100',
            'street'         => 'nullable|string|max:200',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'is_default'     => 'boolean',
        ]);

        return DB::transaction(function () use ($request, $address, $data) {
            if (! empty($data['is_default'])) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            $address->update($data);

            return response()->json(['data' => $address->fresh()]);
        });
    }

    public function destroy(Request $request, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return response()->json(['message' => 'Address deleted']);
    }

    public function setDefault(Request $request, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::transaction(function () use ($request, $address) {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return response()->json(['data' => $address->fresh()]);
    }
}
