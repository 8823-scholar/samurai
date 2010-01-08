<?="<?php\n"?>
/**
 * Smarty用の初期化スクリプト
 *
 * Samurai_Renderer::initメソッドの中でインクルードされるので、
 * $this->Engine
 * でSmartyを参照できます。
 * 
 * @package    <?=$package != '' ? $package."\n" : "[[パッケージ名]]\n"?>
 * @subpackage Config.Renderer
<?include(dirname(dirname(__FILE__)) . '/_doc_comment.skeleton.php');?>
 */
//その他のプロパティ
//$this->Engine->error_reporting = NULL;
//$this->Engine->compile_check = true;
//$this->Engine->force_compile = false;
//$this->Engine->caching = 0;
//$this->Engine->cache_lifetime = 3600;
//$this->Engine->cache_modified_check = false;
//$this->Engine->left_delimiter = '{';
//$this->Engine->right_delimiter = '}';

//Helper
//$this->addHelper('Foo', array('class'=>'Helper_Smarty_Foo'));

