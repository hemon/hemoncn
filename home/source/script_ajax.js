/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: script_ajax.js 6703 2008-03-24 02:37:52Z liguode $
*/

var Ajaxs = new Array();
var AjaxStacks = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
var ajaxpostHandle = 0;
var evalscripts = new Array();
var ajaxpostresult = 0;

function Ajax(recvType, waitId) {

	for(var stackId = 0; stackId < AjaxStacks.length && AjaxStacks[stackId] != 0; stackId++);
	AjaxStacks[stackId] = 1;

	var aj = new Object();

	aj.loading = 'Loading...';//public
	aj.recvType = recvType ? recvType : 'XML';//public
	aj.waitId = waitId ? $(waitId) : null;//public

	aj.resultHandle = null;//private
	aj.sendString = '';//private
	aj.targetUrl = '';//private
	aj.stackId = 0;
	aj.stackId = stackId;

	aj.setLoading = function(loading) {
		if(typeof loading !== 'undefined' && loading !== null) aj.loading = loading;
	}

	aj.setRecvType = function(recvtype) {
		aj.recvType = recvtype;
	}

	aj.setWaitId = function(waitid) {
		aj.waitId = typeof waitid == 'object' ? waitid : $(waitid);
	}

	aj.createXMLHttpRequest = function() {
		var request = false;
		if(window.XMLHttpRequest) {
			request = new XMLHttpRequest();
			if(request.overrideMimeType) {
				request.overrideMimeType('text/xml');
			}
		} else if(window.ActiveXObject) {
			var versions = ['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.7.0', 'Msxml2.XMLHTTP.6.0', 'Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
			for(var i=0; i<versions.length; i++) {
				try {
					request = new ActiveXObject(versions[i]);
					if(request) {
						return request;
					}
				} catch(e) {}
			}
		}
		return request;
	}

	aj.XMLHttpRequest = aj.createXMLHttpRequest();
	aj.showLoading = function() {
		if(aj.waitId && (aj.XMLHttpRequest.readyState != 4 || aj.XMLHttpRequest.status != 200)) {
			changedisplay(aj.waitId, '');
			aj.waitId.innerHTML = '<span><img src="image/loading.gif"> ' + aj.loading + '</span>';
		}
	}

	aj.processHandle = function() {
		if(aj.XMLHttpRequest.readyState == 4 && aj.XMLHttpRequest.status == 200) {
			for(k in Ajaxs) {
				if(Ajaxs[k] == aj.targetUrl) {
					Ajaxs[k] = null;
				}
			}
			if(aj.waitId) changedisplay(aj.waitId, 'none');
			if(aj.recvType == 'HTML') {
				aj.resultHandle(aj.XMLHttpRequest.responseText, aj);
			} else if(aj.recvType == 'XML') {
				aj.resultHandle(aj.XMLHttpRequest.responseXML.lastChild.firstChild.nodeValue, aj);
			}
			AjaxStacks[aj.stackId] = 0;
		}
	}

	aj.get = function(targetUrl, resultHandle) {	
		if(targetUrl.indexOf('?') != -1) {
			targetUrl = targetUrl + '&inajax=1';
		} else {
			targetUrl = targetUrl + '?inajax=1';
		}
		setTimeout(function(){aj.showLoading()}, 500);
		if(in_array(targetUrl, Ajaxs)) {
			return false;
		} else {
			Ajaxs.push(targetUrl);
		}
		aj.targetUrl = targetUrl;
		aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
		aj.resultHandle = resultHandle;
		var delay = 100;
		if(window.XMLHttpRequest) {
			setTimeout(function(){
			aj.XMLHttpRequest.open('GET', aj.targetUrl);
			aj.XMLHttpRequest.send(null);}, delay);
		} else {
			setTimeout(function(){
			aj.XMLHttpRequest.open("GET", targetUrl, true);
			aj.XMLHttpRequest.send();}, delay);
		}

	}
	aj.post = function(targetUrl, sendString, resultHandle) {
		if(targetUrl.indexOf('?') != -1) {
			targetUrl = targetUrl + '&inajax=1';
		} else {
			targetUrl = targetUrl + '?inajax=1';
		}
		setTimeout(function(){aj.showLoading()}, 500);
		if(in_array(targetUrl, Ajaxs)) {
			return false;
		} else {
			Ajaxs.push(targetUrl);
		}
		aj.targetUrl = targetUrl;
		aj.sendString = sendString;
		aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
		aj.resultHandle = resultHandle;
		aj.XMLHttpRequest.open('POST', targetUrl);
		aj.XMLHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		aj.XMLHttpRequest.send(aj.sendString);
	}
	return aj;
}

function newfunction(func){
	var args = new Array();
	for(var i=1; i<arguments.length; i++) args.push(arguments[i]);
	return function(event){
		doane(event);
		window[func].apply(window, args);
		return false;
	}
}

function changedisplay(obj, display) {
	if(display == 'auto') {
		obj.style.display = obj.style.display == '' ? 'none' : '';
	} else {
		obj.style.display = display;
	}
	return false;
}

function evalscript(s) {
	if(s.indexOf('<script') == -1) return s;
	var p = /<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:charset=\"([\w\-]+?)\")?><\/script>/ig;
	var arr = new Array();
	while(arr = p.exec(s)) appendscript(arr[1], '', arr[2], arr[3]);
	p = /<script (?!src)[^\>]*?( reload=\"1\")?>([^\x00]+?)<\/script>/ig;
	while(arr = p.exec(s)) appendscript('', arr[2], arr[1]);
	return s;
}

function appendscript(src, text, reload, charset) {
	var id = hash(src + text);
	if(!reload && in_array(id, evalscripts)) return;
	if(reload && $(id)) {
		$(id).parentNode.removeChild($(id));
	}

	evalscripts.push(id);
	var scriptNode = document.createElement("script");
	scriptNode.type = "text/javascript";
	scriptNode.id = id;
	scriptNode.charset = charset;
	try {
		if(src) {
			scriptNode.src = src;
		} else if(text){
			scriptNode.text = text;
		}
		$('append_parent').appendChild(scriptNode);
	} catch(e) {}
}

function stripscript(s) {
	return s.replace(/<script.*?>.*?<\/script>/ig, '');
}

function ajaxupdateevents(obj, tagName) {
	tagName = tagName ? tagName : 'A';
	var objs = obj.getElementsByTagName(tagName);
	for(k in objs) {
		var o = objs[k];
		ajaxupdateevent(o);
	}
}

function ajaxupdateevent(o) {
	if(typeof o == 'object' && o.getAttribute) {
		if(o.getAttribute('ajaxtarget')) {
			if(!o.id) o.id = Math.random();
			var ajaxevent = o.getAttribute('ajaxevent') ? o.getAttribute('ajaxevent') : 'click';
			var ajaxurl = o.getAttribute('ajaxurl') ? o.getAttribute('ajaxurl') : o.href;
			_attachEvent(o, ajaxevent, newfunction('ajaxget', ajaxurl, o.getAttribute('ajaxtarget'), o.getAttribute('ajaxwaitid'), o.getAttribute('ajaxloading'), o.getAttribute('ajaxdisplay')));
			if(o.getAttribute('ajaxfunc')) {
				o.getAttribute('ajaxfunc').match(/(\w+)\((.+?)\)/);
				_attachEvent(o, ajaxevent, newfunction(RegExp.$1, RegExp.$2));
			}
		}
	}
}

function ajaxget(url, showid, waitid, loading, display, recall) {
	waitid = typeof waitid == 'undefined' || waitid === null ? showid : waitid;
	var x = new Ajax();
	x.setLoading(loading);
	x.setWaitId(waitid);
	x.display = typeof display == 'undefined' || display == null ? '' : display;
	x.showId = $(showid);
	if(x.showId) x.showId.orgdisplay = typeof x.showId.orgdisplay === 'undefined' ? x.showId.style.display : x.showId.orgdisplay;

	if(url.substr(strlen(url) - 1) == '#') {
		url = url.substr(0, strlen(url) - 1);
		x.autogoto = 1;
	}

	var url = url + '&inajax=1&ajaxtarget=' + showid;
	x.get(url, function(s, x) {
		evaled = false;
		if(s.indexOf('ajaxerror') != -1) {
			evalscript(s);
			evaled = true;
		}
		if(!evaled && (typeof ajaxerror == 'undefined' || !ajaxerror)) {
			if(x.showId) {
				changedisplay(x.showId, x.showId.orgdisplay);
				changedisplay(x.showId, x.display);
				x.showId.orgdisplay = x.showId.style.display;
				ajaxinnerhtml(x.showId, s);
				ajaxupdateevents(x.showId);
				if(x.autogoto) scroll(0, x.showId.offsetTop);
			}
		}
		if(!evaled)evalscript(s);
		ajaxerror = null;
		if(recall) {eval(recall);}
	});
}

function ajaxpost(formid, showid, func) {
	showloading();

	if(ajaxpostHandle != 0) {
		return false;
	}
	var ajaxframeid = 'ajaxframe';
	var ajaxframe = $(ajaxframeid);
	if(ajaxframe == null) {
		if (is_ie && !is_opera) {
			ajaxframe = document.createElement("<iframe name='" + ajaxframeid + "' id='" + ajaxframeid + "'></iframe>");
		} else {
			ajaxframe = document.createElement("iframe");
			ajaxframe.name = ajaxframeid;
			ajaxframe.id = ajaxframeid;
		}
		//ajaxframe.id = ajaxframeid;
		ajaxframe.style.display = 'none';
		$('append_parent').appendChild(ajaxframe);

	}
	$(formid).target = ajaxframeid;
	$(formid).action = $(formid).action + '&inajax=1';
	
	ajaxpostHandle = [showid, ajaxframeid, formid, $(formid).target, func];
	if(ajaxframe.attachEvent) {
		ajaxframe.detachEvent ('onload', ajaxpost_load);
		ajaxframe.attachEvent('onload', ajaxpost_load);
	} else {
		document.removeEventListener('load', ajaxpost_load, true);
		ajaxframe.addEventListener('load', ajaxpost_load, false);
	}
	$(formid).submit();
	return false;
}

function ajaxpost_load() {
	showloading('none');
	if(is_ie) {
		var s = $(ajaxpostHandle[1]).contentWindow.document.XMLDocument.text;
	} else {
		var s = $(ajaxpostHandle[1]).contentWindow.document.documentElement.firstChild.nodeValue;
	}
	evaled = false;
	if(s.indexOf('ajaxerror') != -1) {
		evalscript(s);
		evaled = true;
	}
	if(s.indexOf('a href=') != -1) {
		ajaxpostresult = 1;
	} else {
		ajaxpostresult = 0;
	}
	//function
	if(ajaxpostHandle[4]) {
		setTimeout(ajaxpostHandle[4] + '(\'' + ajaxpostHandle[0] + '\',' + ajaxpostresult + ')', 10);
	}
	if(!evaled && (typeof ajaxerror == 'undefined' || !ajaxerror)) {
		ajaxinnerhtml($(ajaxpostHandle[0]), '<div class="popupmenu_inner">' + s + '</div>');
		if(!evaled)evalscript(s);
		setMenuPosition($(ajaxpostHandle[0]).ctrlid, 0);
		jsmenu['timer'][$(ajaxpostHandle[0]).ctrlid] = setTimeout("hideMenu()", 1000);
	}
	ajaxerror = null;
	if($(ajaxpostHandle[2])) {
		$(ajaxpostHandle[2]).target = ajaxpostHandle[3];
	}

	ajaxpostHandle = 0;
}

function ajaxmenu(e, ctrlid, timeout, func, offset) {
	var box = 0;
	showloading();
	if(jsmenu['active'][0] && jsmenu['active'][0].ctrlkey == ctrlid) {
		hideMenu();
		doane(e);
		return;
	} else if(is_ie && is_ie < 7 && document.readyState.toLowerCase() != 'complete') {
		return;
	}
	cache = 0;
	divclass = 'popupmenu_popup';
	optionclass = 'popupmenu_option';
	if(isUndefined(timeout)) timeout = 3000;
	if(isUndefined(func)) func = '';
	if(isUndefined(offset)) offset = 0;
	duration = timeout > 10000 ? 3 : 0;
	executetime = duration ? 2000: timeout;
	if(offset == -1) {
		divclass = 'popupmenu_centerbox';
		box = 1;
	}
	var div = $(ctrlid + '_menu');
	if(cache && div) {
		showMenu(ctrlid, e.type == 'click', offset, duration, timeout, 0, ctrlid, 400, 1);
		if(func) setTimeout(func + '(' + ctrlid + ')', executetime);
		doane(e);
	} else {
		if(!div) {
			div = document.createElement('div');
			div.ctrlid = ctrlid;
			div.id = ctrlid + '_menu';
			div.style.display = 'none';
			div.className = divclass;
			$('append_parent').appendChild(div);
		}

		var x = new Ajax();
		var href = !isUndefined($(ctrlid).href) ? $(ctrlid).href : $(ctrlid).attributes['href'].value;
		x.div = div;
		x.etype = e.type;
		x.optionclass = optionclass;
		x.duration = duration;
		x.timeout = timeout;
		x.executetime = executetime;
		x.get(href + '&ajaxmenuid='+ctrlid+'_menu&popupmenu_box='+box, function(s) {
			evaled = false;
			if(s.indexOf('ajaxerror') != -1) {
				evalscript(s);
				evaled = true;
				if(!cache && duration != 3 && x.div.id) setTimeout('$("append_parent").removeChild($(\'' + x.div.id + '\'))', timeout);
			}
			if(!evaled && (typeof ajaxerror == 'undefined' || !ajaxerror)) {
				if(x.div) x.div.innerHTML = '<div class="' + x.optionclass + '">' + s + '</div>';
				showMenu(ctrlid, x.etype == 'click', offset, x.duration, x.timeout, 0, ctrlid, 400, 1);
				if(func) setTimeout(func + '("' + ctrlid + '")', x.executetime);
			}
			if(!evaled) evalscript(s);
			ajaxerror = null;
			showloading('none');
		});
		doane(e);
	}
	showloading('none');
	doane(e);
}

//得到一个定长的hash值， 依赖于 stringxor()
function hash(string, length) {
	var length = length ? length : 32;
	var start = 0;
	var i = 0;
	var result = '';
	filllen = length - string.length % length;
	for(i = 0; i < filllen; i++){
		string += "0";
	}
	while(start < string.length) {
		result = stringxor(result, string.substr(start, length));
		start += length;
	}
	return result;
}

function stringxor(s1, s2) {
	var s = '';
	var hash = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var max = Math.max(s1.length, s2.length);
	for(var i=0; i<max; i++) {
		var k = s1.charCodeAt(i) ^ s2.charCodeAt(i);
		s += hash.charAt(k % 52);
	}
	return s;
}

function showloading(display, wating) {
	var display = display ? display : 'block';
	var wating = wating ? wating : 'Loading...';
	$('ajaxwaitid').innerHTML = wating;
	$('ajaxwaitid').style.display = display;
}

function ajaxinnerhtml(showid, s) {
	if(showid.tagName != 'TBODY') {
		showid.innerHTML = s;
	} else {
		while(showid.firstChild) {
			showid.firstChild.parentNode.removeChild(showid.firstChild);
		}
		var div1 = document.createElement('DIV');
		div1.id = showid.id+'_div';
		div1.innerHTML = '<table><tbody id="'+showid.id+'_tbody">'+s+'</tbody></table>';
		$('append_parent').appendChild(div1);
		var trs = div1.getElementsByTagName('TR');
		var l = trs.length;
		for(var i=0; i<l; i++) {
			showid.appendChild(trs[0]);
		}
		var inputs = div1.getElementsByTagName('INPUT');
		var l = inputs.length;
		for(var i=0; i<l; i++) {
			showid.appendChild(inputs[0]);
		}		
		div1.parentNode.removeChild(div1);
	}
}