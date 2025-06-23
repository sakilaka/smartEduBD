<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImageUpload
{
    /**
     * Upload file
     *
     * @param UploadedFile $file
     * @param string $path [$height, $width]
     * @param string $prevPath
     * @param bool $withDate
     * @return string
     */
    protected function upload($file, $path, $prevPath = ''): string
    {
        $height = (int) isset($this->resize[0]) ? $this->resize[0] : false;
        $width = (int) isset($this->resize[1]) ? $this->resize[1] : false;

        if ($prevPath) {
            $prevPath = $this->getOriginalPath($prevPath);
            if (Storage::disk('spaces')->exists($prevPath)) {
                Storage::disk('spaces')->delete($prevPath);
            }
        }

        if ($file instanceof UploadedFile) {

            $image = ($height && $width) ? Image::make($file)->resize($height, $width)->encode('jpg') : Image::make($file)->encode('jpg');

            $extension  = $file->extension();
            $fileName   = sprintf('%s.%s', uniqid(time()), $extension);

            $destination = $this->withDateFolder ? sprintf('%s/%s', $path, date('Y/M/d')) : $path;

            Storage::disk('spaces')->put("{$destination}/{$fileName}", $image->stream(), 'public');
            return "{$destination}/{$fileName}";
        }

        return '';
    }

    /**
     * Get previous file path
     *
     * @param string $file
     * @return string
     */
    public function getOriginalPath($file)
    {
        $bucket = config('filesystems.disks.spaces.bucket');
        if (Str::contains($file, "{$bucket}/")) {
            return Str::after($file, "{$bucket}/");
        }

        return $file;
    }
}
