#!/bin/bash

# 先清屏
clear

printf "执行脚本中"
for i in 1 2 3 4 
do
	sleep 0.7
	printf "."
done
sleep 0.7
echo "."
sleep 0.7


printf "=======================================================================\n"
printf "| 欢迎使用计网大作业服务器端脚本！                                    |\n"
printf "|                                                                     |\n"
printf "| 作者：郭崇山                                                        |\n"
printf "| 日期：2019/6/18                                                     |\n"
printf "|                                                                     |\n"
printf "| 启用服务端请输入：start                                             |\n"
printf "| 关闭服务端请输入：stop                                              |\n"
printf "|                                                                     |\n"
printf "| 请使用sudo su root进入root用户后再使用该脚本！                      |\n"
printf "| 按Ctrl + c退出脚本                                                  |\n"
printf "=======================================================================\n"


# state变量代表server状态，1表示开启，0表示关闭
state=0 # 在bash中等号两边不能加空格，而且变量存放的只能是字符串
# 死循环检查服务开启和关闭
while true
do
	read -p "请输入命令：" command
	if [ $command == "start" ]
	then
		if [ $state == 0 ]
		then
			php server_tcp.php & # 允许后台执行
			php server_udp.php &
			state=1 # 变量赋值不能有空格
			echo "server start"
		else
			echo "服务器已打开，无需再次启动！"
		fi
	elif [ $command == "stop" ]
	then
		if [ $state == 1 ]
		then
			# 杀死8080端口的tcp和udp进程
			fuser -s -k -n tcp 8080
			fuser -s -k -n udp 8080
			state=0 # 变量赋值不能有空格
			echo "server stop"
		else
			echo "服务器已关闭，无需再次关闭！"
		fi
	else
		echo "错误的命令！"
	fi
done
