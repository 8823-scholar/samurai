<?php
/**
 * 本番環境用の設定ファイル
 * 
 * @package    Samurai
 * @subpackage Config.Environment
 */
//URLなどの設定
if(isset($_SERVER['HTTP_HOST'])){
    defined('BASE_URI') ? NULL : define('BASE_URI', dirname($_SERVER['SCRIPT_NAME']) == '/' ? '' : dirname($_SERVER['SCRIPT_NAME'])) ;
    defined('BASE_URL') ? NULL : define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URI) ;
    defined('MAIN_URI') ? NULL : define('MAIN_URI', preg_replace('/'.preg_quote(BASE_URI, '/').'/', '', $_SERVER['REQUEST_URI'])) ;
    Samurai_Config::set('url.base_uri', BASE_URI);
    Samurai_Config::set('url.base_url', BASE_URL);
    Samurai_Config::set('url.main_uri', MAIN_URI);
}
