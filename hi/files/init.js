FF = {
	cfg: {
		textlimit: {
			message: 140,
			privatemessage: 140,
			help: 140,
			pro_bas_detail: 200,
			pro_int_misc: 200,
			pro_int_music: 200,
			pro_int_movie: 200,
			pro_int_books: 200,
			pro_int_sports: 200,
			pro_int_persons: 200
		},
		cityselector: {
			pro_bas_province: ['pro_bas_city', 'city'],
			pro_bas_homeprovince: ['pro_bas_homecity', 'homecity']
		},
		formpattern: {
			register: {
				requiredlist: 'email, realname, loginpass, verifypass', verifylist: {'verifypass': 'loginpass'}, relist: 'email, loginpass', charlist: {'realname': 12}
			},
			login: {
				requiredlist: 'loginname, loginpass'
			},
			reset: {
				requiredlist: 'email', relist: 'email'
			},
			invite: {
				requiredlist: 'sendto'
			},
			setloginname: {
				requiredlist: 'newname'
			},
			setpassword: {
				requiredlist: 'loginpass, newpass, verifypass', verifylist: {'verifypass': 'newpass'}, relist: 'loginpass, newpass, verifypass'
			},
			setrealname: {
				requiredlist: 'realname', charlist: {'realname': 12}
			},
			setemail: {
				requiredlist: 'email', relist: 'email'
			},
			setim: {
				requiredlist: 'msn', relist: 'msn'
			},
			setmobile: {
				requiredlist: 'mobile', relist: 'mobile'
			}
		},
		formtip: {
			's-status': {'ct': '输入文字查找消息'},
			's-user': {'rn': '输入昵称查找用户'},
			sharepanel: {'u': 'http://'}
		},
		formfocus: 'login, register, reset, message, help',
		re: {
			loginpass: /^[\x21-\x7e]{4,32}$/ig,
			newpass: /^[\x21-\x7e]{4,32}$/ig,
			verifypass: /^[\x21-\x7e]{4,32}$/ig,
			email: /^[\w\.\-\+]+@([\w\-]+\.)+[a-z]{2,4}$/ig,
			msn: /^[\w\.\-\+]+@([\w\-]+\.)+[a-z]{2,4}$/ig,
			mobile: /^1(3|5|8)[0-9]{9}$/
		}
	}
}

FF.Form = function(name) {
	this.form = {};
	this.url = '';
}

FF.Form.prototype = {
	mkForm:	function(method, action, map, visibility) {
		var el = document.createElement('form'),
			method = method || 'post',
			action = action || window.location.href;
		this.form = el;

		el.setAttribute('method', method);
		el.setAttribute('action', action);
		if (map) {
			for (var itm in map) {
				el.appendChild(this.getInputByMap([itm, map[itm], 'hidden']));
			}
		}
		if (!visibility) YAHOO.util.Dom.setStyle(el, 'display', 'none');
		document.body.appendChild(el);
	},
	getInputByMap: function(map) {
		var name = map.shift(),
			value = map.shift(),
			type = map.shift();
		return this.createInputEl(name, value, type);
	},
	createInputEl: function(name, value, type) {
		var el = document.createElement('input');
		if (name) el.setAttribute('name', name);
		if (value) el.setAttribute('value', value);
		el.setAttribute('type', type || 'hidden');
		return el;
	},
	appendInputElement: function(name, value, type) {
		el = this.createInputEl(name, value, type);
		if (this.form) this.form.appendChild(el);
	},
	post: function(hasAjax, fn) {
		if (hasAjax) {
			YAHOO.util.Connect.setForm(this.form);
			if (this.url == '') {
				this.url = window.location.href;
			}
			var cb = {
				success: function(o) {
					var res = o.responseText;
					var r = new FF.response(res);
					r.show();
					if (fn) {
						fn();
					}
				},
				failure: function(o) {
				},
				timeout: 5000
			}
			YAHOO.util.Connect.asyncRequest('post', this.url, cb);
		} else {
			this.form.submit();
		}
	}
}

FF.ajax_prompt = function(pos, sText) {
	this.pos = pos;
	var container = document.createElement('span');
	this.id = YAHOO.util.Dom.generateId(container);
	YAHOO.util.Dom.addClass(container, 'ajaxprompt');
	document.body.appendChild(container);
	YAHOO.util.Dom.setX(container, pos[0]+5);
	YAHOO.util.Dom.setY(container, pos[1]+5);
	var init_text = document.createTextNode(sText);
	container.appendChild(init_text);
	this.container = container;
}

FF.ajax_prompt.prototype = {
	change: function(sText) {
		this.text = sText;
		YAHOO.util.Dom.get(this.id).firstChild.nodeValue = this.text;
	},
	off: function() {
		disappear(this.id, 1.5);
	}
}

FF.sysmsg = function(text) {
	if (text) {
		this.message = text;
	} else {
		this.message = this.get();
	}
}

FF.sysmsg.prototype = {
	show: function() {
		var arr = YAHOO.util.Dom.getElementsByClassName('sysmsg');
		if (arr.length > 0) {
			function f(el) {
				el.parentNode.removeChild(el);
			}
			YAHOO.util.Dom.batch(arr, f);
		}
		var msg = document.createElement('div');
		YAHOO.util.Dom.addClass(msg, 'sysmsg');
		var text = document.createTextNode(this.message);
		msg.appendChild(text);
		var main = YAHOO.util.Dom.get('main');
		YAHOO.util.Dom.insertBefore(msg, main);
	},
	get: function() {
		var allcookies = document.cookie;
		var pos = allcookies.indexOf("sm=");
		if (pos != -1) {
			var start = pos + 3;
			var end = allcookies.indexOf(";", start)
			if (end == -1) end = allcookies.length;
			var value = allcookies.substring(start, end);
			value = value.replace(/-/g, '+').replace(/_/g, '/');
			for(var i = 3 - (value.length % 4); i > 0; i--) {
				value += '=';
			}
			value = utf8to16(BASE64_decode(unescape(value)));
			document.cookie = 'sm=;';
			return value;
		} else {
			return '';
		}
	}
}

