<?php

namespace Application\Module\Admin;

use Application\Module\Base;

class Demons extends Base
{
	public function actionShowDemons()
	{
		if($workerId = $this->application->request->getQueryParam('workerId'))
		{
			$data['worker'] = $this->application->bll->queue->getWorkerById($workerId);
		}
		$data['queues']     = $this->application->bll->queue->getAll();
		foreach($data['queues'] as $queueId => &$queue)
		{
			$queue['status']    = $this->application->bll->queue->getQueueStatus($queueId);
			$queue['workers']   = $this->application->bll->queue->getQueueWorkers($queueId);
		}
		unset ($queue);
		return $data;
	}
}
