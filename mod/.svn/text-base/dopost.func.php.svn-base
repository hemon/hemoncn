<?php
function getForum($keyword){
	$sql = "SELECT keyword,administrator,member_only
            FROM frm_forum
            WHERE keyword = '$keyword'";
	$rs = $GLOBALS['db']->GetRow($sql);
	return $rs;
}

function addForum($keyword,$username){
	$sql = "INSERT INTO frm_forum
                (keyword, administrator, created, member_only)
            VALUES
                ('$keyword', '$username', now(), 0)";
	$rs = $GLOBALS['db']->Execute($sql);
	return $rs;
}

function getPost($keyword, $page = 1, $n = 10){
	$sql = "SELECT keyword,p.id,pid,p.username,p.name,j.sex,content,created,class,j.img
            FROM frm_post p 
                LEFT JOIN usr j ON p.username = j.username
            WHERE keyword = '$keyword'
            AND isdel = 0
            ORDER BY thread DESC";
	$rs = $GLOBALS['db']->PageExecute($sql, $n, $page);
	return $rs;
}

function addPost($keyword, $username, $name, $content, $pid = 0){
    if( !getForum($keyword) ){
        addForum($keyword, $username);
	}
    $thread = getThread($keyword, $pid);
	$sql = "INSERT INTO frm_post
                (keyword, pid, username, name, content, thread)
	        VALUES
                ('$keyword', '$pid', '$username', '$name', '$content', '$thread')";
	$rs = $GLOBALS['db']->Execute($sql);
	return $rs;
}

function getThread($keyword, $pid = 0){
    // Here we are building the thread field. See the comment
    // in comment_render().
    if ($pid == 0) {
      // This is a comment with no parent comment (depth 0): we start
      // by retrieving the maximum thread level.
      $max = $GLOBALS['db']->GetOne(sprintf("SELECT MAX(thread) FROM frm_post WHERE keyword = '%s'", $keyword));

      // Strip the "/" from the end of the thread.
      $max = rtrim($max, '/');

      // Finally, build the thread field for this new comment.
      $thread = int2vancode(vancode2int($max) + 1) .'/';
    }
    else {
      // This is comment with a parent comment: we increase
      // the part of the thread value at the proper depth.

      // Get the parent comment:
      $pthread = $GLOBALS['db']->GetOne(sprintf('SELECT thread FROM frm_post WHERE id = %d', $pid));;

      // Strip the "/" from the end of the parent thread.
      $pthread = (string) rtrim((string) $pthread, '/');

      // Get the max value in _this_ thread.
      $max = $GLOBALS['db']->GetOne(sprintf("SELECT MAX(thread) FROM frm_post WHERE thread LIKE '%s.%%' AND keyword = '%s'", $pthread, $keyword));

      if ($max == '') {
        // First child of this parent.
        $thread = $pthread .'.'. int2vancode(0) .'/';
      }
      else {
        // Strip the "/" at the end of the thread.
        $max = rtrim($max, '/');

        // We need to get the value at the correct depth.
        $parts = explode('.', $max);
        $parent_depth = count(explode('.', $pthread));
        $last = $parts[$parent_depth];

        // Finally, build the thread field for this new comment.
        $thread = $pthread .'.'. int2vancode(vancode2int($last) + 1) .'/';
      }
    }
    return $thread;
}
/**
 * Generate vancode.
 *
 * Consists of a leading character indicating length, followed by N digits
 * with a numerical value in base 36. Vancodes can be sorted as strings
 * without messing up numerical order.
 *
 * It goes:
 * 00, 01, 02, ..., 0y, 0z,
 * 110, 111, ... , 1zy, 1zz,
 * 2100, 2101, ..., 2zzy, 2zzz,
 * 31000, 31001, ...
 */
function int2vancode($i = 0) {
  $num = base_convert((int)$i, 10, 36);
  $length = strlen($num);
  return chr($length + ord('0') - 1) . $num;
}
/**
 * Decode vancode back to an integer.
 */
function vancode2int($c = '00') {
  return base_convert(substr($c, 1), 36, 10);
}

?>
