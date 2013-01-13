<?php
/**
 * spec.php
 */
?>

Execute specs.
You can choice test runner "PHPSpec" or "PHPUnit" or others.

Spec files:
    spec/Initialization.php             For spec initialization.
    spec/*.spec.php                     Spec files for PHPSpec.
    spec/*.unit.php                     Spec files for PHPUnit.

Usage:
    # execute all specs.
    $ samurai spec

    # execute unit spec.
    $ samurai spec spec/path/to/Spec.spec.php

    # execute all specs in directory.
    $ samurai spec spec/path/to/directory
    
Options:
    --runner=[phpspec|phpunit]          Set runner. (default: phpspec)
    --usage, -[uU]                      Show Usage.

See Other:
    $ samurai add-spec --usage
    
