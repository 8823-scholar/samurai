<?php
/**
 * add - component_usage
 * 
 * add-component::usage
 */
?>

Add component file.
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>

Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error . "\n"; ?>
<?php } ?>
<?php } ?>

Usage:
    $ samurai add-component [component_name] [options]

Options:
    --model                             If model component. extends Samurai_Model
    --usage  -[uU]                      Show Usage.
    --samurai-dir                       Set Samurai_Dir.

Examples:
    $ samurai add-component foo_bar_zoo
        ->   [component_dir]/foo/bar/Zoo.class.php


