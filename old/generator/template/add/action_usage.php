<?php
/**
 * add/action_usage.php
 * 
 * add-actionの説明
 */
?>

<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>
Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error; ?>
<?php } ?>

<?php } ?>
This command is make action files.

Usage:
    $ samurai add-action [action_name] [options]

Options:
    --usage, -[uU]              Show Usage.
    --samurai_dir               Set Samurai_Dir.
    --no-template               Not add template.
    --cli                       For cli action.


