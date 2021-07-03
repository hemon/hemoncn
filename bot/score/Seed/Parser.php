<?php

class Seed_Parser {
	private $table = 'bot_seed';
	public  $tasks = array();
	public  $urls  = array();
	public  $limit = 5;

	public function loop(){
		while(1){
			$this->task()->run()->export()->save()->reset();
		}
	}
	
	private function task(){
		$sql = "SELECT  * 
				  FROM  {$this->table}
				 WHERE  state = '1'
		      ORDER BY  priority, updated 
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
			$task['result'] = $this->parse($task['source']);
		}
		return $this;
	}

	private function export(){
		$cache = array();
		if( !empty($this->tasks) ) foreach($this->tasks as $task){
			foreach($task['result'] as $row){

				unset($row[0]);
				
				$sql = "INSERT INTO 
							edu_score (term, sid, name, course, course_require, credit_hour, score, score_1, score_2) 
						VALUES 
							('" . implode("','", array_map("trim", $row)) . "') 
						ON DUPLICATE KEY UPDATE
						    score   = VALUES(score),
							score_1 = VALUES(score_1),
							score_2 = VALUES(score_2)";
				
				if( 1 == $GLOBALS['db']->exec($sql) ){
				    $url = array(
                        'Term'    => $row[1],
                        'Course'  => $row[4],
                        'ClassID' => $this->classid($task['url'])
                    );
					$this->urls[0][] = 'http://jwc.ecjtu.jx.cn/mis/query.php?'.http_build_query($url,'','&');

                    if( '公共任选课' == trim($row['5']) ){
                        unset($url['ClassID']);
                        $this->urls[-1][] = 'http://jwc.ecjtu.jx.cn/mis/query.php?'.http_build_query($url,'','&');
                    }
				}
			}
		}
		return $this;
	}
	
	private function classid($url){
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
        }
        return false;
    }
	
	private function save(){
	    foreach($this->urls as $priority => $urls){
    		if( empty($urls) ) continue;
    		
			$urls = array_unique($urls);
			$values = "('$priority','" . implode("'),('$priority','", $urls) . "'),";
			$values = substr($values, 0, -1);
			$sql = "INSERT INTO bot_url (priority,url) VALUES $values";
			$GLOBALS['db']->exec($sql);
        }

		return $this;
	}

	private function reset(){
		$values = '';
		if( !empty($this->tasks) ) foreach($this->tasks as $task){
			$values .= "{$task['id']},";
		}
		if( !empty($values) ){
			$values = substr($values, 0, -1);
			// makeup
			$sql = "UPDATE {$this->table} 
					SET state = '2'
					WHERE id IN ($values)";
			$GLOBALS['db']->exec($sql);
			// move
			$sql = "INSERT INTO {$this->table}_history 
					SELECT * FROM {$this->table}
					WHERE state = '2'";
			$GLOBALS['db']->exec($sql);
			// remove
			$sql = "DELETE FROM {$this->table} 
                    WHERE state = '2'";
			$GLOBALS['db']->exec($sql);
		}

		$this->tasks = array();
		$this->urls  = array();
		return $this;
	}
	
	private function parse($res){
		$res = str_replace(array('<font color=red>','</font>'), "", $res);
        preg_match_all ("|<tr><td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>|", $res, $matches, PREG_SET_ORDER);
        return $matches;
	}

}

?>
