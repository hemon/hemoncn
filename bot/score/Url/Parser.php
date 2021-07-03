<?php

class Url_Parser {
	private $table = 'bot_url';
	public  $tasks = array();
	public  $limit = 100;

	public function loop(){
		while(1){
			$this->task()->run()->export()->reset();
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
		$values = '';
		if( !empty($this->tasks) ) foreach($this->tasks as $task){
			foreach($task['result'] as $row){
				unset($row[0]);
				$values .= "('" . implode("','", array_map("trim", $row)) . "'),";
			}
		}
		if( !empty($values) ){
			$values = substr($values, 0, -1);
			$sql = "INSERT INTO 
						edu_score (term, sid, name, course, course_require, credit_hour, score, score_1, score_2) 
					VALUES 
						$values 
					ON DUPLICATE KEY UPDATE
					    score   = VALUES(score),
						score_1 = VALUES(score_1),
						score_2 = VALUES(score_2)";

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
		return $this;
	}
	
	private function parse($res){
		$res = str_replace(array('<font color=red>','</font>'), "", $res);
        preg_match_all ("|<tr><td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>|", $res, $matches, PREG_SET_ORDER);
        return $matches;
	}

}

?>
