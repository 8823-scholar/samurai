<?="<?php\n"?>
/**
 * [[機能説明]]
 * 
 * @package    <?=$package != '' ? $package."\n" : "[[パッケージ名]]\n"?>
<?include('_doc_comment.skeleton.php');?>
 */
class <?=$class_name?><?=$is_model ? " extends Samurai_Model\n" : "\n"?>
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

