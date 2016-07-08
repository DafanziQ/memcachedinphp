# memcachedinphp
###这里有三个文件，hashmemcached.php是自己模拟写的一个关于memcached一致性hash的算法；
###其余的两个文件是和memcached集群实现一致性hash的数据库脚本、nginx配置代码和一个php文件（过程是，nginx直接访问memcached，看能否查到键，####如果查不到。就去访问callback.php去数据库访问，并把查到的结果放进memcached。）