FF.response = function(str) {
	var p = 0, q = 0;
	var status = str.slice(p, p = str.indexOf(':', p));
	if (status == 'success') {
		this.status = true;
	} else if (status == 'failure') {
		this.status = false;
	} else {
		this.status = 'unknown';
	}
	var type = str.slice(++p, q = str.indexOf(':', p + 1));
	if (this.status != 'unknown') {
		if (type == 'tip') {
			this.type = 'tip';
			this.content = str.substring(q + 1);
		} else if (type == 'msg') {
			this.type = 'msg';
			this.content = str.substring(q + 1);
		} else if (type == 'pop') {
			this.type = 'pop';
			this.content = str.substring(q + 1);
		} else {
			this.type == 'msg';
			this.content = str.substring(p + 1);
		}
	} else {
		this.type = 'msg';
		this.content = str;
	}
}

FF.response.prototype = {
	show: function() {
		switch(this.type) {
			case 'tip': break;
			case 'msg':
				var msg = new FF.sysmsg();
				if(msg.message == '') {
					msg.message = this.content;
				}
				msg.show();
				break;
			case 'pop':
				var obj = unserialize(utf16to8(this.content));
				this.createTag('div', 'poverlay', null, 'root');
				var win = this.createTag('div', 'pwindow', null, 'root');
				var title = document.createElement('h4');
				title.appendChild(document.createTextNode(obj['title']));
				win.appendChild(title);
				var content = document.createElement('div');
				YAHOO.util.Dom.addClass(content, 'ff');
				content.innerHTML = obj['content'];
				win.appendChild(content);
				var actpan = document.createElement('div');
				YAHOO.util.Dom.addClass(actpan, 'act');
//				actpan.innerHTML = obj['act'];
				win.appendChild(actpan);
				var attrs = {'type': 'button',
					'class': 'formbutton',
					'name': 'button',
					'value': '确定'};
				var closebtn = this.createTag('input', null, attrs, actpan);
				YAHOO.util.Event.on(closebtn, 'click', function(){
					win.parentNode.removeChild(win);
					disappear('poverlay', 0.3);
					window.location.href = window.location.href;
				});
				break;
			default: break;
		}
	},
	createTag: function(tagName, id, attrs, parent) {
		var tag = document.createElement(tagName);
		if (id) {
			tag.setAttribute('id', id);
		}
		if (attrs) {
			for (var itm in attrs) {
				if (itm == 'class') {
					YAHOO.util.Dom.addClass(tag, attrs[itm]);
				} else {
					tag.setAttribute(itm, attrs[itm]);
				}
			}
		}
		if (parent) {
			if (parent == 'root') {
				document.body.appendChild(tag);
			} else {
				parent.appendChild(tag);
			}
		}
		return tag;
	}
}

function post_form(el) {
	if (post_form.arguments.length == 2) {
		ev = post_form.arguments[1];
	} else {
		ev = null;
	}
	if (YAHOO.util.Dom.hasClass(el, 'as')) {
		if (ev) YAHOO.util.Event.preventDefault(ev);
		as(el);
	} else if (el.getAttribute('id') == 'login' && el['former']) {
		if (ev) YAHOO.util.Event.preventDefault(ev);
		var loginForm = new FF.Form();
		loginForm.form = el;
		loginForm.url = '/login';
		loginForm.appendInputElement('ajax', 'yes');
		var data = el['former'].value;
		function fn() {
			post_former(data);
		}
		loginForm.post(true, fn);
	} else {
		if (!ev) el.submit();
	}
}

function as(el) {
	var thisform = new FF.Form();
	thisform.form = el;
	thisform.appendInputElement('ajax', 'yes');
	thisform.post(true);
}

function post_former(str) {
	var str = BASE64_decode(str);
	var former = new FF.Form();
	var o = unserialize(str);
	former.mkForm(o['method'], o['url'], o['data']);
	if (former.form.getAttribute('method').toLowerCase() == 'get') {
		window.location.href = former.form.getAttribute('action');
	} else {
		former.post();
	}
}

function disappear(el, speed) {
	var el = YAHOO.util.Dom.get(el);
	var anim = new YAHOO.util.Anim(el, {opacity: {to: '0'}}, speed, YAHOO.util.Easing.easeInStrong);
	anim.onComplete.subscribe(function() {
		el.parentNode.removeChild(el);
	});
	anim.animate();
}

function utf16to8(str) {
	var out, i, len, c;

	out = "";
	len = str.length;
	for(i = 0; i < len; i++) {
		c = str.charCodeAt(i);
		if ((c >= 0x0001) && (c <= 0x007F)) {
			out += str.charAt(i);
		} else if (c > 0x07FF) {
			out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
			out += String.fromCharCode(0x80 | ((c >>  6) & 0x3F));
			out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
		} else {
			out += String.fromCharCode(0xC0 | ((c >>  6) & 0x1F));
			out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
		}
	}
	return out;
}

function utf8to16(str) {
	var out, i, len, c;
	var char2, char3;

	out = "";
	len = str.length;
	i = 0;
	while(i < len) {
		c = str.charCodeAt(i++);
		switch(c >> 4)
		{ 
			case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
			// 0xxxxxxx
			out += str.charAt(i-1);
			break;
			case 12: case 13:
			// 110x xxxx   10xx xxxx
			char2 = str.charCodeAt(i++);
			out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
			break;
			case 14:
			// 1110 xxxx  10xx xxxx  10xx xxxx
			char2 = str.charCodeAt(i++);
			char3 = str.charCodeAt(i++);
			out += String.fromCharCode(((c & 0x0F) << 12) |
							 ((char2 & 0x3F) << 6) |
							 ((char3 & 0x3F) << 0));
			break;
		}
	}

	return out;
}

