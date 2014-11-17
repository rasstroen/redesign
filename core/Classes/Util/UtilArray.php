<?php
namespace Util;

class UtilArray
{
	public static function mergeArray(array $arrays, $isIntegerSafe = true)
	{
		$res = array_shift($arrays);
		while (!empty($arrays))
		{
			$next = array_shift($arrays);
			if(is_array($next)) {
				foreach ($next as $k => $v) {
					if (is_integer($k) && !$isIntegerSafe) {
						isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
					} elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
						$res[$k] = self::mergeArray(array($res[$k], $v), $isIntegerSafe);
					} else {
						$res[$k] = $v;
					}
				}
			}
		}
		return $res;
	}
}
