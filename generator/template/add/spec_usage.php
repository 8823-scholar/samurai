<?php
/**
 * add - spec.php
 */
?>

<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>
Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error; ?>
<?php } ?>

<?php } ?>
This command is make spec files.

Usage:
    $ samurai add-spec [spec_name]

Example:
    $ samurai add-spec foo_bar_zoo
        -> spec/foo/bar/Zoo.class.php is created.
    
Options:
    --usage, -[uU]              Show Usage.
    --samurai_dir               Set Samurai_Dir.
    
