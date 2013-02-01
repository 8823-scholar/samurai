<?php echo "<?php\n" ?>
/**
 * Initialization for SPEC.
 *
 * bootstrap script.
 * for database settings, and etc...
 * 
 * @package     <?php echo $package != '' ? $package . "\n" : "[[package]]\n" ?>
 * @subpackage  Spec
<?php include(dirname(dirname(__FILE__)) . '/_doc_comment.skeleton.php'); ?>
 */

// DI
$DI = Samurai::getContainer();

// ActiveGateway
$AGManager = ActiveGatewayManager::singleton();
$AGManager->import(Samurai_Loader::getPath('config/database/sandbox.yml'));
$DI->registerComponent('AG', $AG);

