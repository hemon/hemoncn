FF.util = (function(){
	var $C = YAHOO.util.Connect;
	var $D = YAHOO.util.Dom;
	var $E = YAHOO.util.Event;
	var Color = YAHOO.util.Color;
	var isIE =  YAHOO.env.ua.ie;
	var isIE6 = (isIE == 6);
	var isGecko = YAHOO.env.ua.gecko;
	var isWebkit = YAHOO.env.ua.webkit;
	var isOpera = YAHOO.env.ua.opera;
	var isStr = YAHOO.lang.isString;
	var trim = function(str) {
		str = str.replace(/^\s\s*/, '');
		var ws = /\s/,
			i = str.length;
		while (ws.test(str.charAt(--i)));
		return str.slice(0, i + 1);
	}
	return {
		/**
		 * 跨浏览器使用连续字符的换行
		 * Cross Browser Word Breaker
		 * http://www.hedgerwow.com/360/dhtml/css-word-break.html
		 * @param {string|HTMLElement} el 需要处理连续字符的区块
		 * @return {bool} 成功返回true 失败返回false
		 */
		breakWord:function(el) {
			el = $D.get(el);
			if(!el || el.nodeType !== 1) return false;
			if(el.currentStyle && typeof el.currentStyle.wordBreak === 'string'){// for ie
				el.runtimeStyle.wordBreak = 'break-all';
				return true;
			} else if (document.createTreeWalker){ //for firefox opera and safari
				var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);
				var node,s,c = String.fromCharCode('8203');
				while (walker.nextNode()) {
					node = walker.currentNode;
					s = trim( node.nodeValue ).split('').join(c);
					node.nodeValue = s;
				}
				return true;
			} else {
				return false;
			}
		},
		/**
		 * focus 后光标位置不正确(最前面)的修复方案
		 * @param {string | HTMLElement} elTextArea textarea 元素的 id 或引用
		 */
		fixTextareaCursorPosition: function(elTextarea){
			if (isStr(elTextarea)) elTextarea = $D.get(elTextarea);
			if (isIE || isOpera){
				var rng = elTextarea.createTextRange();
				rng.text = elTextarea.value;
				rng.collapse(false);
			} else if (isWebkit) {
				elTextarea.select();
				window.getSelection().collapseToEnd();
			}
		}
	} // return ends here
})();
