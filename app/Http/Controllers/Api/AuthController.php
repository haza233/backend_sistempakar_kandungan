<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\LocationService; // Import LocationService
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function register(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:pasien,dokter',
        ]);

        // Create a new user
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Find the user by email
        $user = User::where('email', $request->email)->first();
    
        // Check for valid user and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    
        // Create token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
        $ipAddress = $request->ip();
    
        // Check if the IP is a public address
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // Fetch location data if the IP is public
            $locationData = $this->locationService->getLocationData($ipAddress);
            
            // Prepare update data based on the location data response
            if ($locationData && $locationData['status'] === 'success') {
                $updateData = [
                    'ip_address' => $ipAddress,
                    'country' => $locationData['country'] ?? null,
                    'region' => $locationData['regionName'] ?? null,
                    'city' => $locationData['city'] ?? null,
                    'latitude' => $locationData['lat'] ?? null,
                    'longitude' => $locationData['lon'] ?? null,
                ];
            } else {
                \Log::info('Location data retrieval failed for IP ' . $ipAddress . ': ' . ($locationData['message'] ?? 'Unknown error'));
                $updateData = [
                    'ip_address' => $ipAddress,
                    'country' => null,
                    'region' => null,
                    'city' => null,
                    'latitude' => null,
                    'longitude' => null,
                ];
            }
        } else {
            // Handle reserved IP - set default values
            \Log::info('Using reserved IP address, skipping location update for IP ' . $ipAddress);
            $updateData = [
                'ip_address' => $ipAddress,
                'country' => 'Localhost',
                'region' => null,
                'city' => null,
                'latitude' => null,
                'longitude' => null,
            ];
        }
    
        // Log the data being updated
        \Log::info('Updating user location data:', $updateData);
    
        // Update user with IP and location details
        try {
            $user->update($updateData);
        } catch (\Exception $e) {
            \Log::error('Failed to update user location data: ' . $e->getMessage());
        }
    
        // Return response with token and user information
        return response()->json([
            'token' => $token,
            'id' => $user->id,
            'user' => [
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'ip_address' => $ipAddress,
            'latitude' => $updateData['latitude'], // Return latitude
            'longitude' => $updateData['longitude'], // Return longitude
        ], 200);
    }

    public function logout(Request $request)
    {
        // Delete all tokens for the user
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
