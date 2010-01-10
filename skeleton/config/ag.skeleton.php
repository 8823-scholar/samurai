#
# ActiveGateway設定ファイル
#
# dsnは配列形式で複数記述する事によって、ランダムでどちらか一方を使用するようになります。
# 
# example
# <code>
#     alias :
#         dsn  : '[driver]://[user]:[password]@[host]/[dbname]'
#         conf : '/path/to/samurai/config/activegateway/conf/alias.yml'
# </code>

base:
    dsn : 'mysql://user:password@localhost/dbname'

## specなどで使用します
## specを利用する場合は必ず設定してください
sandbox:
    dsn : 'mysql://user:password@localhost/dbname'