function BASE64_encode(src) {
	var enKey = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	var str = new Array();
	var ch1, ch2, ch3;
	var pos = 0;
	while(pos+3<=src.length){
		ch1 = src.charCodeAt(pos++);
		ch2 = src.charCodeAt(pos++);
		ch3 = src.charCodeAt(pos++);
		str.push(enKey.charAt(ch1>>2), enKey.charAt(((ch1<<4)+(ch2>>4))&0x3f));
		str.push(enKey.charAt(((ch2<<2)+(ch3>>6))&0x3f), enKey.charAt(ch3&0x3f));
	}
	if(pos<src.length){
		ch1 = src.charCodeAt(pos++);
		str.push(enKey.charAt(ch1>>2));
		if(pos<src.length){
			ch2= src.charCodeAt(pos);
			str.push(enKey.charAt(((ch1<<4)+(ch2>>4))&0x3f));
			str.push(enKey.charAt(ch2<<2&0x3f), '=');
		}else{
			str.push(enKey.charAt(ch1<<4&0x3f), '==');
		}
	}
	return str.join('');
}

function BASE64_decode(src) {
	var deKey = new Array(
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
		52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
		-1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
		15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
		-1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
		41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1
	);
	var str = new Array();
	var ch1, ch2, ch3, ch4;
	var pos = 0;
	src = src.replace(/[^A-Za-z0-9\+\/]/g, '');
	while(pos+4<=src.length){
		ch1 = deKey[src.charCodeAt(pos++)];
		ch2 = deKey[src.charCodeAt(pos++)];
		ch3 = deKey[src.charCodeAt(pos++)];
		ch4 = deKey[src.charCodeAt(pos++)];
		str.push(String.fromCharCode(
			(ch1<<2&0xff)+(ch2>>4), (ch2<<4&0xff)+(ch3>>2), (ch3<<6&0xff)+ch4));
	}
	if(pos+1<src.length){
		ch1 = deKey[src.charCodeAt(pos++)];
		ch2 = deKey[src.charCodeAt(pos++)];
		if(pos<src.length){
			ch3 = deKey[src.charCodeAt(pos)];
			str.push(String.fromCharCode((ch1<<2&0xff)+(ch2>>4), (ch2<<4&0xff)+(ch3>>2)));
		}else{
			str.push(String.fromCharCode((ch1<<2&0xff)+(ch2>>4)));
		}
	}
	return str.join('');
}

function unserialize(ss) {
	var p = 0, ht = [], hv = 1;
	var unser_null = function() {
		p++;
		return null;
	};
	var unser_boolean = function() {
		p++;
		var b = (ss.charAt(p++) == '1');
		p++;
		return b;
	};
	var unser_integer = function() {
		p++;
		var i = parseInt(ss.substring(p, p = ss.indexOf(';', p)));
		p++;
		return i;
	};
	var unser_double = function() {
		p++;
		var d = ss.substring(p, p = ss.indexOf(';', p));
		switch (d) {
			case 'NAN': d = NaN; break;
			case 'INF': d = Number.POSITIVE_INFINITY; break;
			case '-INF': d = Number.NEGATIVE_INFINITY; break;
			default: d = parseFloat(d);
		}
		p++;
		return d;
	};
	var unser_string = function() {
		p++;
		var l = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		var s = utf8to16(ss.substring(p, p += l));
		p += 2;
		return s;
	};
	var unser_array = function() {
		p++;
		var n = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		var a = [];
		ht[hv++] = a;
		for (var i = 0; i < n; i++) {
			var k;
			switch (ss.charAt(p++)) {
				case 'i': k = unser_integer(); break;
				case 's': k = unser_string(); break;
				case 'U': k = unser_unicode_string(); break;
				default: return false;
			}
			a[k] = __unserialize();
		}
		p++;
		return a;
	};
	var unser_date = function() {
		var k, a = [];
		for (var i = 0; i < 7; i++) {
			p++;
			k = unser_string();
			p++;
			a[k] = unser_integer();
		}
		var dt = new Date(
			a['year'],
			a['month'] - 1,
			a['day'],
			a['hour'],
			a['minute'],
			a['second'],
			a['millisecond']
		);
		ht[hv++] = dt;
		return dt;
	}
	var unser_object = function() {
		p++;
		var l = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		var cn = utf8to16(ss.substring(p, p += l));
		p += 2;
		var n = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		if (cn == "Date" && n == 7) {
			return unser_date();
		}
		if (eval(['typeof(', cn, ') == "undefined"'].join(''))) {
			eval(['function ', cn, '(){}'].join(''));
		}
		var o = eval(['new ', cn, '()'].join(''));
		ht[hv++] = o;
		for (var i = 0; i < n; i++) {
			var k;
			switch (ss.charAt(p++)) {
				case 's': k = unser_string(); break;
				case 'U': k = unser_unicode_string(); break;
				default: return false;
			}
			if (k.charAt(0) == '\0') {
				k = k.substring(k.indexOf('\0', 1) + 1, k.length);
			}
			o[k] = __unserialize();
		}
		p++;
		if (typeof(o.__wakeup) == 'function') o.__wakeup();
		return o;
	};
	var unser_custom_object = function() {
		p++;
		var l = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		var cn = utf8to16(ss.substring(p, p += l));
		p += 2;
		var n = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		if (eval(['typeof(', cn, ') == "undefined"'].join(''))) {
			eval(['function ', cn, '(){}'].join(''));
		}
		var o = eval(['new ', cn, '()'].join(''));
		ht[hv++] = o;
		if (typeof(o.unserialize) != 'function') p += n;
		else o.unserialize(ss.substring(p, p += n));
		p++;
		return o;
	};
	var unser_unicode_string = function() {
		p++;
		var l = parseInt(ss.substring(p, p = ss.indexOf(':', p)));
		p += 2;
		var sb = [];
		for (var i = 0; i < l; i++) {
			if ((sb[i] = ss.charAt(p++)) == '\\') {
				sb[i] = String.fromCharCode(parseInt(ss.substring(p, p += 4), 16));
			}
		}
		p += 2;
		return sb.join('');
	};
	var unser_ref = function() {
		p++;
		var r = parseInt(ss.substring(p, p = ss.indexOf(';', p)));
		p++;
		return ht[r];
	};
	var __unserialize = function() {
		switch (ss.charAt(p++)) {
			case 'N': return ht[hv++] = unser_null();
			case 'b': return ht[hv++] = unser_boolean();
			case 'i': return ht[hv++] = unser_integer();
			case 'd': return ht[hv++] = unser_double();
			case 's': return ht[hv++] = unser_string();
			case 'U': return ht[hv++] = unser_unicode_string();
			case 'r': return ht[hv++] = unser_ref();
			case 'a': return unser_array();
			case 'O': return unser_object();
			case 'C': return unser_custom_object();
			case 'R': return unser_ref();
			default: return false;
		}
	};
	return __unserialize();
}

