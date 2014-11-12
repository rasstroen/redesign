<?php
namespace Application\Component\Database;
/**
 * Class Base
 * @package Core\Database
 *
 * @property  PDODatabase $web
 * @property  PDODatabase $master
 */
class Base extends \Application\Component\Base
{
	private $connections;

	public function __get($baseName)
	{
		return isset($this->connections[$baseName]) ? $this->connections[$baseName] : $this->connect($baseName);
	}

	private function connect($baseName)
	{
		$dbConfig       = $this->application->configuration->getDbConfiguration($baseName);
		return $this->connections[$baseName] = new PDODatabase(new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']));
	}

	public function reconnectAll()
	{
		foreach($this->connections as $baseName => $pdoDatabase)
		{
			$dbConfig       = $this->application->configuration->getDbConfiguration($baseName);
			$this->connections[$baseName] = new PDODatabase(new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']));
		}
	}
}