<?php
/**
 * ��ȡָ����������ݣ�����������ݲ����ڻ�ʧЧ���򷵻� false
 *
 * �����ļ����Ǹ��� cacheId �� md5 �㷨���ɵġ��������ļ�����Ŀ¼����
 * Ӧ�ó������� internalCACHEDIR ������
 *
 * �� $timeIsLifetime ����Ϊ true ʱ���ú������黺���ļ������������ڼ���
 * $time �Ƿ���ڵ�ǰʱ�䡣����ǣ��򷵻� false����ʾ����������Ѿ����ڡ�
 *
 * ��� $timeIsLifetime ����Ϊ false����ú������黺���ļ��������������Ƿ����
 * $time ����ָ����ʱ�䡣����ǣ��򷵻� false��
 *
 * �÷���
 * <code>
 * // �÷� 1���������ݣ���������������Ϊ 900 ��
 * $cacheId = 'myDataCache';
 * $data = get_cache($cacheId, 900);
 * if (!$data) {
 *     // �����ݿ��ȡ����
 *     $data = $dbo->getAll($sql);
 *     write_cache($cacheId, $data);
 * }
 *
 * // �÷� 2��
 * $xmlFilename = 'myData.xml';
 * $xmlData = get_cache($xmlFilename, filemtime($xmlFilename), false);
 * if (!$xmlData) {
 *     $xmlData = ����xml();
 *     write_cache($xmlFilename, $xmlData);
 * }
 * </code>
 *
 * @param string $cacheId ����ID����ͬ�Ļ�������Ӧ��ʹ�ò�ͬ��ID
 * @param int $time �������ʱ��򻺴���������
 * @param boolean $timeIsLifetime ָʾ $time ����������
 *
 * @return mixed ���ػ�������ݣ����治���ڻ�ʧЧ�򷵻� false
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
 * ����������д�뻺��
 *
 * �÷���
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
 * ɾ��ָ���Ļ�������
 *
 * �÷���
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
