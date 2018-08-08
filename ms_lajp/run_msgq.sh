#!/bin/sh

# -----------------------------------------------------------
#  LAJP-Java Message Queue Service 启动脚本 
#		
# 		(2009-10 http://code.google.com/p/lajp/)
#  
# -----------------------------------------------------------

# java服务中需要的jar文件或classpath路径，如业务程序、第三方jar文件log4j等
export classpath=./lajp-10.05.jar:./test_service

# 自动启动类和方法，LAJP服务启动时会自动加载并执行
# export AUTORUN_CLASS=com.foo.AutoRunClass
# export AUTORUN_METHOD=AutoRunMethod

# 字符集设置  GBK|UTF-8
# export CHARSET=UTF-8

# LAJP服务启动指令(前台)
java -classpath $classpath lajp.PhpJava

# LAJP服务启动指令(后台)
# nohup java -classpath $classpath lajp.PhpJava &

