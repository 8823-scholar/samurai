<?
/**
 * error/input.tpl
 * 
 * 入力エラー
 */
?>
ERROR!!
<? $errors = $this->Error->getMessages() ?>
<? if($errors){ ?>
Error:
<? foreach($errors as $error){ ?>
 - <?=$error?>
<? } ?>

<? } ?>

