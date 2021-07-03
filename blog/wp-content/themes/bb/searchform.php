<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
<p><input class="querytext" type="text" onfocus="if(value=='输入搜索关键字后回车'){this.value=''};this.style.color='#666666';" onblur="if(this.value==''){this.value='输入搜索关键字后回车'};this.style.color='#DADFE3';" value="输入搜索关键字后回车" size="24" name="s" id="s" />
<input type="submit" id="searchsubmit" value="搜索" style="display: none;"/></p>
</form>