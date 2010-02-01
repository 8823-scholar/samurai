#
# SamuraiFW用のメインコンテナ設定ファイル
#

## FWを構成するメインインスタンスの設定
Renderer:
    class : Samurai_Renderer_<?php echo ucfirst($renderer_name) . "\n" ?>
    initMethod:
        name : init
        args : ['config/renderer/<?php echo $renderer_name ?>.php']

