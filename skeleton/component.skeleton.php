<?php echo "<?php\n" ?>
/**
 * <?php echo ( $description ? $description : '[[description]]' ) . "\n" ?>
 * 
 * @package     <?php echo $package != '' ? $package . "\n" : "[[package]]\n" ?>
<?php include(dirname(__FILE__) . '/_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?><?php echo $is_model ? " extends Samurai_Model\n" : "\n" ?>
{
    /**
     * @dependencies
     */


    /**
     * constructor.
     *
     * @access     public
     */
    public function __construct()
    {
        parent::__construct();
    }
}

