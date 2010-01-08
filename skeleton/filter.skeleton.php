<?="<?\n"?>
/**
 * [[機能説明]]
 * 
 * @package    <?=$package != '' ? $package."\n" : "[[パッケージ名]]\n"?>
 * @subpackage Filter
<?include('_doc_comment.skeleton.php');?>
 */
class <?=$class_name?> extends Samurai_Filter
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

