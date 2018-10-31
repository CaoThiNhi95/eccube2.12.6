<?php

class ImageImagick extends SC_Plugin_Base
{
    function install($arrPlugin)
    {
        $plugin_code = $arrPlugin['plugin_code'];

        // ロゴ画像のコピー
        $src_logo_path = PLUGIN_UPLOAD_REALDIR . $plugin_code . '/logo.png';
        $dest_logo_path = PLUGIN_HTML_REALDIR . $plugin_code . '/logo.png';
        copy($src_logo_path, $dest_logo_path);
    }

    function uninstall($arrPlugin)
    {
    }

    function enable($arrPlugin)
    {
        if (!extension_loaded('imagick')) {
            return 'ImageMagickモジュール(PHP拡張モジュール)がインストールされてないため、プラグインを有効にできません。';
        }
        if (!extension_loaded('exif')) {
            return 'Exifモジュール(PHP拡張モジュール)がインストールされてないため、プラグインを有効にできません。';
        }
    }

    function disable($arrPlugin)
    {
    }

    function loadClassFileChange(&$classname, &$classpath)
    {
        $plugin_code = $this->arrSelfInfo['plugin_code'];

        if($classname == 'SC_UploadFile_Ex') {
            $classpath = PLUGIN_UPLOAD_REALDIR
                . "{$plugin_code}/SC_UploadFileImagick.php";
            $classname = 'SC_UploadFileImagick';
        }
    }

    function preProcess(LC_Page_Ex $objPage)
    {
        if (get_class($objPage) != 'LC_Page_ResizeImage_Ex') {
            return;
        }


        $objFormParam = new SC_FormParam_Ex();
        $objPage->lfInitParam($objFormParam);
        $objFormParam->setParam($_GET);
        $arrForm  = $objFormParam->getHashArray();

        $file = NO_IMAGE_REALFILE;

        // NO_IMAGE_REALFILE以外のファイル名が渡された場合、ファイル名のチェックを行う
        if (strlen($arrForm['image']) >= 1
            && $arrForm['image'] !== NO_IMAGE_REALFILE) {

            // ファイル名が正しく、ファイルが存在する場合だけ、$fileを設定
            if (!$objPage->lfCheckFileName()) {
                GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php image=' . $arrForm['image']);
            } elseif (file_exists(IMAGE_SAVE_REALDIR . $arrForm['image'])) {
                $file = IMAGE_SAVE_REALDIR . $arrForm['image'];
            }
        }

        $this->lfOutputImage($file, $arrForm['width'], $arrForm['height']);
    }

    function lfOutputImage($file, $width, $height)
    {
        $compression_quality = $this->arrSelfInfo['free_field1'];

        $arrImageInfo = getimagesize($file);
        $mime_type = $arrImageInfo['mime'];
        $src_width = $arrImageInfo[0];
        $src_height = $arrImageInfo[1];

        $objImage = new Imagick($file);
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

        header("Content-type: {$mime_type}");
        echo $objImage;
        $objImage->destroy();

        exit;
    }
}

