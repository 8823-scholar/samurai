<?="<?php\n"?>
/**
 * SPEC用の初期化ファイル
 *
 * すべてのSPECで必要な前提処理をここに記述してください。
 * beforeSuperAllみたいなものです。
 * 
 * @package    <?=$package != '' ? $package."\n" : "[[パッケージ名]]\n"?>
 * @subpackage Action
<?include(dirname(dirname(__FILE__)) . '/_doc_comment.skeleton.php');?>
 */

//AG設定
Samurai_Loader::load('library/ActiveGateway/ActiveGatewayManager.class.php');
$AGManager = ActiveGatewayManager::singleton();
$AGManager->import(Samurai_Loader::getPath('config/activegateway/activegateway.<?=$project_name?>.yml'));
//$AGManager->import(Samurai_Loader::getPath('config/activegateway/activegateway.development.yml'));
//$AGManager->import(Samurai_Loader::getPath('config/activegateway/activegateway.sandbox.yml'));
$AG = $AGManager->getActiveGateway('sandbox');

