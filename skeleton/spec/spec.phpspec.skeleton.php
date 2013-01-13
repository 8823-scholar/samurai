<?php echo "<?php\n" ?>
/**
 * <?php echo ( $description ? $description : '[[description]]' ) . "\n" ?>
 * 
 * @package     <?php echo $package != '' ? $package . "\n" : "[[package]]\n" ?>
 * @subpackage  Spec
<?php include(dirname(dirname(__FILE__)) . '/_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?> extends Samurai_Spec_Context_PHPSpec
{
    /**
     * before case.
     *
     * @access  public
     */
    public function before()
    {
    }

    /**
     * after case.
     *
     * @access  public
     */
    public function after()
    {
    }

    /**
     * before all cases.
     *
     * @access  public
     */
    public function beforeAll()
    {
        $this->_injectDependencies();
        $this->_setupFixtures();
    }

    /**
     * after all cases.
     *
     * @access  public
     */
    public function afterAll()
    {
        $this->_clearFixtures();
    }
}

