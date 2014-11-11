<?php
namespace Application\Component\Image;
use Application\Component\Base;

class Converter extends Base
{
	public function resize($orig_file_path, $settings, $target_file_path)
	{
		$quality = 95;

		$crop = $settings['crop_method'];
		$current_width = $settings['width'];
		$current_height = $settings['height'];
		$target_width = min($current_width, $settings['width_requested']);
		$target_height = min($current_height, $settings['height_requested']);

		if ($crop)
		{
			$x_ratio = $target_width / $current_width;
			$y_ratio = $target_height / $current_height;
			$ratio = min($x_ratio, $y_ratio);
			$use_x_ratio = ($x_ratio == $ratio);
			$new_width = $use_x_ratio ? $target_width : floor($current_width * $ratio);
			$new_height = !$use_x_ratio ? $target_height : floor($current_height * $ratio);
		}
		else
		{
			$new_width = $target_width;
			$new_height = $target_height;
		}

		$im = new \Imagick($orig_file_path);

		$im->cropThumbnailImage($new_width, $new_height);
		$im->setImageCompression(\Imagick::COMPRESSION_JPEG);
		$im->setImageCompressionQuality($quality);
		$im->stripImage();
		$im->writeImage($target_file_path);
		$im->destroy();
		return array($new_width, $new_height, $target_width, $target_height);
	}
}