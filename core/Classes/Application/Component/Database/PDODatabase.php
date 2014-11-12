<?php
namespace Application\Component\Database;
class PDODatabase{
	/**
	 * @var PDO
	 */
	public $pdo;
	function __construct(\PDO $database)
	{
		$this->pdo = $database;
		$this->query('SET NAMES utf8');
	}

	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * Возвращает массив ассоциативных массивов с результатом выборки
	 * или пустой массив
	 *
	 * @param $query
	 * @param array $parameters
	 * @param null $keyfield
	 * @return array
	 */
	public function selectAll($query, array $parameters = array(), $keyfield = null)
	{
		$out = array();
		$result = $this->query($query, $parameters);
		$i = 0;
		while ($data = $result->fetch(\PDO::FETCH_ASSOC))
		{
			$out[$keyfield ? $data[$keyfield] : $i++] = $data;
		}
		return $out;
	}

	public function selectSingle($query, array $parameters = array())
	{
		$out    = array();
		$result = $this->query($query, $parameters);
		while ($data = $result->fetch(\PDO::FETCH_ASSOC))
		{
			$out = array_shift($data);
		}
		return $out;
	}

	public function selectRow($query, array $parameters = null)
	{
		$result = $this->query($query, $parameters);
		return $result->fetch(\PDO::FETCH_ASSOC);
	}

	private function prepareQuery(&$query, array &$parameters = array())
	{
		$queryArray         = explode('?', $query);
		$resultParameters   = array();
		$resultQueryArray   = array();

		foreach($queryArray as $key => $queryPart)
		{
			if(isset($parameters[$key]) && is_array($parameters[$key]))
			{
				$resultQueryArray[]      = $queryPart;
				for($i=0; $i<count($parameters[$key]); $i++)
				{
					$resultParameters[] = $parameters[$key][$i];
				}
				$resultQueryArray[] = str_repeat('?,', count($parameters[$key])-1) . '?';
			}
			else
			{
				$resultQueryArray[] = $queryPart . ($key !== count($queryArray) - 1 ? '?' : '');
				if(isset($parameters[$key]))
				{
					$resultParameters[] = $parameters[$key];
				}
			}
		}
		$query      = implode('', $resultQueryArray);
		$parameters = $resultParameters;
	}

	public function query($query, array $parameters = array())
	{
		$this->prepareQuery($query, $parameters);
		$stmt = $this->pdo->prepare($query);
		$i  = 1;
		foreach($parameters as $parameter)
		{
			$stmt->bindValue($i++, $parameter, $parameter === (int)$parameter ? \PDO::PARAM_INT : \PDO::PARAM_STR);
		}

		if ($stmt->execute())
		{
			return $stmt;
		}
		else
		{
			$errorInfo = $stmt->errorInfo();
			throw new \Exception('Database error:' . print_r($errorInfo, true));
		}
	}
}