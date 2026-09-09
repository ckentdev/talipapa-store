<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesDocumentUpload
{
    protected function uploadDocument(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    protected function documentRules(bool $required = true): array
    {
        $rule = ($required ? 'required' : 'nullable').'|file|mimes:jpg,jpeg,png,pdf|max:5120';

        return ['file' => $rule];
    }
}
