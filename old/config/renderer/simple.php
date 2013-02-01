<?php
/**
 * Simple用の初期化スクリプト
 * 
 * @package    Samurai
 * @subpackage Config.Renderer
 */

//ディレクトリ
$this->template_dir = Samurai_Config::get('directory.template');

//Helper
$Helper = $this->addHelper('Error', array('class'=>'Etc_Helper_Simple_ErrorList'));

//アプリケーションによる上書用ファイルのインクルード
$application_override = sprintf('config/renderer/simple.%s.php', SAMURAI_APPLICATION_NAME);
$application_override = Samurai_Loader::getPath($application_override);
if(Samurai_Loader::isReadable($application_override)) include($application_override);

//環境による上書用ファイルのインクルード
$environment_override = sprintf('config/renderer/simple.%s.php', SAMURAI_ENVIRONMENT);
$environment_override = Samurai_Loader::getPath($environment_override);
if(Samurai_Loader::isReadable($environment_override)) include($environment_override);

