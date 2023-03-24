<?php

namespace App\Services\Admin;

use App\Services\Shared\Aws\LightsailService;
use App\Models\Instance;

class InstanceService
{
    public function __construct(LightsailService $lightsail_service)
    {
        $this->lightsail_service = $lightsail_service;
    }

    // API. Returns information about all Amazon Lightsail virtual private servers, or instances.
    public function getInstances($pageToken = null) : object
    {
        $params = $pageToken ? array('pageToken' => $pageToken) : null;
        $method = 'GetInstances';
        return $this->lightsail_service->call($method, [$params]);
    }

    // API. Returns information about a specific Amazon Lightsail instance, which is a virtual private server.
    public function getInstance($name) : object
    {
        $params = array(
            'instanceName' => $name,
        );
        $method = 'GetInstance';
        return $this->lightsail_service->call($method, [$params]);
    }

    // API. Method to get single (only) metric over last 7 days period
    public function GetInstanceMetricData($name, $metric) : object
    {
        $unit = match($metric) {
            'BurstCapacityPercentage', 'CPUUtilization' => 'Percent',
            'BurstCapacityTime' => 'Seconds',
            'NetworkIn', 'NetworkOut' => 'Bytes',
            'StatusCheckFailed', 'StatusCheckFailed_Instance', 'StatusCheckFailed_System' => 'Count'
        };
        $params = array(
            'endTime' => now(),
            'instanceName' => $name,
            'metricName' => $metric,
            'period' => 60*60*27*7, //seconds, 1 week
            'startTime' => now()->subDays(7),
            'statistics' => ['Minimum', 'Maximum', 'Average'],
            'unit' => $unit,
        );
        $method = 'GetInstanceMetricData';
        return $this->lightsail_service->call($method, [$params]);
    }

    // Metod to inject local partner into remote instances
    public function mergePartnerInstance($data) : ?array
    {
        if(empty($data)){
            return null;
        }

        //collect array of remote instances into collection
        $remote_instances = collect($data['instances']);

        //get local instances where remote instance name matched local
        $local_instances = Instance::whereIn('name', $remote_instances->pluck('name'))->with(['partner'])->get()->keyBy('name')->map(function ($item) {
            return array(
                'partner_id' => $item->partner_id,
                'partner_name' => $item->partner?->name,
                'partner_email' => $item->partner?->email,
            );
        });

        //mutate remote instance array and inject partner data or defaults
        $remote_instances->transform(function ($item) use ($local_instances) {
            //inject defaults for all
            $item['partner_id'] = null;
            $item['partner_name'] = null;
            $item['partner_email'] = null;
            //if there's a match between local and remote instances, inject partner info
            if($local_instances->has($item['name'])){
                $item = array_merge($item, $local_instances[$item['name']]);
            }

            return $item;
        });
        
        //push array back
        $data['instances'] = $remote_instances->all();

        return $data;
    }
}
