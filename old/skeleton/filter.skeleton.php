<?php echo "<?php\n" ?>
/**
 * [[機能説明]]
 * 
 * @package    <?php echo $package != '' ? $package . "\n" : "[[パッケージ名]]\n" ?>
 * @subpackage Filter
<?php include('_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?> extends Samurai_Filter
{
    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
    }


    /**
     * @override
     */
    protected function _postfilter()
    {
        parent::_postfilter();
    }
}

