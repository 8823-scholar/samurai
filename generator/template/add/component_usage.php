<?
/**
 * add - component_usage
 * 
 * add-component::usage
 */
?>
<? $errors = $this->Error->getMessages(); ?>
<? if($errors){ ?>
Error:
<? foreach($errors as $error){ ?>
 <?=$error?>
<? } ?>

<? } ?>
Usage:
    samurai add-component [component_name] [options]
Options:
    --usage  -[uU]  Show Usage.
    --samurai_dir   Set Samurai_Dir.
Examples:
    samurai add-component example_example1_user
        ->   [component_dir]/example/example1/User.class.php


