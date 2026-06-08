<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileSetupController extends Controller
{
    public function setup(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['nullable', 'email', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        return response()->json([
            'message' => 'Profile updated.',
            'user'    => new UserResource($request->user()->fresh()),
        ]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('photo')->store("profile_pictures/{$user->id}", 'public');

        $user->update(['profile_picture' => $path]);

        return response()->json([
            'message'         => 'Photo uploaded.',
            'profile_picture' => asset('storage/' . $path),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $request->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function setLocation(Request $request): JsonResponse
    {
        $request->validate([
            'region'         => ['required', 'string'],
            'district'       => ['required', 'string'],
            'ward'           => ['nullable', 'string'],
            'street'         => ['nullable', 'string'],
            'latitude'       => ['nullable', 'numeric'],
            'longitude'      => ['nullable', 'numeric'],
            'label'          => ['nullable', 'string'],
            'recipient_name' => ['nullable', 'string'],
            'phone'          => ['nullable', 'string'],
        ]);

        $user = $request->user();

        // Clear previous default
        UserAddress::where('user_id', $user->id)->update(['is_default' => false]);

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => $request->label ?? 'Home',
            'recipient_name' => $request->recipient_name ?? $user->full_name,
            'phone'          => $request->phone ?? $user->phone,
            'region'         => $request->region,
            'district'       => $request->district,
            'ward'           => $request->ward,
            'street'         => $request->street,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'is_default'     => true,
        ]);

        $user->update(['profile_completed' => true]);

        return response()->json([
            'message' => 'Location saved. Profile setup complete.',
            'user'    => new UserResource($user->fresh()),
        ]);
    }
}
