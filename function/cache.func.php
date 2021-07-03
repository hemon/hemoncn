<?php
/**
 * 读取指定缓存的内容，如果缓存内容不存在或失效，则返回 false
 *
 * 缓存文件名是根据 cacheId 用 md5 算法生成的。而缓存文件保存目录则由
 * 应用程序设置 internalCACHEDIR 决定。
 *
 * 当 $timeIsLifetime 参数为 true 时，该函数会检查缓存文件的最后更新日期加上
 * $time 是否大于当前时间。如果是，则返回 false，表示缓存的内容已经过期。
 *
 * 如果 $timeIsLifetime 参数为 false，则该函数会检查缓存文件的最后更新日期是否大于
 * $time 参数指定的时间。如果是，则返回 false。
 *
 * 用法：
 * <code>
 * // 用法 1：缓存数据，缓存数据生存期为 900 秒
 * $cacheId = 'myDataCache';
 * $data = get_cache($cacheId, 900);
 * if (!$data) {
 *     // 从数据库读取数据
 *     $data = $dbo->getAll($sql);
 *     write_cache($cacheId, $data);
 * }
 *
 * // 用法 2：
 * $xmlFilename = 'myData.xml';
 * $xmlData = get_cache($xmlFilename, filemtime($xmlFilename), false);
 * if (!$xmlData) {
 *     $xmlData = 分析xml();
 *     write_cache($xmlFilename, $xmlData);
 * }
 * </code>
 *
 * @param string $cacheId 缓存ID，不同的缓存内容应该使用不同的ID
 * @param int $time 缓存过期时间或缓存生存周期
 * @param boolean $timeIsLifetime 指示 $time 参数的作用
 *
 * @return mixed 返回缓存的内容，缓存不存在或失效则返回 false
 */

//define("CACHEDIR", "./");

function get_cache_dir($key){
    return CACHEDIR . substr($key, 0, 2) . '/';
}

function create_cache_dir($dir){
    if( !file_exists($dir) ){
		$oldu = umask(0);
		if( !mkdir($dir,0771) ) print( "Unable to mkdir $dir");
		umask($oldu);
	}
}
 
function get_cache($cacheId, $time = 900, $timeIsLifetime = true) {
    $key = md5($cacheId);
    $cache_dir = get_cache_dir($key);
    $cacheFile = $cache_dir . $key;
    if (!is_readable($cacheFile)) { return false; }
    $filetime = filemtime($cacheFile);
    if ($timeIsLifetime) {
        if (time() >= $filetime + $time) { return false; }
    } else {
        if ($time >= $filetime) { return false; }
    }
    return unserialize(file_get_contents($cacheFile));
}

/**
 * 将变量内容写入缓存
 *
 * 用法：
 * <code>
 * write_cache('my_cache_id', $data);
 * </code>
 *
 * @param string $cacheId
 * @param mixed $data
 *
 * @return boolean
 */
function write_cache($cacheId, $data) {
    $key = md5($cacheId);
    $cache_dir = get_cache_dir($key);
    create_cache_dir($cache_dir);
    $cacheFile = $cache_dir . $key;
    $contents = serialize($data);
    return file_put_contents($cacheFile, $contents);
}

/**
 * 删除指定的缓存内容
 *
 * 用法：
 * <code>
 * purge_cache('my_cache_id');
 * </code>
 *
 * @param string $cacheId
 */
function purge_cache($cacheId) {
    $key = md5($cacheId);
    $cache_dir = get_cache_dir($key);
    $cacheFile = $cache_dir . $key;
    if (file_exists($cacheFile)) { unlink($cacheFile); }
}

?>
