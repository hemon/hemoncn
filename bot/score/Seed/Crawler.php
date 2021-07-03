<?php

class Seed_Crawler {
	private $table  = 'bot_seed';
	public  $tasks  = array();
	private $sessid = '';
	public  $limit  = 5;
	
	public function __construct(){
		$this->login();
	}
	
	public function loop(){
		while(1){
			$this->task()->run()->save();
		}
	}

	private function init(){
		$sql = "
            INSERT INTO bot_seed(url)
			SELECT CONCAT('http://jwc.ecjtu.jx.cn/mis/query.php?Term=". TERM ."&StuID=',sid)
			FROM (
				SELECT sid,LEFT(cid,12) cid 
				FROM edu_student
                ORDER BY RAND()
            ) r 
			GROUP BY cid;";

        $GLOBALS['db']->exec($sql) or die($GLOBALS['db']->errorInfo());
    }
	
	private function task(){
		$sql = "SELECT  * 
				  FROM  {$this->table} 
				 WHERE  state = '0'
		      ORDER BY  priority, updated, RAND()
				 LIMIT  {$this->limit}";
				
		$sth = $GLOBALS['db']->prepare($sql);
		$sth->execute();
		$this->tasks = $sth->fetchAll();
		
		// if empty task ,should be rebuild seed url
		if( empty($this->tasks) ){
		  $this->init();
		  exit;
		}
		
		return $this;
	}
	
	private function run(){
		if( !empty($this->tasks) ) while(list($i, $task) = each($this->tasks)){
			$url = $task['url'] . '&PHPSESSID=' . $this->sessid;
			$res = Curl::exec($url);
			$state = $this->validate($res);
			switch ($state){
				case 0:
					$this->tasks[$i]['source']  = $res;
					$this->tasks[$i]['updated'] = date('Y-m-d H:i:s');
					$this->tasks[$i]['state']   = 1;
					break;
				case 1:
					$this->login();
				case 2:
					prev($this->tasks);
					continue 2;
			}
		}
		return $this;
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
	    $html  = Curl::exec($login, array(CURLOPT_HEADER=>1));
	    preg_match('/PHPSESSID=(\w+)/', $html, $matches);
	    $this->sessid = $matches[1];
	}

	private function validate($res){
		switch (1) {
			case (false !== strpos($res, 'login'))    : return 1;
			case (false !== strpos($res, 'failured')) : return 2;
			case (false !== strpos($res, 'Not'))      : return 2;
			case empty($res) : return 2;
			default : return 0;
		}
	}

	private function parseUrl($url){
		if( preg_match('|ID=(\d+)|i', $url, $matches) ){
		    $id = $matches[1];
		    switch ( strlen($id) ){
		        case 12: 
                    return $id;
		        case 14: 
		            $sth = $GLOBALS['db']->prepare("SELECT cid FROM edu_student WHERE sid = '$id'");
                    $sth->execute();
                    if( $cid = $sth->fetchColumn() ){
                        return substr($cid,0,12);
                    }
            }
            return false;
        }
        return true;
	}

}