function reflow() {
	if (YAHOO.env.ua.ie) {
		document.body.style.zoom = document.body.style.zoom == "1" ? "100%" : "1";
	}
}

function initTip() {
	var tip = YAHOO.util.Dom.get('systip');
	if (!tip) return;
	var form_arr = tip.getElementsByTagName('form');
	if (form_arr.length == 0) return;
	function f(el) {
		YAHOO.util.Event.on(el, 'submit', function(ev) {
			YAHOO.util.Event.preventDefault(ev);
			var hidden_mark = document.createElement('input');
			hidden_mark.setAttribute('type', 'hidden');
			hidden_mark.setAttribute('name', 'ajax');
			hidden_mark.setAttribute('value', 'yes');
			this.appendChild(hidden_mark);
			YAHOO.util.Connect.setForm(this);
			var url = window.location.href;
			var callback = {
				success: function(o) {
					var msg = new FF.sysmsg();
					msg.show();
					var a = YAHOO.util.Dom.getElementsByClassName('post_act', 'a', tip);
					if (a.length > 0) {
						var a = a[0];
						var url = a.getAttribute('href'),
							url_arr = url.split('/');
							target = url_arr.pop();
						var data = 'action=tip.hide&tip=' + target + '&ajax=yes&token=' + el['token'].value;
						var cb = {
							success: function(o) {
								var res = o.responseText;
								if (res.substring(0,7) == 'success') {
									tip.parentNode.removeChild(tip);
									reflow();
								} else if (res.substring(0,7) == 'failure') {
								} else {
								}
							},
							failure: function(o) {
							},
							timeout: 5000
						}
						YAHOO.util.Connect.asyncRequest('post', '/tip.hide', cb, data);
					}
				},
				failure: function(o) {
				},
				timeout: 5000
			}
			YAHOO.util.Connect.asyncRequest('post', url, callback);
		});
	}
	YAHOO.util.Dom.batch(form_arr, f);
}

function initStream() {
	var el = YAHOO.util.Dom.get('stream');
	if (!el) return;

	var msgs = el.getElementsByTagName('li');
	YAHOO.util.Dom.addClass(msgs, 'unlight');
	YAHOO.util.Event.on(msgs, 'mouseover', function(e){
		YAHOO.util.Dom.addClass(this, 'light');
		YAHOO.util.Dom.removeClass(this, 'unlight');
	});
	YAHOO.util.Event.on(msgs, 'mouseout', function(e){
		YAHOO.util.Dom.addClass(this, 'unlight');
		YAHOO.util.Dom.removeClass(this, 'light');
	});
}

function initInfos() {
	var el = YAHOO.util.Dom.get('user_infos');
	if (!el) return;

	var b_bio = false,
		infos = YAHOO.util.Dom.getElementsByClassName('collapse', 'li', el),
		b_infos = infos.length > 0 ? true : false,
		el_bio = YAHOO.util.Dom.get('bio');

	if (el_bio) {
		var span_bio = el_bio.getElementsByTagName('span')[0];
		var str_text = span_bio.innerText || span_bio.textContent;
		if (str_text.length > 60) {
			b_bio = true;
		}
	}

	if (b_infos || b_bio) {
		if (b_bio) {
			var str1 = span_bio.innerHTML;
			var str2 = str_text.substring(0, 50);
			span_bio.innerHTML = str2;
		}
		var el_toggle = document.createElement('a');
		el_toggle.appendChild(document.createTextNode('(更多...)'));
		el_toggle.setAttribute('title', '点击展开');
		el_toggle.setAttribute('href', 'javascript:void 0');
		if (el_bio) {
			el_toggle.style.marginLeft = '.5em';
			el_bio.appendChild(el_toggle);
		} else {
			el.appendChild(el_toggle);
		}
		var el_toggle2 = document.createElement('a');
		el_toggle2.appendChild(document.createTextNode('(收起...)'));
		el_toggle2.setAttribute('title', '点击收起');
		el_toggle2.setAttribute('href', 'javascript:void 0');
		el_toggle2.style.display = 'none';
		el.appendChild(el_toggle2);
		YAHOO.util.Event.on(el_toggle, 'click', function(e){
			if (b_bio) span_bio.innerHTML = str1;
			YAHOO.util.Dom.addClass(el, 'visible');
			YAHOO.util.Dom.removeClass(infos, 'collapse');
			this.style.display = 'none';
			el_toggle2.style.display = 'inline';
		});
		YAHOO.util.Event.on(el_toggle2, 'click', function(e){
			if (b_bio) span_bio.innerHTML = str2;
			YAHOO.util.Dom.removeClass(el, 'visible');
			YAHOO.util.Dom.addClass(infos, 'collapse');
			this.style.display = 'none';
			el_toggle.style.display = 'inline';
		});
	}
}

function initSearch() {
	var $D = YAHOO.util.Dom;
	var $E = YAHOO.util.Event;
	var el = $D.get('search-switch');
	if (!el) return;

	var frm = $D.get('searchpanel');
	var elKeyword = $D.get('keyword');
	elKeyword.focus();
	
	var tabs = el.getElementsByTagName('a');
	$E.on(tabs, 'click', function(ev){
		var name = this.id.substring(7);
		if (!$D.hasClass(this, 'current')) {
			$D.removeClass(tabs, 'current');
			$D.addClass(this, 'current');
			elKeyword.name = name;
			if (YAHOO.lang.trim(elKeyword.value) != '') {
				sendRequest(ev);
			}
		}
		elKeyword.focus();
		$E.preventDefault(ev);
	});
	YAHOO.util.Event.on(frm, 'submit', sendRequest);
	function sendRequest(e){
		if(e) { $E.stopEvent(e); }
		if(elKeyword.name== 'q'){
			var url = '/q/' + encodeURIComponent(elKeyword.value);
			window.location.href = url;
		} else {
			frm.submit();
		}
	}
}

