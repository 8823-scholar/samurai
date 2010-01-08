<?
/**
 * add/action_usage.php
 * 
 * add-actionの説明
 */
?>
<? $errors = $this->Error->getMessages() ?>
<? if($errors){ ?>
Error:
<? foreach($errors as $error){ ?>
 - <?=$error?>
<? } ?>

<? } ?>
Usage:
    samurai add-action [action_name] [options]
Options:
    --usage  -[uU]  Show Usage.
    --samurai_dir   Set Samurai_Dir.
    --template      add-template, same time.


