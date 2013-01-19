<?php echo "<?php\n" ?>
/**
 * <?php echo ( $description ? $description : '[[description]]' ) . "\n" ?>
 * 
 * @package     <?php echo $package != '' ? $package . "\n" : "[[package]]\n" ?>
 * @subpackage  Migration
<?php include(__DIR__ . DS . '_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?> extends ActiveGateway_Migration
{
    /**
     * when version up.
     *
     * @access  public
     */
    public function up()
    {
        // implements.
    }

    /**
     * when version down.
     *
     * @access  public
     */
    public function down()
    {
        // implements.
    }
}

