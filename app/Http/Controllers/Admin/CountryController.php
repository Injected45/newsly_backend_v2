<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCountryRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::withCount('sources');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $countries = $query->ordered()->paginate(15);

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(StoreCountryRequest $request)
    {
        $data = $request->validated();
        
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }

        Country::create($data);

        Cache::forget('countries:active');

        return redirect()->route('admin.countries.index')
            ->with('success', 'تم إضافة الدولة بنجاح');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(StoreCountryRequest $request, Country $country)
    {
        $country->update($request->validated());

        Cache::forget('countries:active');

        return redirect()->route('admin.countries.index')
            ->with('success', 'تم تحديث الدولة بنجاح');
    }

    public function destroy(Country $country)
    {
        if ($country->sources()->exists()) {
            return back()->with('error', 'لا يمكن حذف دولة لديها مصادر مرتبطة');
        }

        $country->delete();

        Cache::forget('countries:active');

        return redirect()->route('admin.countries.index')
            ->with('success', 'تم حذف الدولة بنجاح');
    }

    public function toggleActive(Country $country)
    {
        $country->update(['is_active' => !$country->is_active]);

        Cache::forget('countries:active');

        return back()->with('success', 'تم تحديث حالة الدولة');
    }
}


