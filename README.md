# memcachedinphp

#####这里有两个文件，hashmemcached.php是自己模拟写的一个关于memcached一致性hash的算法；
#####里面的callback.php就是用来nginx直接在memcached查找不到key-value的时候要使用的php文件。这个文件一是从数据库查找到数据，另外把查到的数据写进memcached



_ _ _


**注意因为要使用nginx 的一致性hash的模块，所以要重新编译nginx给他添加ngx_http_upstream_consistent_hash模块**
```
wget https://github.com/replay/ngx_http_consistent_hash
tar -zxvf master(就是上一条命令下载下来的压缩文件)
cd ngx_http_consistent_hash-master
./configure --prefix=/usr/local/nginx/ --add-module=/usr/local/src/ngx_http_consistent_hash-master/(
以后重新编译任何软件时都要先去查看它原来编译的参数，nginx的使用./memcached -V,我们发现这里它原先只有--prefix=/usr/local/nginx/，我们重新编译的时候把它也要写上。此时如果你不停止nginx的话，这条命令处了编译出一个新的nginx外，还会把旧的保留，并加了一个后缀.old)
make && make install
```

**然后准备数据库的内容**
```
create database test;
use test;
create table user(  uid int, uname varchar(20) );
insert user values(1,"zhangsan"),(2,"lisi"),(3,"wangwu"),(4,"zhaoliu");
```


**修改niginx的配置文件nginx.conf**
```

   upstream mcserver{
     consistent_hash $request_uri;
     server localhost:11211;
     server localhost:11212;
     server localhost:11213;
    }
   server{
        listen       80;
        server_name  localhost;
        location / {
            root   html;
            memcached_pass mcserver;
            error_page 404 /callback.php;
            }
        location ~ \.php$ {
            root           html;
            fastcgi_pass   127.0.0.1:9000;
            et $memcached_key $request_uri;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
            }
        }



```
