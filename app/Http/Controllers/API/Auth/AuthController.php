<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VendorProfile;
use App\Services\KilakonaService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $role = $request->role ?? 'customer';

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'password'   => $request->password,
            'role'       => $role === 'vendor' ? 'customer' : $role, // starts as customer until approved
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
        ]);

        if ($role === 'vendor') {
            VendorProfile::create([
                'user_id'       => $user->id,
                'business_name' => $request->business_name,
                'region'        => $request->region,
                'is_approved'   => false,
            ]);

            // Notify vendor to wait for admin approval
            try {
                (new KilakonaService())->send(
                    $user->phone,
                    "Asante {$user->first_name}! Ombi lako la kuwa muuzaji kwa \"{$request->business_name}\" limepokelewa. " .
                    "Tafadhali subiri hadi dakika 10 hadi admin akikagua na kukuidhinisha akaunti yako. " .
                    "Utapata ujumbe mwingine ukikubaliwa."
                );
            } catch (\Throwable $e) {
                Log::error('Vendor registration SMS failed', ['error' => $e->getMessage()]);
            }
        }

        $this->otpService->generate($user, 'phone');

        return response()->json([
            'message' => 'Registration successful. OTP sent to your phone.',
            'user'    => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($user->status === 'suspended') {
            return response()->json(['message' => 'Your account has been suspended.'], 403);
        }

        if (! $user->phone_verified_at) {
            $this->otpService->generate($user, 'phone');
            return response()->json([
                'message'           => 'Phone not verified. OTP has been resent.',
                'requires_otp'      => true,
                'phone'             => $user->phone,
            ], 403);
        }

        $token = $user->createToken('sokoni-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->firstOrFail();
        $type = $request->type ?? 'phone';

        $verified = $this->otpService->verify($user, $request->code, $type);

        if (! $verified) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $token = $user->createToken('sokoni-app')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate(['phone' => ['required', 'exists:users,phone']]);

        $user = User::where('phone', $request->phone)->firstOrFail();
        $this->otpService->generate($user, 'phone');

        return response()->json(['message' => 'OTP resent successfully.']);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = $request->phone
            ? User::where('phone', $request->phone)->firstOrFail()
            : User::where('email', $request->email)->firstOrFail();

        $type = ($request->method === 'email') ? 'email' : 'password_reset';
        $this->otpService->generate($user, $type);

        return response()->json([
            'message' => 'Reset code sent successfully.',
            'phone'   => $user->phone,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->firstOrFail();

        $verified = $this->otpService->verify($user, $request->code, 'password_reset');

        if (! $verified) {
            return response()->json(['message' => 'Invalid or expired reset code.'], 422);
        }

        $user->update(['password' => $request->password]);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => new UserResource($request->user())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
