FF.app = (function(){
	var $C = YAHOO.util.Connect;
	var $D = YAHOO.util.Dom;
	var $E = YAHOO.util.Event;
	var trim = YAHOO.lang.trim;
	var isIE =  YAHOO.env.ua.ie;
return {
	// 快速回复
	quickReply:function(){
		var elForm = $D.get('message');
		var elTextarea = elForm['content'];
		var elReplyIds = elForm['in_reply_to_status_id'];
		var elStream = $D.get('stream');
		focus();

		$E.on(elStream, 'click', function(e){
			var target = $E.getTarget(e);
			var nodeName = target.nodeName.toLowerCase();
			if (nodeName == 'a' && $D.hasClass(target, 'reply')){
				var datas = target.getAttribute('rel').split('|');
				var name = datas[0];
				var id = datas[1];
				elTextarea.value = combine('@'+name, trim(elTextarea.value));
				elReplyIds.value = id;
				window.scrollTo(0, 0);
				window.setTimeout(function(){ focus();}, 10);
			}
		});
		// 合并用户名 新的插到最前面 
		function combine(name, nameString){
			var seperator = ' ';
			var names = nameString.split(seperator);
			if(nameString.length == 0)
				return name + seperator;
			for(var i = 0, len = names.length; i < len; i++){
				if(names[i] == name){
					names.splice(i, 1);
					break;
				}
			}
			names.unshift(name);
			return names.join(seperator) + seperator;
		}
		function focus(){
			elTextarea.focus();
			FF.util.fixTextareaCursorPosition(elTextarea);
		}
	}
} // return ends here
})();
