<?php
namespace Application\Command\Tasks\Worker;

use Application\Command\Search;
use Application\Console;

class Theme extends Base
{
	public function methodRecalculate()
	{
		$command = new Search($this->application, array());
		$command->actionApplyThemes();
	}
}
