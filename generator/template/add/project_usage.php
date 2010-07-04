<?php
/**
 * add/project_usage.php
 * 
 * add-project::usage
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
    samurai add-project [project_name] [options]
Options:
    --usage  -[uU]                Show usage.
    --samurai_dir=[samurai_dir]   Set samurai dir.
    --renderer=[renderer]         Set main renderer

