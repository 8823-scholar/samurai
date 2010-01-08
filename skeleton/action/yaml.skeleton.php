#
# <?=join(' - ', $action_names)?>.yml
#

<? if($global){ ?>
#Convert:
#    * : 'trim'
#    
#Validate:
#    
<? } ?>
<? if($action){ ?>
View:
    success : '<?=$template?>'
<? } ?>

