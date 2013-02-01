#
# .samurai
# 
# generator用設定ファイル。
# generatorを利用する際に、各プロジェクトディレクトリごとに設定を変えたい場合に
# このファイルを利用してください。
#

generator:
    renderer:
        name : '<?php echo $renderer_name ?>'
        suffix : '<?php echo $renderer_suffix ?>'
#    generator:
#        package   : 'Package'
#        author    : 'Foo Bar <foo@bar.jp>'
#        copyright : 'Foo Project'
#        license   : 'http://www.php.net/license/3_01.txt The PHP License, version 3.01'
#    encoding:
#        config   : UTF-8
#        script   : UTF-8
#        template : UTF-8
#        skeleton : UTF-8
#        output   : UTF-8
#    directory:
#        component : 'component'
#        action    : 'component/action'
#        template  : 'template'
#        migration : 'db/migrate'
#        spec      : 'spec'
#        skeleton  : 'skeleton'
#        www       : 'public_html'
#    action:
#        config_file : samurai.yml
#        dicon_file  : samurai.dicon

