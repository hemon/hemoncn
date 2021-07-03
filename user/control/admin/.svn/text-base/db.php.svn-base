<?php

class control extends adminbase {

	var $startrow = 0;
	var $sizelimit = 0;
	var $complete = TRUE;

	function control() {
		$this->adminbase();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadmindb']) {
			$this->message('no_permission_for_this_module');
		}
		$this->check_priv();
	}

	function onls() {
		$status = 0;
		if($delete = $_POST['delete']) {
			if(is_array($delete)) {
				foreach($delete AS $filename) {
					@unlink('./data/backup/'.str_replace(array('/', '\\'), '', $filename));
				}
			}
			$status = 2;
			$this->writelog('db_delete', "delete=".implode(',', $_POST['delete']));
		}

		$volumelist = array();
		if(is_dir(UC_ROOT.'./data/backup/')) {
			$dir = dir(UC_ROOT.'./data/backup/');
			while($entry = $dir->read()) {
				$file = './data/backup/'.$entry;
				if(is_file($file) && preg_match("/\.sql$/i", $file)) {
					$filesize = filesize($file);
					$fp = fopen($file, 'rb');
					$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
					fclose ($fp);
					$volumelist[] = array(
						'dateline' => $this->date($identify[0]),
						'version' => $identify[1],
						'volume' => $identify[2],
						'filename' => $entry,
						'size' => $this->sizecount($filesize)
					);
				}
			}
			$dir->close();
		} else {
			cpmsg('db_export_dest_invalid');
		}

		$filename = gmdate('ymd', $this->time).'_'.$this->random(4);
		$this->view->assign('status', $status);
		$this->view->assign('filename', $filename);
		$this->view->assign('volumelist', $volumelist);
		$this->view->display('admin_db');
	}

	function onexport() {
		$filename = getgpc('filename', 'R');
		$this->sizelimit = getgpc('sizelimit', 'R');
		$tableid = intval(getgpc('tableid'));
		$startfrom = intval(getgpc('startfrom'));
		$volume = intval(getgpc('volume')) + 1;

		$tables = $table = array();
		$sqldump = '';

		$bakfile = './data/backup/'.str_replace(array('/', '\\', '.'), '', $filename);
		$time = $this->date($this->time);
		$idstring = '# Identify: '.base64_encode($this->time.",".UC_VERSION.",$volume")."\n";

		if(!$filename || preg_match("/(\.)(exe|jsp|asp|aspx|cgi|fcgi|pl)(\.|$)/i", $filename)) {
			$this->message('db_export_filename_invalid');
		}

		$this->db->query('SET SQL_QUOTE_SHOW_CREATE=0', 'SILENT');

		$query = $this->db->query("SHOW TABLE STATUS LIKE '".UC_DBTABLEPRE."%'");
		while($table = $this->db->fetch_array($query)) {
			$tables[] = $table['Name'];
		}

		for(; $this->complete && $tableid < count($tables) && strlen($sqldump) + 500 < $this->sizelimit * 1000; $tableid++) {
			$sqldump .= $this->sqldumptable($tables[$tableid], $startfrom, strlen($sqldump));
			if($this->complete) {
				$startfrom = 0;
			}
		}

		$dumpfile = $bakfile."-%s".'.sql';
		!$this->complete && $tableid--;
		if(trim($sqldump)) {
			$sqldump = "$idstring".
				"# <?exit();?>\n".
				"# UCenter Multi-Volume Data Dump Vol.$volume\n".
				"# Version: UCenter ".UC_VERSION."\n".
				"# Time: $time\n".
				"# Table Prefix: ".UC_DBTABLEPRE."\n".
				"#\n".
				"# Discuz! Home: http://www.discuz.com\n".
				"# Please visit our website for newest infomation about Discuz!\n".
				"# --------------------------------------------------------\n\n\n".
				$sqldump;
			$dumpfilename = sprintf($dumpfile, $volume);
			@$fp = fopen($dumpfilename, 'wb');
			@flock($fp, 2);
			if(@!fwrite($fp, $sqldump)) {
				@fclose($fp);
				$this->message('db_export_file_invalid');
			} else {
				fclose($fp);
				unset($sqldump);
				$this->message('db_export_multivol_redirect', "admin.php?m=db&a=export&filename=".rawurlencode($filename)."&sizelimit=".rawurlencode($this->sizelimit)."&volume=".rawurlencode($volume)."&tableid=".rawurlencode($tableid)."&startfrom=".rawurlencode($this->startrow), 0, array('$volume' => $volume));
			}
		} else {
			$volume--;
			$filelist = '';

			for($i = 1; $i <= $volume; $i++) {
				$filename = sprintf($dumpfile, $i);
				$filelist .= "<a href=\"$filename\">$filename<br />";
			}
			$this->writelog('db_export', $bakfile.'*.sql');
			$this->message('db_export_multivol_succeed', '', 0, array('$volume' => $volume, '$filelist' => $filelist));
		}
	}

	function onimport() {

		$file = getgpc('file');
		$datafile = UC_ROOT.'data/backup/'.$file;

		if(@$fp = fopen($datafile, 'rb')) {
			$sqldump = fgets($fp, 256);
			$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", $sqldump)));
			$volume = intval($identify[2]);
			$sqldump .= fread($fp, filesize($datafile));
			fclose($fp);
		} else {
			if(getgpc('autoimport')) {
				$this->load('cache');
				$_ENV['cache']->updatedata();
				$this->writelog('db_import', './data/backup/'.preg_replace("/-\d+(\..+)$/", "*\\1", $file));
				$this->message('db_import_multivol_succeed');
			} else {
				$this->message('db_import_file_illegal');
			}
		}

		$sqlquery = $this->splitsql($sqldump);
		unset($sqldump);

		foreach($sqlquery AS $sql) {
			$sql = $this->syntablestruct(trim($sql), $this->db->version() > '4.1', UC_DBCHARSET);
			if($sql != '') {
				$this->db->query($sql, 'SILENT');
				if(($sqlerror = $this->db->error()) && $this->db->errno() != 1062) {
					$this->db->halt('MySQL Query Error', $sql);
				}
			}
		}

		$file_next = preg_replace("/-($volume)(\..+)$/", "-".($volume + 1)."\\2", $file);
		$this->message('db_import_multivol_prompt', "admin.php?m=db&a=import&file=$file_next&autoimport=yes", 0, array('$volume' => $volume));

	}

	function random($length, $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	function sqldumptable($table, $startfrom = 0, $currsize = 0) {
		$offset = 300;
		$tabledump = '';
		$usehex = TRUE;
		$tablefields = array();

		$query = $this->db->query("SHOW FULL COLUMNS FROM $table", 'SILENT');
		if(!$query && $this->db->errno() == 1146) {
			return;
		} elseif(!$query) {
			$usehex = FALSE;
		} else {
			while($fieldrow = $this->db->fetch_array($query)) {
				$tablefields[] = $fieldrow;
			}
		}
		if(!$startfrom) {
			$createtable = $this->db->query("SHOW CREATE TABLE $table", 'SILENT');
			if(!$this->db->error()) {
				$tabledump = "DROP TABLE IF EXISTS $table;\n";
			} else {
				return '';
			}
			$create = $this->db->fetch_row($createtable);
			$tabledump .= $create[1];

			$tablestatus = $this->db->fetch_first("SHOW TABLE STATUS LIKE '$table'");
			$tabledump .= ($tablestatus['Auto_increment'] && strpos($create[1], 'AUTO_INCREMENT') === FALSE ? " AUTO_INCREMENT=$tablestatus[Auto_increment]" : '').";\n\n";
		}

		$tabledumped = 0;
		$numrows = $offset;
		$firstfield = $tablefields[0];

		while($currsize + strlen($tabledump) + 500 < $this->sizelimit * 1000 && $numrows == $offset) {
			if($firstfield['Extra'] == 'auto_increment') {
				$selectsql = "SELECT * FROM $table WHERE $firstfield[Field] > $startfrom LIMIT $offset";
			} else {
				$selectsql = "SELECT * FROM $table LIMIT $startfrom, $offset";
			}
			$tabledumped = 1;
			$rows = $this->db->query($selectsql);
			$numfields = $this->db->num_fields($rows);

			$numrows = $this->db->num_rows($rows);
			while($row = $this->db->fetch_row($rows)) {
				$comma = $t = '';
				for($i = 0; $i < $numfields; $i++) {
					$t .= $comma.($usehex && !empty($row[$i]) && (strpos($tablefields[$i]['Type'], 'char') !== FALSE || strpos($tablefields[$i]['Type'], 'text') !== FALSE) ? '0x'.bin2hex($row[$i]) : '\''.mysql_escape_string($row[$i]).'\'');
					$comma = ',';
				}
				if(strlen($t) + $currsize + strlen($tabledump) + 500 < $this->sizelimit * 1000) {
					if($firstfield['Extra'] == 'auto_increment') {
						$startfrom = $row[0];
					} else {
						$startfrom++;
					}
					$tabledump .= "INSERT INTO $table VALUES ($t);\n";
				} else {
					$this->complete = FALSE;
					break 2;
				}
			}
		}

		$this->startrow = $startfrom;
		$tabledump .= "\n";

		return $tabledump;
	}

	function splitsql($sql) {
		$sql = str_replace("\r", "\n", $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= $query[0] == "#" ? NULL : $query;
			}
			$num++;
		}
		return($ret);
	}

	function syntablestruct($sql, $version, $dbcharset) {

		if(strpos(trim(substr($sql, 0, 18)), 'CREATE TABLE') === FALSE) {
			return $sql;
		}

		$sqlversion = strpos($sql, 'ENGINE=') === FALSE ? FALSE : TRUE;

		if($sqlversion === $version) {

			return $sqlversion && $dbcharset ? preg_replace(array('/ character set \w+/i', '/ collate \w+/i', "/DEFAULT CHARSET=\w+/is"), array('', '', "DEFAULT CHARSET=$dbcharset"), $sql) : $sql;
		}

		if($version) {
			return preg_replace(array('/TYPE=HEAP/i', '/TYPE=(\w+)/is'), array("ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"), $sql);

		} else {
			return preg_replace(array('/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'), array('', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'), $sql);
		}
	}

	function sizecount($filesize) {
		if($filesize >= 1073741824) {
			$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
		} elseif($filesize >= 1048576) {
			$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
		} elseif($filesize >= 1024) {
			$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
		} else {
			$filesize = $filesize . ' Bytes';
		}
		return $filesize;
	}

}

?>