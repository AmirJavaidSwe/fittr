<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait ImageableTrait
{
    public function uploadFiles($files, $model, $path = 'images', $disk = null)
    {
        try {
            if(!$disk) {
                $disk = config('filesystems.default');
            }

            if(!Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->makeDirectory($path);
            }

            $files = collect(Arr::wrap($files));

            $files->each(function ($item) use($path, $disk, $model) {

                $original_filename = $item->getClientOriginalName();
                $filename = $item->hashName();
                $size = $item->getSize();

                $item->storeAs($path, $filename, $disk);

                $model->images()->create([
                    'original_filename' => $original_filename,
                    'filename' => $filename,
                    'path' => $path,
                    'disk' => $disk,
                    'size' => $size
                ]);
            });

        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
