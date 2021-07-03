function book(){
    $(".book").each(function(){
        var loginid = $(this).html();
        var query = "?path=$book[200][a]&loginid=" + loginid;
        var url = '?loginid=' + loginid;
        var obj = $(this);
        $.get(query, function(data){
            if( (0 < data.length) && (data.indexOf('Fatal error') == -1) ) obj.html(data).attr('href',url);
        });
    });
}

function reader(){
    $(".reader").each(function(){
        var userid = $(this).html();
        var url = "?path=$user[200][a]&userid=" + userid;
        var obj = $(this);
        $.get(url,function(data){
            if( (0 < data.length) && (data.indexOf('Fatal error') == -1) ) obj.html(data);
        });
    });
}

function douban(){
    var douban = window.frames["doubanIframe"].document;
    //intro
    if($("#intro").length == 0){
        var intro = $('div.indent', douban).eq(2).html();
        if(intro && intro.indexOf('book/tag',1) == -1 && intro.indexOf('form_email',1) == -1 ) {
            $("#begin").html(intro).show().before('<h2>内容简介</h2>');
        }
    }
    //like
    $('div.obss a', douban)
        .attr("target", "_blank")
        .attr("href", function(){ 
            var alt = $('img', this).attr('alt');
            var title = ( alt ? alt: this.title ); 
            var key = title.replace(/[\(|（|：|:|-|-|—|-|-|―|\/|\\].*/, '');
            var utf8 = ( $.browser.msie ? '' : '&encode=UTF-8' );
            return '?key=' + key + utf8;
        });
    var obss = $('div.obss', douban).html();
    $(".mainr").append(obss);
    //review
    $("ul.tlst", douban).each(function(){
        $('a', this).attr("target", "_blank").attr("href", doubanUrl);
        var face = $('li.ilst', this).html();
        var clst = $('li.clst', this);
        var user =  $('span.starb', clst).html();
        var rate =  $('span.stars', clst).attr('class');
        var date =  $('div span.pl', clst).html();
        var relink = $('li.nlst a', this).attr('href');
        var review = $('div', clst).html();
        review = review.replace(/\<.*>/, '').replace(/\.\.+/, '');
        var html = '<li><p class="title">'+ user +'<span class="'+ rate +'"></span><b class="datetime">'+ date +'</b></p><p class="content"><p class="face">'+ face +'</p>'+ review +'<a href='+ relink +' target="_blank" title="更多……">......</a></p></li>';
        
        $('ul', window.frames["mypost"].document).append(html);
        $('#mypost').css("height",window.frames["mypost"].document.body.scrollHeight);
    });
}

function doubanUrl(){
    return this.href.replace(/.*hemon.cn\//, 'http://www.douban.com/');
}
