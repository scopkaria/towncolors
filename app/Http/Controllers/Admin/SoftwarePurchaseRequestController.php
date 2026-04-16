<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwarePurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SoftwarePurchaseRequestController extends Controller
{
    public function index(): View
    {
        $requests = SoftwarePurchaseRequest::with(['product:id,title,price,currency', 'user:id,name,email'])
            ->latest()
            ->paginate(20);

        return view('admin.shop.requests', compact('requests'));
    }

    public function update(Request $request, SoftwarePurchaseRequest $softwareRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,contacted,approved,rejected,completed'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $softwareRequest->update($validated);

        return back()->with('success', 'Request updated.');
    }
}
