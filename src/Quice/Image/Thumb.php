<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Image;

class Thumb
{

    public function getSize($image)
    {
        $imgData = @getimagesize($image);
        if (!$imgData) {
            throw new Exception('Load Image Fail');
        }

        $data = array();
        $data['width'] = $imgData[0];
        $data['height'] = $imgData[1];
        $data['mime'] = $imgData['mime'];
        return $data;
    }

    public function load($image, $mime)
    {
        $imgLoaders = array(
            'image/jpeg' => 'imagecreatefromjpeg', 'image/pjpeg' => 'imagecreatefromjpeg',
            'image/png' => 'imagecreatefrompng', 'image/gif' => 'imagecreatefromgif',
        );
        if (!in_array($mime, array_keys($imgLoaders))) {
            throw new Exception('Image Mime Type Not Supported');
        }
        $loader = $imgLoaders[$mime];
        $source = $loader($image);
        return $source;
    }

    public function thumb($source, $thumbDest, $sourceSize, $targetSize, $scale = true, $inflate = true, $quality = 75)
    {
        // set var
        $sourceWidth = $sourceSize['width'];
        $sourceHeight = $sourceSize['height'];
        $sourceMime = $sourceSize['mime'];

        $targetWidth = $targetSize['width'];
        $targetHeight = $targetSize['height'];
        $targetMime = $targetSize['mime'];
        $cropX = 0;
        $cropY = 0;

        // cal size
        if ($targetWidth > 0) {
            $ratioWidth = $targetWidth / $sourceWidth;
        }
        if ($targetHeight > 0) {
            $ratioHeight = $targetHeight / $sourceHeight;
        }
        if ($scale) {
            if ($targetWidth && $targetHeight) {
                $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
            }
            if ($targetWidth xor $targetHeight) {
                $ratio = (isset($ratioWidth)) ? $ratioWidth : $ratioHeight;
            }
            if ((!$targetWidth && !$targetHeight) || (!$inflate && $ratio > 1)) {
                $ratio = 1;
            }

            $thumbWidth = floor($ratio * $sourceWidth);
            $thumbHeight = ceil($ratio * $sourceHeight);
            $targetHeight = $thumbHeight;
        } else {
            if ($sourceWidth > $sourceHeight) { //landscape from here new
                $thumbWidth = round($sourceWidth * $ratioHeight);
                $thumbHeight = $targetHeight;
                $cropX = ceil(($sourceWidth - $sourceHeight) / 2);
                $cropY = 0;
            } elseif ($sourceWidth < $sourceHeight) { //portrait
                $thumbHeight = round($sourceHeight * $ratioWidth);
                $thumbWidth = $targetWidth;
                $cropX = 0;
                $cropY = ceil(($sourceHeight - $sourceWidth) / 2);
            } else { //square
                $thumbWidth = $targetWidth;
                $thumbHeight = $targetHeight;
                $cropX = 0;
                $cropY = 0;
            }
        }
        if(empty($targetWidth)) {
            $targetWidth = $thumbWidth;
        }
        if(empty($targetHeight)) {
            $targetHeight = $thumbHeight;
        }

        // create image
        $thumb = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($sourceWidth == $targetWidth && $sourceHeight == $targetHeight) {
            $thumb = $source;
        } else {
            imagecopyresampled($thumb, $source, 0, 0, $cropX, $cropY,
                $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight
            );
        }

        // generate thumb
        $imgCreators = array(
            'image/jpeg' => 'imagejpeg', 'image/pjpeg' => 'imagejpeg',
            'image/png' => 'imagepng', 'image/gif' => 'imagegif',
        );
        if($targetMime !== null) {
            $creator = $imgCreators[$targetMime];
        } else {
            $creator = $imgCreators[$sourceMime];
        }

        if ($creator == 'imagejpeg') {
            imagejpeg($thumb, $thumbDest, $quality);
        } else {
            $creator($thumb, $thumbDest);
        }
    }

    public function destroy($image)
    {
        // Free up memory
        imagedestroy($image);
    }

}
