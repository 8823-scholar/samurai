<?php
/**
 * add - spec.php
 */
?>

For add spec files.
Default runner is "PHPSpc".
<?php $errors = $this->Error->getMessages(); ?>
<?php if($errors){ ?>

Error:
<?php foreach($errors as $error){ ?>
 - <?php echo $error . "\n"; ?>
<?php } ?>
<?php } ?>

Usage:
    $ samurai add-spec [spec_name] [options]
    
Options:
    --runner=[phpspec|phpunit]          Set runner. (default: phpspec)
    --description=[text]                Description text.
    --usage, -[uU]                      Show Usage.
    --samurai-dir=[path/to/dir]         Set Samurai Dir.

Example:
    $ samurai add-spec foo_bar_zoo
        -> spec/foo/bar/Zoo.spec.php is created. (use PHPSpec)

    $ samurai add-spec foo_bar_zoo --runner=phpunit
        -> spec/foo/bar/Zoo.unit.php is created. (use PHPUnit)

    # spec name is enable multiple.
    $ samurai add-spec foo_bar_zoo1 foo_bar_zoo2
        -> spec/foo/bar/Zoo1.spec.php
        -> spec/foo/bar/Zoo2.spec.php
    
