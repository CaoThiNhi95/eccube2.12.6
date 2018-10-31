<?php

/* アップロードファイル管理クラス */
class SC_UploadFileImagick extends SC_UploadFile
{
    var $plugin_code = 'ImageImagick';

    function makeThumb($src_file, $width, $height, $dst_file)
    {
        // プラグイン情報を取得.
        $arrPluginInfo = SC_Plugin_Util_Ex::getPluginByPluginCode($this->plugin_code);
        $compression_quality = $arrPluginInfo['free_field1'];

        $extension = $this->PLG_getImageExtension($src_file);
        if (empty($extension)) {
            echo "イメージの形式が不明か、対応していない形式です。";
            exit;
        }

        $dst_file .= '.' . $extension;

        $arrImageInfo = getimagesize($src_file);
        $src_width = $arrImageInfo[0];
        $src_height = $arrImageInfo[1];

        $objImage = new Imagick($src_file);
        $objImage->setCompressionQuality($compression_quality);

        // 同じ大きさの場合は変換しない。
        // また、拡大するようなこともしない。
        if ($width < $src_width || $height < $src_height) {
            // Imagick3以降は、bestfitだと拡大される場合がある
            //$objImage->thumbnailImage($width, $height, TRUE);
            // bestfitだと、指定サイズより1ピクセル小さくなる場合があるのでヤメ

            $src_aspect_ratio = $src_width / $src_height;
            $target_aspect_ratio = $width / $height;
            if ($src_aspect_ratio === $target_aspect_ratio) {
                $objImage->thumbnailImage($width, $height);
            } elseif ($src_aspect_ratio > $target_aspect_ratio) {
                $objImage->thumbnailImage($width, 0);
            } else {
                $objImage->thumbnailImage(0, $height);
            }
        }

        $objImage->writeImage($dst_file);
        $objImage->destroy();

        return basename($dst_file);
    }

    // 画像ファイルの拡張子を調べて取得する
    function PLG_getImageExtension($file_path)
    {
        $type = exif_imagetype($file_path);

        switch ($type) {
            case IMAGETYPE_GIF:
                $extension = 'gif';
                break;
            case IMAGETYPE_JPEG:
                $extension = 'jpg';
                break;
            case IMAGETYPE_PNG:
                $extension = 'png';
                break;
            default:
                $extension = NULL;
        }

        return $extension;
    }
}

