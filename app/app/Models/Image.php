<?php

namespace App\Models;

use App\Helpers\FileHelper;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * Get the path to the image file
     */
    public function path($size = 'full'): string
    {
        $suffix = $size === 'medium' ? '_m' : '';

        $imagePath = 'images/'.$this->id.$suffix.'.webp';

        if (FileHelper::exists($imagePath)) {
            return storage_asset($imagePath);
        }

        $imagePath = str_replace('.webp', '.jpg', $imagePath);

        return storage_asset($imagePath);
    }
}
