<?php echo "<?php\n" ?>
/**
 * [[機能説明]]
 * 
 * @package    <?php echo $package != '' ? $package . "\n" : "[[パッケージ名]]\n" ?>
<?php include('_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?><?php echo $is_model ? " extends Samurai_Model\n" : "\n" ?>
{
    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }
}

