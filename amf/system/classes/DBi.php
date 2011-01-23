<?php
	class DBi
	{
		public $dsn;
		public $dbh;
		public $database = DB_NAME;
		public $host = DB_HOST;
		public $username = DB_USER;
		public $password = DB_PASS;
	   
		public function __construct()
		{    
			$this->dsn = 'mysql:host='.$this->host.';dbname='.$this->database;
			$this->connect();  
		}
	   
		public function connect()
		{
			try
			{
				$this->dbh = new PDO($this->dsn, $this->username, $this->password);
				$this->dbh->setAttribute(PDO::ATTR_PERSISTENT, TRUE);
				$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e) { return $e->getMessage(); }
		}
		
		public function prepare($query) { return $this->dbh->prepare($query); }
	}
?>
