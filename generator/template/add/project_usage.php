<?php
/**
 * add/project_usage.php
 * 
 * add-project::usage
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
    samurai add-project [project_name] [options]
Options:
    --usage  -[uU]                Show usage.
    --samurai_dir=[samurai_dir]   Set samurai dir.
    --renderer=[renderer]         Set main renderer

