#
# <?php echo join(' - ', $action_names) ?>.yml
#

<?php if($global){ ?>
#Convert:
#    *: 'trim'
#    
#Validate:
#    
<?php } ?>
<?php if($action){ ?>
View:
    success: '<?php echo $template ?>'
<?php } ?>

