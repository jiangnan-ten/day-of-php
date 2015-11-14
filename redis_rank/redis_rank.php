<?php 

class Rank
{
	private $id;
	private $redis;
	private $init = null;
	private $people = [];

	public function __construct($people)
	{
		$id 		= 	isset($_GET['id']) ? $_GET['id'] : 0;
		$name 		=	isset($_GET['name']) ? $_GET['name'] : '';
		$init =   ( empty($id) && empty($name) ) ? true : false; 	
		$this->id 	= 	$id 	= 	empty($id) ? 1 : $id;
		$this->name = 	$name	=	empty($name) ? 'jam' : $name; 

		$this->con();
		if (!$init) 
		{
			$this->init($people);
			$this->incr($name);
		}
		
		$this->getRank();
	}

	public function con()
	{
		$redis = new Redis();
		$con = $redis->connect('127.0.0.1', 6379);
		if( $con )
			return $this->redis = $redis;
		else
			exit('reids connect failed');
	}

	public function init($people)
	{
		$people_redis = $this->getPeople();

		if( empty($people_redis) && !empty($people) )
		{
			$this->setPeople($people);
		}
	}

	public function getPeople()
	{
		return $this->redis->zRange('rank', 0, -1, true);
	}

	public function setPeople($people)
	{
		foreach($people as $v)
		{
			$res = $this->redis->zAdd('rank', 0, $v);
		}		
	}

	public function incr($name)
	{
		$this->redis->zIncrBy('rank', 1, $name);
	}

	public function getRank()
	{
		$res = $this->redis->zRevRangeByScore('rank', '+inf', '-inf', ['withscores' => true]);
		echo json_encode($res);
	}
}

$people = ['jam', 'jack', 'tom', 'linda', 'ten', 'jj', 'jackson', 'luo', 'me', 'halen'];
$rank = new Rank($people);