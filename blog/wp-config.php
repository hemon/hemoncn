<?php
// ** MySQL settings ** //
define('DB_NAME', 'hemon');    // ��������д�������ݿ�����
define('DB_USER', 'root');     // ��������д�������ݿ���û�����
define('DB_PASSWORD', 'zzzizzz1'); // ��������д���û�������
define('DB_HOST', 'localhost');    // һ������£���������ı���Ŀ
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// You can have multiple installations in one database if you give each a unique prefix
$table_prefix  = 'wp_';   // Only numbers, letters, and underscores please!

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', 'zh_CN');

/* That's all, stop editing! Happy blogging. */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'wp-settings.php');
?>