function initClick() {
	YAHOO.util.Event.on(document, 'click', handle);
	function handle(ev) {
		if (ev.button == 0) {
			var target = YAHOO.util.Event.getTarget(ev);
			if (target.tagName.toLowerCase() == 'img') {
				target = YAHOO.util.Dom.getAncestorBy(target);
			}
			if (target.tagName.toLowerCase() == 'a' && YAHOO.util.Dom.hasClass(target, 'post_act')) {
				pa(target);
				YAHOO.util.Event.preventDefault(ev);
			} else if (YAHOO.util.Dom.hasClass(target, 'auto-select')) {
				target.select();
			} else if (YAHOO.util.Dom.hasClass(target, 'sharebtn')) {
				YAHOO.util.Event.preventDefault(ev);
				if (((window.location.href.indexOf('/home') != -1) || (window.location.href.indexOf('/shares') != -1))  && confirm('把本按钮添加到浏览器的工具栏上，就可以快速与你的好友分享网页。\r\n是否查看详细的操作指南？')) {
					window.location.href = 'http://help.fanfou.com/share_button.html';
				}
			}
		}
	}

	function post_act(aMap, sConfirm) {
		if (sConfirm && !confirm(sConfirm)) return;
		var act = new FF.Form();
		act.mkForm('post', window.location.href, aMap);
		act.post();
	}

	function pa(el) {
		var url = el.getAttribute('href'),
			arr = {};
		var pos = url.lastIndexOf('/');
		var pos1 = url.substring(0, pos).lastIndexOf('/');
		if (YAHOO.util.Dom.hasClass(el, 'sp')) {
			var actname = url.substring(pos + 1);
			arr['action'] = actname;
		} else {
			var target = url.substring(pos + 1);
			var actname = url.substring(pos1 + 1, pos);
			var tarname = actname.split('.')[0];
			arr['action'] = actname;
			arr[tarname] = target;
		}
		var token = el.getAttribute('token'),
			tkVal = '&token=' + token;
		arr['token'] = token;
		if (actname == 'favorite.add' || actname == 'photo.share') {
			var data = 'action=' + actname + '&' + tarname + '=' + target + '&ajax=yes' + tkVal,
				url = '/' + actname;
			var pos = YAHOO.util.Dom.getXY(el);
			var span = new FF.ajax_prompt(pos, '正在加入分享...');
			var callback1 = {
				success: function(o) {
					var res = o.responseText;
					if (res.substring(0,7) == 'success') {
						var str = res.substring(8);
						span.change(str);
						span.off();
					} else if (res.substring(0,7) == 'failure') {
						var str = res.substring(8);
						span.change(str);
						span.off();
					} else {
						span.change('服务异常，请稍后再试');
						span.off();
					}
				},
				failure: function(o) {
						span.change('服务异常，请稍后再试');
						span.off();
				},
				timeout: 5000
			}
			YAHOO.util.Connect.asyncRequest('post', url, callback1, data);
			return;
		} else if (actname == 'notice.hideall') {
			var str = '你确定要隐藏所有通知吗？';
			if (!confirm(str)) return;
			var data = 'action=notice.hideall&ajax=yes' + tkVal,
				url = '/notice.hideall';
			var container = YAHOO.util.Dom.get('newsfeed');
			container.parentNode.removeChild(container);
			var callback2 = {
				success: function(o) {
					var res = o.responseText;
					if (res.substring(0,7) == 'success') {
					} else if (res.substring(0,7) == 'failure') {
					} else {
					}
				},
				failure: function(o) {
				},
				timeout: 5000
			}
			YAHOO.util.Connect.asyncRequest('post', url, callback2, data);
			return;
		} else if (actname == 'notice.ignore') {
			var data = 'action=notice.ignore&notice=' + target + '&ajax=yes' + tkVal,
				url = '/notice.ignore';
			var container = YAHOO.util.Dom.getAncestorByTagName(el, 'li');
			if (YAHOO.util.Dom.getAncestorByTagName(container, 'ul').getElementsByTagName('li').length == 1) {
				container = YAHOO.util.Dom.get('newsfeed');
			}
			container.parentNode.removeChild(container);
			reflow();
			var callback3 = {
				success: function(o) {
					var res = o.responseText;
					if (res.substring(0,7) == 'success') {
					} else if (res.substring(0,7) == 'failure') {
					} else {
					}
				},
				failure: function(o) {
				},
				timeout: 5000
			}
			YAHOO.util.Connect.asyncRequest('post', url, callback3, data);
			return;
		} else if (actname == 'tip.hide') {
			var data = 'action=tip.hide&tip=' + target + '&ajax=yes' + tkVal,
				url = '/tip.hide';
			var container = YAHOO.util.Dom.get('systip');
			container.parentNode.removeChild(container);
			reflow();
			var callback4 = {
				success: function(o) {
					var res = o.responseText;
					if (res.substring(0,7) == 'success') {
					} else if (res.substring(0,7) == 'failure') {
					} else {
					}
				},
				failure: function(o) {
				},
				timeout: 5000
			}
			YAHOO.util.Connect.asyncRequest('post', url, callback4, data);
			return;
		} else if (actname == 'msg.del') {
			var str = '你确定要删除这条消息吗？';
			// 单条消息删除需要跳转
			var redirect = el.getAttribute('redirect');
			if(redirect){
				arr['redirect'] = redirect;
			}
		} else if (actname == 'share.del' || actname == 'favorite.del') {
			var str = '你确定要删除这个分享吗？';
		} else if (actname == 'photo.del') {
			var str = '你确定要删除这张照片吗？';
		} else if (actname == 'friend.remove') {
			var str = '你确定要删除这个好友吗？';
		} else if (actname == 'user.block') {
			var username = el.getAttribute('sname'); 
			var str = '确定把 '+username+' 加入黑名单吗？加入黑名单后他既不能查看你的信息，也不能给你发私信。';
		} else if (actname == 'privatemsg.del') {
			var str = '你确定要删除这条私信吗？';
		}
		post_act(arr, str);
	}
}

