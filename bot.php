<?php
require_once("config.php");
require_once('function/curl.func.php');

$GLOBALS['db']->debug = true;

$bot = $argv[1];

if ($argc > 0)
{
    for ($i = 1; $i < $argc; $i++)
    {
        parse_str($argv[$i], $val);
        $_REQUEST = array_merge($_REQUEST, $val);
    }
}

require_once("bot/$bot.bot.php");
bot();

?>
