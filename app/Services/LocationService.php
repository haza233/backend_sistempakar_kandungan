<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class LocationService
{
    public function getLocationData($ipAddress)
    {
        try {
            // Use an external API to get location data based on the IP address
            $response = Http::get("http://ip-api.com/json/{$ipAddress}");

            // Check if the response was successful
            if ($response->successful()) {
                return $response->json();
            } else {
                // Log the error response for debugging purposes
                \Log::error('Location service request failed for IP ' . $ipAddress . ': ' . $response->body());
                return [
                    'status' => 'fail',
                    'message' => 'Unable to retrieve location data',
                ];
            }
        } catch (RequestException $e) {
            // Log the exception message
            \Log::error('Exception occurred while fetching location data: ' . $e->getMessage());
            return [
                'status' => 'fail',
                'message' => 'Exception occurred during location request',
            ];
        } catch (\Exception $e) {
            // Catch any other exceptions
            \Log::error('Unexpected error while fetching location data: ' . $e->getMessage());
            return [
                'status' => 'fail',
                'message' => 'An unexpected error occurred',
            ];
        }
    }
}
