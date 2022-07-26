<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * 
 */
trait UploadPhoto
{
    protected function uploadPhoto($value, $attribute_name, $destination_path)
    {
        $disk = config('starmoozie.base.root_disk_name');

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            return null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {
            // 0. Buat Image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate filename.
            $filename = md5($value.time()).'.jpg';

            // 2. Store gambar di disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            // 3. Delete gambar sebelumnya jika ada.
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // 4. set image destination
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            return $public_destination_path.'/'.$filename;
        }
    }
}
