#
# SamuraiFW用のメインコンテナ設定ファイル
#

## FWを構成するメインインスタンスの設定
Renderer:
    class : Samurai_Renderer_<?=ucfirst($renderer_name)."\n"?>
    initMethod:
        name : init
        args : ['config/renderer/<?=$renderer_name?>.php']

