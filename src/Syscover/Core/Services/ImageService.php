<?php namespace Syscover\Core\Services;

use Intervention\Image\Image;

class ImageService
{
    public static function checkOrientation(Image $image)
    {
        $exif = $image->exif();
        $mime = $image->mime();

        info($mime);
        if ($mime == 'image/jpeg' && ($exif['Orientation'] ?? false))
        {
            if (! empty($exif['Orientation']))
            {
                $rotate = false;
                switch($exif['Orientation'])
                {
                    case 8:
                        $image->rotate(90);
                        $rotate = true;
                        break;
                    case 3:
                        $image->rotate(180);
                        $rotate = true;
                        break;
                    case 6:
                        $image->rotate(-90);
                        $rotate = true;
                        break;
                }

                if ($rotate)
                {
                    $image->save();
                }
            }
        }

        return $image;
    }
}
