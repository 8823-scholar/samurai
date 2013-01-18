#
# config/database/<?php echo $env ?>.yml
#
# [example: simple]
# alias:
#     dsn: '[driver]://[user]:[password]@[host]/[dbname]'
#
# [example: detail]
# alias:
#     dsn: '[driver]://[user]:[password]@[host]/[dbname]'
#     slaves:
#         slave1:
#             dsn: '[driver]://[user]:[password]@[host]/[dbname]'
#     charset: utf8
#     collate: utf8_general_ci
#     conf: 'config/database/conf/alias.yml'
#

base:
    dsn: 'mysql://user:password@localhost/dbname_<?php echo $env ?>'

