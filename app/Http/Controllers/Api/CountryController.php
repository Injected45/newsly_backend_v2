<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    /**
     * List all active countries
     */
    public function index(Request $request): JsonResponse
    {
        $countries = Cache::remember('countries:active', config('news.cache.countries_ttl'), function () {
            return Country::active()
                ->ordered()
                ->withCount(['sources' => fn($q) => $q->active()])
                ->get();
        });

        return $this->success(CountryResource::collection($countries));
    }

    /**
     * Get country details
     */
    public function show(Country $country): JsonResponse
    {
        if (!$country->is_active) {
            return $this->error('Country not found', 404);
        }

        $country->loadCount(['sources' => fn($q) => $q->active()]);

        return $this->success(new CountryResource($country));
    }
}



