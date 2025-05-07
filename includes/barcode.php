<?php

use Com\Tecnick\Barcode\Barcode as Barcode_Barcode;

include_once EZ2_3RD_PLUGIN_DIR . 'composer/vendor/autoload.php';

final class Ez2_3rd_Barcode
{
    private $data;

    private $width;

    private $height;

    private $padding;

    private $barcode_obj;

    private $qr_error_correct;

    /* DO NOT CHANGE */
    private $valid_qr_error_correct = ['L', 'M', 'Q', 'H']; // L 7% M 15% Q 25% H 30%
    /* DO NOT CHANGE */

    public function __construct()
    {
        if (!defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', (fileperms(ABSPATH . 'index.php') & 0777 | 0644));
        }

        $this->clear();
    }

    public function clear(): Ez2_3rd_Barcode
    {
        $this->data = '';
        $this->width = -4;
        $this->height = -4;
        $this->padding = [-4, -4, -4, -4];
        $this->qr_error_correct = 'M';

        return $this;
    }

    public function set_data($data): Ez2_3rd_Barcode
    {
        $this->data = (string) $data;

        return $this;
    }

    public function set_width($width): Ez2_3rd_Barcode
    {
        $this->width = (int) $width;
        $this->width = $this->make_negative($this->width);

        return $this;
    }

    public function set_height($height): Ez2_3rd_Barcode
    {
        $this->height = (int) $height;
        $this->height = $this->make_negative($this->height);

        return $this;
    }

    public function set_padding($padding): Ez2_3rd_Barcode
    {
        if (!is_array($padding)) {
            $padding = (int) $padding;
            $padding = array_fill(0, 4, $padding);
        }
        $this->padding = array_map([$this, 'make_negative'], $padding);

        return $this;
    }

    public function set_qr_error_correct($error_correct): Ez2_3rd_Barcode
    {
        if (in_array($error_correct, $this->valid_qr_error_correct)) {
            $this->qr_error_correct = $error_correct;
        }

        return $this;
    }

    private function make_negative($number): int
    {
        return 0 - abs($number);
    }

    public function build($type): void
    {
        if ($type == 'QRCODE') {
            $type .= ',' . $this->qr_error_correct;
        }

        $this->barcode_obj = new Barcode_Barcode();
        $this->barcode_obj = $this->barcode_obj->getBarcodeObj($type, $this->data, $this->width, $this->height, '#000', $this->padding)
            ->setBackgroundColor('#fff');
    }

    public function save_file($file_type, $file_path): bool
    {
        if (file_exists($file_path)) {
            return false;
        }

        switch ($file_type) {
            case 'svg':
                $svg = $this->barcode_obj->getSvgCode();
                if (!empty($svg)) {
                    $svg = str_replace(["\t", "\n"], '', $svg);
                    $svg = str_replace(' />', '/>', $svg);
                    $svg = preg_replace_callback('/\.([0-9]*)(0*)( |")/U', function ($matches) {
                        if ($matches[1] == '') {
                            return $matches[3];
                        }
                        return '.' . $matches[1] . $matches[3];
                    }, $svg);
                    $svg = str_replace('version="1"', 'version="1.0"', $svg);

                    @file_put_contents($file_path, $svg);
                }
                break;
            case 'png':
            default:
                $img = $this->barcode_obj->getGd();
                @imagepng($img, $file_path);
                break;
        }

        if (is_file($file_path)) {
            @chmod($file_path, FS_CHMOD_FILE); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
            return true;
        }

        return false;
    }
}
