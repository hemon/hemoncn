function getLunar(){
    sys_birthday = $("#sys_birthday").val();
    isLunar = $("input[@name=isLunar]:checked").val();
    $.get("lar.php", { date: sys_birthday, lar: isLunar },
        function(data){
            if( isLunar == 1 ){
                $("#birthday").val(data);
                $("#lunar").val(sys_birthday);
            } else {
                $("#birthday").val(sys_birthday);
                $("#lunar").val(data);
            }
        }
    );
}

$(function(){
    $('.tab li').tab();
    $("input[@name=isLunar]").bind("click", getLunar); 
    if( $("#lunar").val() == "" ){
        getLunar();
    }
});
