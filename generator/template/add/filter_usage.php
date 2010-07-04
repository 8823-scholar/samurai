<?php
/**
 * add - component_usage
 * 
 * add-filter::usage
 */
?>
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>
Error:
<?php foreach($errors as $error){ ?>
 <?php echo $error; ?>
<?php } ?>

<?php } ?>
Usage:
    samurai add-filter [filter_name] [options]
Options:
    --usage  -[uU]  Show Usage.
    --samurai_dir   Set Samurai_Dir.
Examples:
    samurai add-filter example
        ->   [component_dir]/filter/Example.class.php


