function page(recordCount, show, pageShow, pageCount, pageNow, pageStr) {
    //初始化记录
    if (pageCount < 1) pageCount = 0;
    if (pageNow < 1) pageNow = 0;
    str = '<div class="Pages"><div class="Paginator">';
    //计算分页开始、结束、起止页数
    if (pageCount <= pageShow) {
        startHave = false;
        endHave = false;
        startNum = 1;
        endNum = pageCount;
    } else if (pageNow - 1 <= pageShow / 2) {
        startHave = false;
        endHave = true;
        startNum = 1;
        endNum = pageShow - 1;
    } else if (pageCount - pageNow <= pageShow / 2) {
        startHave = true;
        endHave = false;
        startNum = pageCount - pageShow + 2;
        endNum = pageCount;
    } else {
        startHave = true;
        endHave = true;
        startNum = pageNow - Math.floor((pageShow - 2) / 2);
        endNum = startNum + pageShow - 3;
    }
    //如果链接含有page=?，替换为page=_page_
    if (pageStr == '&') pageStr = '';
    reg = /\Wpage=\d+/;
    if (pageStr.match(reg)) {
        pageStr = pageStr.replace(reg, '?page=_page_');
    } else {
        pageStr = '?page=_page_' + pageStr;
    }
    //显示 [1]...
    if (startHave) {
        startStr = " <a class='first' href='" + pageStr.replace("_page_", 1) + "'>1</a><span class='break'>...</span>";
        str += startStr;
    }
    //显示 [5][6][7],
    for (i = startNum; i <= endNum; i++) {
        if (pageNow == i)
            str += "<strong>" + i + "</strong>";
        else
            str += " <a href='" + pageStr.replace("_page_", i) + "'>" + i + "</a>";
    }

    //显示 ...[9]
    if (endHave) {
        endStr = "<span class='break'>...</span><a class='last' href='" + pageStr.replace("_page_", pageCount) + "'>" + pageCount + "</a>";
        str += endStr;
    }

    return str + "</div></div>";
}
