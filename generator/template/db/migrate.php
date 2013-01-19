<?php
/**
 * db - migration.php
 */
?>

Database migration.
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>

Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error . "\n"; ?>
<?php } ?>
<?php } ?>

Usage:
    $ samurai db-migrate [options]
    
Options:
    --usage, -[uU]                      Show Usage.
    --samurai-dir=[path/to/dir]         Set Samurai Dir.

Other See:
    $ samurai add-migration --usage
    
