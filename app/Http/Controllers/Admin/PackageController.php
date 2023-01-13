<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
            'packages' => Package::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PackageCreate', [
            'page_title' => __('New package'),
            'header' => __('New package'),
        ]);
    }

    public function store(PackageRequest $request)
    {
        Package::create($request->validated());

        return redirect()->route('admin.packages.index')->with('flash_type', 'success')->with('flash_timestamp', time());
    }

    public function show(Package $package)
    {
        return Inertia::render('Admin/PackageShow', [
            'package' => $package,
            'page_title' => __('Package details'),
            'header' => __('Package details'),
        ]);
    }

    public function edit(Package $package)
    {
        return Inertia::render('Admin/PackageEdit', [
            'package' => $package,
            'page_title' => __('Package details'),
            'header' => __('Update package details'),
        ]);
    }

    public function update(PackageRequest $request, Package $package)
    {
        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        $package->update($request->validated());
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('flash_timestamp', time());
    }
}