function initForm() {
	var form_arr = document.getElementsByTagName('form');
	if (form_arr.length == 0) return;
	var hasFocus = false, tip = false;
	// 只有textarea和text input可以focus
	function canFocus(el){
		var type = el.getAttribute('type');
		var nodeName = el.tagName;
		if(!type || !nodeName) return false;
		return type.toLowerCase() == 'text' || type.toLowerCase() == 'textarea' ;
	}
	function focus(el){
		if(canFocus(el)){ el.focus(); }
	}
	for (var i=0; i<form_arr.length; i++) {
		var fm = form_arr[i];
		var id = fm.getAttribute('id');
		if (!hasFocus && id && FF.cfg.formfocus.indexOf(id) != -1) {
			if (!fm.elements[0].value){
				focus(fm.elements[0]);
			} else {
				focus(fm.elements[1]);
			}
			hasFocus = true;
		}
		if (YAHOO.util.Dom.hasClass(fm, 'limit')) {
			FieldLimit(fm);
		}
		if (FF.cfg.formpattern[id]) {
			chkform(fm);
		}
		if (FF.cfg.formtip[id]) {
			var tip = true;
		}
		for (var j=0; j<fm.elements.length; j++) {
			var element = fm.elements[j];
			if (FF.cfg.textlimit[element.getAttribute('id')]) {
				YAHOO.util.Event.on(element, 'keyup', function() {
					textLimit(this, FF.cfg.textlimit[this.id]);
				});
			}
			if (tip && FF.cfg.formtip[id][element.name]) {
				var text = FF.cfg.formtip[id][element.name];
				setFieldTip(element, fm, text);
			}
			if (element.type == 'submit' && element.name == 'remove') {
				YAHOO.util.Event.on(element, 'click', function(ev) {
					if (!confirm('你确定要' + this.value + '吗？')) YAHOO.util.Event.preventDefault(ev);
				});
			}
			if (YAHOO.util.Dom.hasClass(element, 'qs')) {
				function ctrlEnter(e) {
					if (YAHOO.env.ua.ie) {
						if (window.event.ctrlKey && window.event.keyCode == 13) post_form(this);
					} else {
						function isKeyTrigger(e,keyCode){
							var argv = isKeyTrigger.arguments;
							var argc = isKeyTrigger.arguments.length;
							var bCtrl = false;
							if (argc > 2) bCtrl = argv[2];
							var bAlt = false;
							if (argc > 3) bAlt = argv[3];
							var nav4 = window.Event ? true : false;
							if (typeof e == 'undefined') e = event;
							if (bCtrl && !((typeof e.ctrlKey != 'undefined') ? e.ctrlKey : e.modifiers & Event.CONTROL_MASK > 0)) return false;
							if (bAlt && !((typeof e.altKey != 'undefined') ? e.altKey : e.modifiers & Event.ALT_MASK > 0)) return false;
							var whichCode = 0;
							if (nav4) whichCode = e.which;
							else if (e.type == 'keypress' || e.type == 'keydown') whichCode = e.keyCode;
							else whichCode = e.button;
							return (whichCode == keyCode);
						}
						if (isKeyTrigger(e, 13, true)) {
							post_form(this);
						}
					}
				}
				YAHOO.util.Event.on(element, 'keyup', ctrlEnter, fm, true);
			}
		}
	}
	function textLimit(el, count) {
		if(el.value.length > count) {
			el.value = el.value.slice(0, count);
			var tip_array = YAHOO.util.Dom.getElementsByClassName('hint', 'span', YAHOO.util.Dom.getAncestorBy(el));
			if (tip_array.length > 0) YAHOO.util.Dom.removeClass(tip_array[0], 'hidden');
		}
	}
	function FieldLimit(el) {
		var count = FF.cfg.textlimit[el.getAttribute('id')];
		var text = el.content;
		var counter = YAHOO.util.Dom.getElementsByClassName('tip', 'p', el)[0];
		var last = '';
		function textCount() {
			// 频繁赋值ie会闪 这里作一下处理 如果没改动则跳过
			if(this.value == last) {
				return;
			} else {
				last = this.value;
			}
			var btn = YAHOO.util.Dom.getElementsByClassName('formbutton', 'input', el)[0];
			if (this.value.length == 0) {
				btn.disabled = true;
				YAHOO.util.Dom.addClass(btn, 'forbidden');
			} else {
				btn.disabled = false;
				YAHOO.util.Dom.removeClass(btn, 'forbidden');
			}
			 if (this.value.length <= count) {
				counter.innerHTML = '可以输入 <span class="counter">' + (count - this.value.length) + '</span> 字';
				if (YAHOO.util.Dom.hasClass(counter, 'caution')) YAHOO.util.Dom.removeClass(counter, 'caution');
			} else {
				counter.innerHTML = '已经超出 <span class="counter">' + (this.value.length - count) + '</span> 字';
				if (!YAHOO.util.Dom.hasClass(counter, 'caution')) YAHOO.util.Dom.addClass(counter, 'caution');
			}
		}
		//YAHOO.util.Event.on(text, 'click', textCount);
		//YAHOO.util.Event.on(text, 'keyup', textCount);
		//YAHOO.util.Event.on(text, 'blur', textCount);
		var interId = window.setInterval(function(){
			textCount.call(text);
		},50);
		YAHOO.util.Event.onDOMReady(textCount, null, text);
	}
	function setFieldTip(elem, form, text) {
		function __add(el, str) {
			if (el.value == '') {
				el.value = str;
				YAHOO.util.Dom.addClass(el, 'empty');
			}
		}
		function __rm(el, str) {
			if (el.value == str) el.value = '';
			YAHOO.util.Dom.removeClass(el, 'empty');
		}
		YAHOO.util.Event.on(elem, 'focus', function() {
			__rm(elem, text);
		});
		YAHOO.util.Event.on(elem, 'blur', function() {
			__rm(elem, text);
			__add(elem, text);
		});
		YAHOO.util.Event.on(form, 'submit', function() {
			__rm(elem, text);
		});
		__add(elem, text);
	}
	var a_f_e = YAHOO.util.Dom.getElementsByClassName('format-email', 'input', 'container');
	if (a_f_e.length > 0) {
		function Levenshtein_Distance(s,t){
			var n=s.length;// length of s
			var m=t.length;// length of t
			var d=[];// matrix
			var i;// iterates through s
			var j;// iterates through t
			var s_i;// ith character of s
			var t_j;// jth character of t
			var cost;// cost
			// Step 1
			if (n == 0) return m;
			if (m == 0) return n;
			// Step 2
			for (i = 0; i <= n; i++) {
				d[i]=[];
				d[i][0] = i;
			}
			for (j = 0; j <= m; j++) {
				d[0][j] = j;
			}
			// Step 3
			for (i = 1; i <= n; i++) {
				s_i = s.charAt (i - 1);
				// Step 4
				for (j = 1; j <= m; j++) {
					t_j = t.charAt (j - 1);
					// Step 5
					if (s_i == t_j) {
						cost = 0;
					}else{
						cost = 1;
					}
					// Step 6
					d[i][j] = Minimum (d[i-1][j]+1, d[i][j-1]+1, d[i-1][j-1] + cost);
				}
			}
			// Step 7
			return d[n][m];
		}
		function Levenshtein_Distance_Percent(s,t){
			var l=s.length>t.length?s.length:t.length;
			var d=Levenshtein_Distance(s,t);
			return (1-d/l).toFixed(4);
		}
		function Minimum(a,b,c){
			return a<b?(a<c?a:c):(b<c?b:c);
		}
		function findSimilarest(originalEmail){
			arr=originalEmail.split('@');
//			if(arr.length!=2)throw new Error('wrong email');
		
			var Theashold = 0.6;
			var domains = ['gmail.com',
			'163.com',
			'hotmail.com',
			'126.com',
			'qq.com',
			'yahoo.com.cn',
			'sina.com',
			'sohu.com',
			'msn.com',
			'tom.com',
			'21cn.com',
			'live.com',
			'yahoo.com',
			'yeah.net',
			'foxmail.com',
			'vip.sina.com',
			'eyou.com',
			'263.net',
			'sina.com.cn',
			'citiz.net',
			'live.cn',
			'56.com',
			'yahoo.com.tw',
			'vip.163.com',
			'188.com',
			'baidu.com',
			'xmu.edu.cn',
			'sogou.com',
			'mails.tsinghua.edu.cn',
			'live.it',
			'fudan.edu.cn',
			'yahoo.com.hk',
			'tencent.com',
			'wozone.cn',
			'vip.sohu.net',
			'googlemail.com',
			'gd165.com',
			'discuz.com'];
		
			var domain = arr[1];
			domain = domain.toLowerCase();
			var max = 0;
			var max_index = -1;
			for(i in domains){
				var dist = Levenshtein_Distance_Percent(domain, domains[i]);
				if(dist > max){
					max = dist;
					max_index = i;
				}
			}
			if(max > Theashold){
				arr[1] = domains[max_index]
				var ret =  arr.join('@');;
				return ret;
			}
			else{
				return null;
			}
		}
		function checkEmail(){
			var email_addr = YAHOO.lang.trim(this.value);
			if (email_addr == 0 || email_addr.split('@').length != 2) return;
			var email_addr_correct = findSimilarest(email_addr);
			if (email_addr_correct == email_addr || email_addr_correct == null) {
				if (document.getElementById('email-suggest')) {
					var tip = document.getElementById('email-tip');
					tip.parentNode.removeChild(tip);
					var icon_question = document.getElementById('icon-question');
					icon_question.parentNode.removeChild(icon_question);
					YAHOO.util.Dom.removeClass(this, 'input-question');
				}
			} else {
				YAHOO.util.Dom.addClass(this, 'input-question');
				if (document.getElementById('email-suggest')) {
					var email_suggest = document.getElementById('email-suggest');
					email_suggest.lastChild.nodeValue = email_addr_correct;
				} else {
					var container = YAHOO.util.Dom.getAncestorBy(this);
					YAHOO.util.Dom.setStyle(container, 'position', 'relative');

					var tip = document.createElement('p');
					tip.setAttribute('id', 'email-tip');
					YAHOO.util.Dom.addClass(tip, 'input-tip');
					container.appendChild(tip);
					tip.appendChild(document.createTextNode('你是否想输入'));
					var region = YAHOO.util.Dom.getRegion(this);
					var tip_w = region['right'] - region['left'] - 23;
					var tip_h = region['bottom'] - region['top'];
					YAHOO.util.Dom.setX(tip, region['left']);
					YAHOO.util.Dom.setY(tip, region['bottom'] - 1);
					YAHOO.util.Dom.setStyle(tip, 'width', tip_w + 'px');

					var email_suggest = document.createElement('strong');
					email_suggest.setAttribute('id', 'email-suggest');
					email_suggest.setAttribute('title', '我要使用此email');
					email_suggest.appendChild(document.createTextNode(email_addr_correct));
					tip.appendChild(email_suggest);
					YAHOO.util.Dom.setStyle(email_suggest, 'cursor', 'pointer');

					var closebtn = document.createElement('a');
					YAHOO.util.Dom.addClass(closebtn, 'close');
					closebtn.setAttribute('href', '#');
					closebtn.setAttribute('title', '关闭');
					closebtn.appendChild(document.createTextNode('关闭'));
					tip.appendChild(closebtn);

					var icon_question = document.createElement('a');
					icon_question.setAttribute('id', 'icon-question');
					icon_question.setAttribute('href', '#');
					icon_question.setAttribute('title', '你输入的email可能存在问题...');
					icon_question.appendChild(document.createTextNode('你输入的email可能存在问题...'));
					container.appendChild(icon_question);
					YAHOO.util.Dom.setStyle(icon_question, 'position', 'absolute');
					YAHOO.util.Dom.setStyle(icon_question, 'overflow', 'hidden');
					YAHOO.util.Dom.setStyle(icon_question, 'display', 'block');
					YAHOO.util.Dom.setStyle(icon_question, 'width', '18px');
					YAHOO.util.Dom.setStyle(icon_question, 'height', '18px');
					YAHOO.util.Dom.setStyle(icon_question, 'text-indent', '-999em');
					YAHOO.util.Dom.setX(icon_question, region['right'] - 21);
					YAHOO.util.Dom.setY(icon_question, region['top'] + 3);

					YAHOO.util.Event.on(email_suggest, 'click', function(ev){
						YAHOO.util.Event.preventDefault(ev);
						this.value = email_addr_correct;
						container.removeChild(tip);
						container.removeChild(icon_question);
						YAHOO.util.Dom.removeClass(this, 'input-question');
					}, this, true);

					YAHOO.util.Event.on(closebtn, 'click', function(ev){
						YAHOO.util.Event.preventDefault(ev);
						YAHOO.util.Dom.setStyle(tip, 'display', 'none');
					});

					YAHOO.util.Event.on(icon_question, 'click', function(ev){
						YAHOO.util.Event.preventDefault(ev);
						YAHOO.util.Dom.setStyle(tip, 'display', 'block');
					});
				}
			}
		}
		YAHOO.util.Dom.batch(a_f_e, function(el){
			YAHOO.util.Event.on(el, 'blur', checkEmail);
		});
	}
}

