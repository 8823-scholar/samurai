<?php
/**
 * add - component_usage
 * 
 * add-filter::usage
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
    samurai add-filter [filter_name] [options]
Options:
    --usage  -[uU]  Show Usage.
    --samurai_dir   Set Samurai_Dir.
Examples:
    samurai add-filter example
        ->   [component_dir]/filter/Example.class.php


