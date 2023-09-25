<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Enums\AppUserSource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Services\Admin\InstanceService;

class InstanceController extends Controller
{
    public $service;

    public function __construct(InstanceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if (Gate::denies('viewAny-'.AppUserSource::get('admin'). '-aws-instances-viewAny')) {
            abort(403);
        }
        //get a list of AWS Lightsail instances
        $api_results = $this->service->getInstances();

        //inject local partners into dataset
        $api_results->data = $this->service->mergePartnerInstance($api_results->data);

        return Inertia::render('Admin/Instances', [
            'page_title' => __('Instances'),
            'header' => __('AWS Lightsail Instances'),
            'api_results' => $api_results,
        ]);
    }

    public function show(Request $request, $name)
    {
        if (Gate::denies('view-'.AppUserSource::get('admin'). '-aws-instances-view')) {
            abort(403);
        }
        return Inertia::render('Admin/InstanceShow', [
            'api_results' => $this->service->getInstance($name),
            'page_title' => __('AWS Lightsail Instances'),
            'header' => array(
                [
                    'title' => __('AWS Lightsail Instances'),
                    'link' => route('admin.instances.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => $name,
                    'link' => route('admin.instances.show', ['name' => $name]),
                ],
            ),
        ]);
    }

    public function showMetric(Request $request, $name, $metric)
    {
        if (Gate::denies('showMetric-'.AppUserSource::get('admin'). '-aws-instances-showMetric')) {
            abort(403);
        }
        return Inertia::render('Admin/InstanceMetric', [
            'api_results' => $this->service->GetInstanceMetricData($name, $metric),
            'page_title' => __('Instance metrics'),
            'header' => array(
                [
                    'title' => __('AWS Lightsail Instances'),
                    'link' => route('admin.instances.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => $name,
                    'link' => route('admin.instances.show', ['name' => $name]),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Metric').' '.$metric,
                    'link' => null,
                ],
            ),
        ]);
    }
}
