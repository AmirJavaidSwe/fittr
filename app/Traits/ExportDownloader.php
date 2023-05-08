<?php

namespace App\Traits;

use App\Enums\ExportFileType;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait ExportDownloader
{
    private function downloadFromDisk($export)
    {
        switch ($export->storage_disk) {
            case 'private-remote':
                return $this->downloadFromS3($export);
            case 'private':
                return $this->downloadFromLocalStorage($export);
        }

        return redirect()->back();
    }

    private function downloadFromS3($export): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $storage = Storage::disk($export->storage_disk);
        $temporaryUrl = $storage->temporaryUrl(
            $export->file_path,
            Carbon::now()->addMinutes(),
            [
                'ResponseContentType' => Storage::disk($export->storage_disk)->mimeType($export->file_path),
                'ResponseContentDisposition' => 'attachment; filename="' . $export->file_name . '"',
            ]
        );

        return redirect($temporaryUrl);
    }

    private function downloadFromLocalStorage($export): BinaryFileResponse
    {
        $storage = Storage::disk($export->storage_disk);
        $filePath = $storage->path($export->file_path);

        return response()->download($filePath, $export->file_name, [
            'Content-Type' => Storage::disk($export->storage_disk)->mimeType($export->file_path),
            'Content-Disposition' => 'attachment; filename="' . $export->file_name . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0'
        ]);
    }
}
