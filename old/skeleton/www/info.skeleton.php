<?php echo "<?php\n" ?>
/**
 * Samurai::info()を出力するためのエントリーポイント
 *
 * @package    <?php echo $package != '' ? $package . "\n" : "[[パッケージ名]]\n" ?>
<?php include(dirname(dirname(__FILE__)) . '/_doc_comment.skeleton.php'); ?>
 */

require_once 'Samurai/Samurai.class.php';

//SamuraiFWの起動
define('SAMURAI_APPLICATION_NAME', '<?php echo $project_name ?>');
//define('SAMURAI_ENVIRONMENT', 'development');
Samurai::unshiftSamuraiDir('<?php echo $samurai_dir ?>');
Samurai::init();
Samurai_Config::set('action.default', 'samurai_info');
$Controller = Samurai::getContainer()->getComponent('Controller');
$Controller->execute();

