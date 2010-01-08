<?
/**
 * add/template_usage.tpl
 * 
 * add-template Usage.
 */
?>
<? $errors = $this->Error->getMessages(); ?>
<? if($errors){ ?>
Error:
<? foreach($errors as $error){ ?>
 - <?=$error?>
<? } ?>

<? } ?>
Usage:
    samurai add-template [template_name] [options]
Options:
    --usage  -[uU]  Show Usage.
    --samurai_dir   Set Samurai_Dir.
    --cli           skeleton 4 client.
Attention:
    [template_name] => example/example1.tpl
    Please, input like up-writing to "[template_name]".


