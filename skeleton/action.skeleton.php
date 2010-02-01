<?php echo "<?php\n" ?>
/**
 * [[機能説明]]
 * 
 * @package    <?php echo $package != '' ? $package . "\n" : "[[パッケージ名]]\n" ?>
 * @subpackage Action
<?php include('_doc_comment.skeleton.php'); ?>
 */
class <?php echo $class_name ?> extends Samurai_Action
{
    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        return 'success';
    }
}

