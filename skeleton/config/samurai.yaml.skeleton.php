#
# SamuraiFW用の設定ファイル
#

## ログディレクティブ
loggers:
    display:
        enable : true
        client : SimpleDisplay
        log_level : warn
    simple:
        enable : true
        client : SimpleFile
        log_level : debug
        logfile : 'log/<?php echo $project_name ?>.log'
    mail:
        enable : false
        client : Mail
        log_level : warn
        from : 'alert@samurai.example.jp'
        subject : 'Samurai Alert'
        mail:
            - foo@samurai.example.jp
    php:
        enable : true
        client : PhpErrorHandler

