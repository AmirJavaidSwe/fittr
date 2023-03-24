<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\InstanceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstanceController extends Controller
{
    public function __construct(InstanceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
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
