<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Package;
use App\Enums\AppUserSource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\PackageRequest;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('viewAny-'.AppUserSource::admin->name . '-packages-viewAny')) {
            abort(403);
        }

        return Inertia::render('Admin/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
            'packages' => Package::all(),
        ]);
    }

    public function create()
    {
        if (Gate::denies('create-'.AppUserSource::admin->name . '-packages-create')) {
            abort(403);
        }

        return Inertia::render('Admin/PackageCreate', [
            'page_title' => __('New package'),
            'header' => __('New package'),
        ]);
    }

    public function store(PackageRequest $request)
    {
        if (Gate::denies('create-'.AppUserSource::admin->name . '-packages-create')) {
            abort(403);
        }

        Package::create($request->validated());

        return redirect()->route('admin.packages.index')->with('flash_type', 'success')->with('flash_timestamp', time());
    }

    public function show(Package $package)
    {
        if (Gate::denies('view-'.AppUserSource::admin->name . '-packages-view')) {
            abort(403);
        }

        return Inertia::render('Admin/PackageShow', [
            'package' => $package,
            'page_title' => __('Package details'),
            'header' => __('Package details'),
        ]);
    }

    public function edit(Package $package)
    {
        if (Gate::denies('update-'.AppUserSource::admin->name . '-packages-update')) {
            abort(403);
        }

        return Inertia::render('Admin/PackageEdit', [
            'package' => $package,
            'page_title' => __('Package details'),
            'header' => __('Update package details'),
        ]);
    }

    public function update(PackageRequest $request, Package $package)
    {
        if (Gate::denies('update-'.AppUserSource::admin->name . '-packages-update')) {
            abort(403);
        }

        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        $package->update($request->validated());
    }

    public function destroy(Package $package)
    {
        if (Gate::denies('destroy-'.AppUserSource::admin->name . '-packages-destroy')) {
            abort(403);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')->with('flash_timestamp', time());
    }
}
