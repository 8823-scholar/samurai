<?php echo "<?php\n" ?>
/**
 * <?php echo ( $description ? $description : '[[description]]' ) . "\n" ?>
 * 
 * @package     <?php echo $package != '' ? $package . "\n" : "[[package]]\n" ?>
 * @subpackage  Action
<?php include(dirname(__FILE__) . '/_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?> extends Samurai_Action
{
    /**
     * @dependencies
     */


    /**
     * execute.
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();

        // write process, please.

        return 'success';
    }
}

