jQuery.fn.tab = function() {
    var holder = new Array();
    this.each(function(){
        var objID = $(this).attr("id").replace('tab_','');
        holder.push(objID);
        $(this).click(function(){
            var objID = $(this).attr("id").replace('tab_','');
            switchTabs(objID);
        });
    });
    function switchTabs(objID){
		for( var key in holder ) {
			var tabID = holder[key];
			if( objID == tabID ) {
				jQuery("." + tabID).show();
				jQuery("#tab_" + tabID).addClass("up");
			} else {
				jQuery("." + tabID).hide();
				jQuery("#tab_" + tabID).removeClass("up");
			}
		}
    }
}
