<?php
/**
 * add - migration.php
 */
?>

For add migration files.
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>

Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error . "\n"; ?>
<?php } ?>
<?php } ?>

Usage:
    $ samurai add-migration [migration_name] [options]
    
Options:
    --usage, -[uU]                      Show Usage.
    --samurai-dir=[path/to/dir]         Set Samurai Dir.

Example:
    $ samurai add-migration create_user_table
        -> db/migrate/<?php echo date('YmdHis')?>_create_user_table.php
    
