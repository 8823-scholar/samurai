<?php
/**
 * error/input.tpl
 * 
 * 入力エラー
 */
?>
ERROR!!
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>
Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error; ?>
<?php } ?>

<?php } ?>

