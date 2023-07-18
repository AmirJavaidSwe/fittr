<?php

namespace App\Services\Partner;

use App\Enums\PackType;

class PackService
{
    public function canChangeType($new_type, $old_type): bool
    {
        if($new_type == $old_type){
            return true;
        }
        switch ($new_type) {
            case PackType::default->name:
                return false;
            case PackType::corporate->name:
                return false;
            
            default:
                return in_array($new_type, [PackType::class_lesson->name, PackType::service->name, PackType::hybrid->name]);
        }
    }
}