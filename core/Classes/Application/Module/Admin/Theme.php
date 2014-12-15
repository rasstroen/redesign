<?php

namespace Application\Module\Admin;

use Application\Module\Base;

class Theme extends Base
{
	public function actionListAdmin()
	{
		$data = array();
		$data['themes'] = $this->application->bll->theme->getAll();
		return $data;
	}
}
