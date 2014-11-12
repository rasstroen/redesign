<?php
namespace Application\Command\Tasks\Worker;

class Author extends Base
{
	public function methodUpdateInfo(array $worker)
	{
		print_r($worker);
	}
}
