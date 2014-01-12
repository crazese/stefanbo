#!/bin/bash
while Trun
do
clear
echo "———————-menu————————"
echo -e "\033[49;32;1m(1) 重启\033[49;35;1miptables\033[0m "
echo -e "\033[49;32;1m(2) 添加\033[49;35;1miptables\033[49;32;1m规则\033[0m "
echo -e "\033[49;32;1m(3) 删除\033[49;35;1miptables\033[49;32;1m规则\033[0m "
echo -e "\033[49;32;1m(4) 关闭\033[49;35;1miptables\033[0m "
echo -e "\033[49;32;1m(5) 保存\033[49;35;1miptables\033[49;32;1m规则(输完ACL后要记得保存和查看)\033[0m "
echo -e "\033[49;32;1m(6) 查看\033[49;35;1miptables\033[49;32;1m状态\033[0m "
echo -e "\033[49;32;1m(7) 添加\033[49;35;1miptables\033[49;32;1m控制列表\033[0m "
echo -e "\033[49;32;1m(0) 退出脚本\033[0m "
echo "————————————————————"
echo -en "\033[49;31;1m 请输入数字0-7:  \033[0m"
read num
if [[ "${num}" =~ "^$" ]];
then
echo -e "\033[49;31;5m 请输入0-7中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${num}" =~ "^[a-zA-Z]+$" ]];
then
echo -e "\033[49;31;5m 请输入0-7中的数字!或者Ctrl+C退出! \033[0m"
else
#if [ ${num} -lt 0 -o ${num} -gt 7 ]
if [[ "${num}" =~ "[8-9]" ]];
then
echo -e "\033[49;31;5m 请输入0-7中的数字!或者Ctrl+C退出! \033[0m"
else
if [ "${num}" == "1" ]
then
service iptables restart&
else
if [ "${num}" == "2" ]
#######################################################
then
while Trun
do
clear
echo "———————-add ACL———————"
echo -e "\033[49;32;1m(1) 针对源\033[49;35;1mIP\033[49;32;1m放行添加\033[0m "
echo -e "\033[49;32;1m(2) 针对服务器\033[49;35;1m端口\033[49;32;1m放行添加 \033[0m "
echo -e "\033[49;32;1m(3) 针对有\033[49;35;1mIP和端口\033[49;32;1m的规则添加\033[49;35;1m（这里要参数IP和端口 例如:1.1.1.1/255.255.0.0 80）\033[0m "
echo -e "\033[49;32;1m(4) 自定义完整语句添加 \033[0m "
echo -e "\033[49;32;1m(0) 返回上一级 \033[0m "
echo "——————————————————————"
echo -en "\033[49;31;1m 请输入数字0-4: \033[0m"
read aclnum
if [[ "${aclnum}" =~ "^$" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${aclnum}" =~ "^[a-zA-Z]+$" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${aclnum}" =~ "[5-9]" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
elif [ "${aclnum}" == "1" ]
then
read ip
iptables -A INPUT -s ${ip} -p tcp -j ACCEPT
service iptables save
elif [ "${aclnum}" == "2" ]
then
read port
iptables -A INPUT -p tcp -s 0/0 --dport ${port} -j ACCEPT
service iptables save
elif [ "${aclnum}" == "3" ]
then
read ip port
iptables -A INPUT -p tcp -s ${ip} --dport ${port} -j ACCEPT
service iptables save
elif [ "${aclnum}" == "4" ]
then
read addacl
`${addacl}`
service iptables save
else
break
fi
fi
fi
echo -n "是否想继续添加,回车或Y继续，按N返回上一级！: [y/n]:"
read contine
if [ "${contine}" == "n" -o "${contine}" == "N" ]
then
break
fi
done
#######################################################
else
if [ "${num}" == "3" ]
then
while Trun
do
clear
echo "———————delete ACL———————-"
echo -e "\033[49;32;1m(1) 针对源\033[49;35;1mIP\033[49;32;1m删除\033[0m "
echo -e "\033[49;32;1m(2) 针对服务器\033[49;35;1m端口\033[49;32;1m删除 \033[0m "
echo -e "\033[49;32;1m(3) 针对有\033[49;35;1mIP和端口\033[49;32;1m的规则删除\033[49;35;1m（这里要参数IP和端口 例如:1.1.1.1/255.255.0.0 80）\033[0m "
echo -e "\033[49;32;1m(4) 自定义完整语句删除 \033[0m "
echo -e "\033[49;32;1m(0) 返回上一级 \033[0m "
echo "————————————————-"
echo -en "\033[49;31;1m 请输入数字0-4: \033[0m"
read aclnum
if [[ "${aclnum}" =~ "^$" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${aclnum}" =~ "^[a-zA-Z]+$" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${aclnum}" =~ "[5-9]" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
elif [ "${aclnum}" == "1" ]
then
read ip
iptables -D INPUT -s ${ip} -p tcp  -j ACCEPT
service iptables save
elif [ "${aclnum}" == "2" ]
then
read port
iptables -D INPUT -p tcp -s 0/0 --dport ${port} -j ACCEPT
service iptables save
elif [ "${aclnum}" == "3" ]
then
read ip port
iptables -D INPUT -p tcp -s ${ip} --dport ${port} -j ACCEPT
service iptables save
elif [ "${aclnum}" == "4" ]
then
read deleteacl
`${deleteacl}`
service iptables save
else
break
fi
fi
fi
echo -n "是否想继续删除,回车或Y继续，按N返回上一级！: [y/n]:"
read contine
if [ "${contine}" == "n" -o "${contine}" == "N" ]
then
break
fi
done
###################################################################
else
if [ "${num}" == "4" ]
then
echo -e "`service iptables stop&` "
else
if [ "${num}" == "5" ]
then
echo -e "`service iptables save&`"
else
if [ "${num}" == "6" ]
then
echo -e "`service iptables status&`"
else
##################################################################
if [ "${num}" == "7" ]
then
while Trun
do
clear
echo "———————list ACL———————-"
echo -e "\033[49;32;1m(1) 看当前正在使用的规则集 \033[0m "
echo -e "\033[49;32;1m(2) 查看每个策略或每条规则、每条链的简单流量统计\033[0m "
echo -e "\033[49;32;1m(3) 查看NAT表 \033[0m "
echo -e "\033[49;32;1m(4) 自定义查看 \033[0m "
echo -e "\033[49;32;1m(0) 退回上一级\033[0m "
echo "————————————————-"
echo -en "\033[49;31;1m 请输入数字0-4: \033[0m"
read aclnum
if [[ "${aclnum}" =~ "^$" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${aclnum}" =~ "^[a-zA-Z]+$" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
else
if [[ "${aclnum}" =~ "[5-9]" ]];
then
echo -e "\033[49;31;5m 请输入0-4中的数字!或者Ctrl+C退出! \033[0m"
elif [ "${aclnum}" == "1" ]
then
iptables -L
elif [ "${aclnum}" == "2" ]
then
iptables -L -n -v
elif [ "${aclnum}" == "3" ]
then
iptables -L -t nat
elif [ "${aclnum}" == "4" ]
then
read listacl
`${listacl}`
else
break
fi
fi
fi
echo -n "是否想继续查看,回车或Y继续，按N返回上一级！: [y/n]:"
read contine
if [ "${contine}" == "n" -o "${contine}" == "N" ]
then
break
fi
done
################################################
else
exit
fi
fi
fi
fi
fi
fi
fi
fi
fi
fi
echo -n "按回车或者Y返回上一级，按N退出程序！[y/n]:"
read contine
if [ "${contine}" == "n" -o "${contine}" == "N" ]
then
exit
fi
done