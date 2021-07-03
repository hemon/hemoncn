<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: setting.php 12181 2008-01-17 06:15:55Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends adminbase {

	function control() {
		$this->adminbase();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminsetting']) {
			$this->message('no_permission_for_this_module');
		}
		$this->check_priv();
	}

	function onls() {
		$this->load('user');
		$updated = false;
		if($this->submitcheck()) {
			$timeformat = getgpc('timeformat', 'P');
			$dateformat = getgpc('dateformat', 'P');
			$timeoffset = getgpc('timeoffset', 'P');
			$dateformat = str_replace(array('yyyy', 'mm', 'dd'), array('y', 'n', 'j'), strtolower($dateformat));
			$timeformat = $timeformat == 1 ? 'H:i' : 'h:i A';
			$timeoffset = in_array($timeoffset, array('-12', '-11', '-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3.5', '-3', '-2', '-1', '0', '1', '2', '3', '3.5', '4', '4.5', '5', '5.5', '5.75', '6', '6.5', '7', '8', '9', '9.5', '10', '11', '12')) ? $timeoffset : 8;

			$this->set_setting('dateformat', $dateformat);
			$this->set_setting('timeformat', $timeformat);
			$timeoffset = $timeoffset * 3600;
			$this->set_setting('timeoffset', $timeoffset);
			$updated = true;

			$this->load('cache');
			$_ENV['cache']->updatedata('settings');
		}

		//$settings = $this->get_setting(array('dateformat', 'timeformat', 'timeoffset'));
		$settings = $this->get_setting(array('doublee', 'accessemail', 'censoremail', 'censorusername', 'dateformat', 'timeoffset', 'timeformat'));
		if($updated) {
			//note 通知子应用
			$this->load('note');
			$_ENV['note']->add('updateclient', '', $this->serialize($settings));
			$_ENV['note']->send();
		}
		$settings['dateformat'] = str_replace(array('y', 'n', 'j'), array('yyyy', 'mm', 'dd'), $settings['dateformat']);
		$settings['timeformat'] = $settings['timeformat'] == 'H:i' ? 1 : 0;
		$a = getgpc('a');
		$this->view->assign('a', $a);
		
		$this->view->assign('dateformat', $settings['dateformat']);
		$timeformatchecked = array($settings['timeformat'] => 'checked="checked"');
		$this->view->assign('timeformat', $timeformatchecked);
		$timeoffset = intval($settings['timeoffset'] / 3600);
		$checkarray = array($timeoffset => 'selected="selected"');
		$this->view->assign('checkarray', $checkarray);
		$this->view->assign('updated', $updated);
		$this->view->display('admin_setting');
	}

	function onregister() {
		$updated = false;
		if($this->submitcheck()) {
			$this->set_setting('doublee', getgpc('doublee', 'P'));
			$this->set_setting('accessemail', getgpc('accessemail', 'P'));
			$this->set_setting('censoremail', getgpc('censoremail', 'P'));
			$this->set_setting('censorusername', getgpc('censorusername', 'P'));
			$updated = true;
			$this->writelog('setting_register_update');
			$this->load('cache');
			$_ENV['cache']->updatedata('settings');
		}
		$settings = $this->get_setting(array('doublee', 'accessemail', 'censoremail', 'censorusername', 'dateformat', 'timeoffset', 'timeformat'));

		//note 通知子应用
		if($updated) {
			$this->get_setting();
			$this->load('note');
			$_ENV['note']->add('updateclient', '', $this->serialize($settings));
			$_ENV['note']->send();
		}

		$this->view->assign('a', getgpc('a'));
		$doubleechecked = array($settings['doublee'] => 'checked="checked"');
		$this->view->assign('doublee', $doubleechecked);
		$this->view->assign('accessemail', $settings['accessemail']);
		$this->view->assign('censoremail', $settings['censoremail']);
		$this->view->assign('censorusername', $settings['censorusername']);
		$this->view->assign('updated', $updated);
		$this->view->display('admin_setting');
	}

}

?>