function chkform(fm) {
	var submit_yet = false;
	var id = fm.getAttribute('id');
	var relist = FF.cfg.formpattern[id].relist;
	var verifylist = FF.cfg.formpattern[id].verifylist;
	var requiredlist = FF.cfg.formpattern[id].requiredlist;
	var charlist = FF.cfg.formpattern[id].charlist;
	function markError(errorField) {
		YAHOO.util.Dom.addClass(YAHOO.util.Dom.getAncestorBy(errorField), 'error');
	}
	function fixError(errorField) {
		YAHOO.util.Dom.removeClass(YAHOO.util.Dom.getAncestorBy(errorField), 'error');
	}
	function chkRE(s, r) {
		r.lastIndex = 0;
		if (s.length && !r.test(s)) return false;
		else return true;
	}
	function verify(s, s2) {
		if (((!submit_yet && s.length) || (submit_yet || s.length)) && s != s2) return false;
		else return true;
	}
	function chkRequired(s, b) {
		if (s.length == 0) return false;
		else if (b) return true;
		else return 'i';
	}
	function chkLength(s, n) {
		var num = 0;
		for (var i=0; i<s.length; i++) {
			if (s.charCodeAt(i) < 128) num++;
			else num += 2;
		}
		if(num > n) return false;
		else return true;
	}
	function chkPattern() {
		for (var i=0; i<fm.elements.length; i++) {
			var f = fm[i];
			var name = f.getAttribute('name');
			if (name == '') continue; 
			var str = YAHOO.lang.trim(f.value);
			var onlyrequired = true;
			if (relist && relist.indexOf(name) != -1) {
				if (name == 'mobile'){
					str = str.replace(/[\s-]+/g, '');
				}
				!chkRE(str, FF.cfg.re[name]) ? markError(f) : fixError(f);
				onlyrequired = false;
			}
			if (verifylist && verifylist[name]) {
				!verify(f.value, fm[verifylist[name]].value) ? markError(f) : fixError(f);
				onlyrequired = false;
			}
			if (charlist && charlist[name]) {
				!chkLength(str, charlist[name]) ? markError(f) : fixError(f);
				onlyrequired = false;
			}
			if (requiredlist && submit_yet && (requiredlist.indexOf(name) != -1)) {
				var sw = chkRequired(f.value, onlyrequired);
				if (sw == 'i') ;
				else !sw ? markError(f) : fixError(f);
			}
		}
	}
	for (var i=0; i<fm.elements.length; i++) {
		YAHOO.util.Event.on(fm[i], 'focus', function() {
			YAHOO.util.Dom.addClass(YAHOO.util.Dom.getAncestorBy(this), 'inputting');
		});
		YAHOO.util.Event.on(fm[i], 'blur', function() {
			YAHOO.util.Dom.removeClass(YAHOO.util.Dom.getAncestorBy(this), 'inputting');
			chkPattern();
		});
	}
	YAHOO.util.Event.on(fm, 'submit', function(ev) {
		submit_yet = true;
		chkPattern();
		var err_arr = YAHOO.util.Dom.getElementsByClassName('error', 'p', fm);
		if (err_arr.length > 0) {
			alert('信息填写有误，请检查');
			YAHOO.util.Event.preventDefault(ev);
			return;
		}
		post_form(fm, ev);
	});
}

