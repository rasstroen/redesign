<?php
namespace Application\Component\Image;

use Application\Component\Base;

class Converter extends Base
{
	public function resize($orig_file_path, $settings, $target_file_path, $size)
	{
		putenv("MAGICK_THREAD_LIMIT=1");
		$quality = 95;

		$crop           = $settings['crop_method'];
		$current_width  = $size[0];
		$current_height = $size[1];
		$target_width   = min($current_width, $settings['width_requested']);
		$target_height  = min($current_height, $settings['height_requested']);

		if($crop)
		{
			$x_ratio     = $target_width / $current_width;
			$y_ratio     = $target_height / $current_height;
			$ratio       = min($x_ratio, $y_ratio);
			$use_x_ratio = ($x_ratio == $ratio);
			$new_width   = $use_x_ratio ? $target_width : floor($current_width * $ratio);
			$new_height  = !$use_x_ratio ? $target_height : floor($current_height * $ratio);
		}
		else
		{
			$new_width  = $target_width;
			$new_height = $target_height;
		}
		$im = new \Imagick($orig_file_path);
		$pi = (pathinfo($target_file_path));
		$dirname = $pi['dirname'];

		mkdir($dirname, 0777, true);
		$im->cropThumbnailImage($new_width, $new_height);
		$im->setImageCompression(\Imagick::COMPRESSION_JPEG);
		$im->setImageCompressionQuality($quality);
		$im->stripImage();
		$im->writeImage($target_file_path);
		$im->destroy();
		unset($im);

		return array($new_width, $new_height, $target_width, $target_height, $target_file_path);
	}
}