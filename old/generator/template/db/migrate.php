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
    --redo                              migration redo. (default: --step=1)
    --step=[number]                     number of step. (used by redo)
    --usage, -[uU]                      Show Usage.
    --samurai-dir=[path/to/dir]         Set Samurai Dir.

Example:
    # redo
    $ samurai db-migrate --redo

    # redo from before 2 step.
    $ samurai db-migrate --redo --step=2

Other See:
    $ samurai add-migration --usage
    