FF.ga = {
	init: function(){
		var s = document.createElement('script');
		s.type = 'text/javascript';
		s.src = 'http://www.google-analytics.com/ga.js';
		document.getElementsByTagName('head')[0].appendChild(s);
		var	pageTracker,
			maxTry = 0,
			timer = window.setInterval(function(){
				// 1200秒还没有得到pageTracker，放弃
				if (maxTry > 599) window.clearInterval(timer);
				if (!pageTracker){
					try {
						pageTracker = _gat._getTracker('UA-1805418-1');
						pageTracker._initData();
					} catch(ex){
						//do nothing
					}
				} else {
					window.clearInterval(timer);
					FF.ga.setRules(pageTracker);
				}
				++maxTry;
			} , 200);
	},
	setRules: function(tracker){
		var l = window.location.href,
			h = window.location.host,
			host = h.match(/^(?:www\.)?(\w+\.com)/);
		host = (host && host.length > 1) ? host[1] : 'fanfou.com';
		tracker._setDomainName(host);
		if (FF.gaCode){
			var code = '/' + FF.gaCode;
		} else {
			var code = l.replace(new RegExp('^http://' + host + '/'), '');
		}
		tracker._trackPageview(code);
	}
}

YAHOO.util.Event.onDOMReady(function(){
	initClick();
	FF.ga.init();
});
YAHOO.util.Event.onContentReady('container', initTip);
YAHOO.util.Event.onContentReady('container', initInfos);
YAHOO.util.Event.onContentReady('container', initSearch);
YAHOO.util.Event.onContentReady('container', initStream);
YAHOO.util.Event.onContentReady('container', initForm);

