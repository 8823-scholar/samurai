<?php
/**
 * add/project_usage.php
 * 
 * add-project::usage
 */
?>

add project.
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>

Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error . "\n"; ?>
<?php } ?>
<?php } ?>

Usage:
    $ samurai add-project [project_name] [options]

Options:
    --renderer=[smarty|simple|phptal]       Set main renderer. (default: smarty)
    --usage, -[uU]                          Show usage.
    --samurai-dir=[path/to/dir]             Set samurai dir.

