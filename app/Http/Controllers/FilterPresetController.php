<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilterPresetRequest;
use App\Models\FilterPreset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FilterPresetController extends Controller
{
    public function store(StoreFilterPresetRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->filterPresets()->updateOrCreate(
            ['name' => $validated['name']],
            ['filters' => $validated['filters']],
        );

        return back()->with('success', 'Zestaw filtrów został zapisany.');
    }

    public function destroy(Request $request, FilterPreset $filterPreset): RedirectResponse
    {
        $this->authorize('delete', $filterPreset);

        $filterPreset->delete();

        return back()->with('success', 'Zestaw filtrów został usunięty.');
    }
}
