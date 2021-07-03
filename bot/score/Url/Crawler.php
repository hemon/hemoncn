<?php

class Url_Crawler {
	public  $tasks  = array();
	private $table  = 'bot_url';
	private $sessid = '';
	public  $limit  = 5;
	public  $priority = 0;
	
	public function __construct($limit = 5, $priority =  0){
		$this->login();
	}
	
	public function loop(){
		while(1){
			$this->task()->run()->save();
		}
	}
	
	private function task(){
		$sql = "SELECT  * 
				  FROM  {$this->table} 
				 WHERE  state = '0' AND priority = '{$this->priority}'
		      ORDER BY  id DESC
				 LIMIT  {$this->limit}";
				
		$sth = $GLOBALS['db']->prepare($sql);
		$sth->execute();
		$this->tasks = $sth->fetchAll();
		
		if( empty($this->tasks) ){
			exit;
		}
		
		return $this;
	}
	
	private function run(){
		if( !empty($this->tasks) ) foreach($this->tasks as &$task){
			$max = 1;
			for($start = 0; $start < $max; $start++){
				$url = "{$task['url']}&start={$start}&PHPSESSID={$this->sessid}";
    			$res = Curl::exec($url);
    			if( $start == 0 ) $max = $this->max($res);
                        
    			$state = $this->validate($res);
    			switch ($state){
    				case 0:
    					$task['source'] .= $res;
    					$task['updated'] = date('Y-m-d H:i:s');
    					$task['state']   = 1;
    					break;
    				case 1:
    					$this->login();
    				case 2:
    					$start--;
    					continue 2;
    			}
            }
		}
		return $this;
	}
	
	private function max($res){
		if( preg_match('/>(\d+)\W+<\/select>/U', $res, $matches) ){
			return $matches[1];
	    }
	    return 1;
	}
	
	private function save(){
		$values = '';
		if( !empty($this->tasks) ) foreach($this->tasks as &$task){
			$values .= "('{$task['id']}','{$task['updated']}','{$task['state']}','" . mysql_escape_string($task['source'])."'),";
		}
		if( !empty($values) ){
			$values = substr($values, 0, -1);
			$sql = "INSERT INTO 
						{$this->table} (id,updated,state,source) 
					VALUES 
						$values 
					ON DUPLICATE KEY UPDATE
						updated = VALUES(updated),
						state   = VALUES(state),
						source  = VALUES(source)";

			return $GLOBALS['db']->exec($sql);
		}
	}

	private function login(){
	    $login = 'http://jwc.ecjtu.jx.cn/mis/login.php?user=jwc&pass=jwc';
	    $html  = Curl::exec($login, array(CURLOPT_HEADER => 1));

	    preg_match('/PHPSESSID=(\w+)/', $html, $matches);
	    $this->sessid = $matches[1];
	}

	private function validate($res){
		switch (1) {
			case (false !== strpos($res, 'login'))    : return 1;
			case (false !== strpos($res, 'failured')) : return 2;
			case (false !== strpos($res, 'Not'))      : return 2;
			
			case empty($res) : return 2;
			default          : return 0;
		}
	}

}
