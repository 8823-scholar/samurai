<?php
/**
 * add - component_usage
 * 
 * add-component::usage
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
    samurai add-component [component_name] [options]
Options:
    --usage  -[uU]  Show Usage.
    --samurai_dir   Set Samurai_Dir.
Examples:
    samurai add-component example_example1_user
        ->   [component_dir]/example/example1/User.class.php


