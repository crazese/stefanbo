<?
//树型目录结构模板程序
//菜单目录库字段说明：
//menu_id 菜单项目 id
//menu 菜单名称
//menu_grade 菜单等级 1 为主菜单 2 为二级菜单 ........
//menu_superior 上一级菜单 id 号

function my_menu($menu_content,$i,$menu_grade_temp,$menu_superior_temp)
{
global $PHP_SELF;
$temp1=$menu_grade_temp+1;
$menu_superior_temp_array=split("/",$menu_superior_temp);
for ($t=0;$t<$i;$t++)
{
$menu_array=split("/",$menu_content[$t]);
If(($menu_array[2]==$menu_grade_temp)&&($menu_array[3]==$menu_superior_temp_array[$menu_grade_temp-1]))
{
for($p=1;$p<=$menu_grade_temp;$p++){echo ">";} //显示目录前的等级箭头
$temp3=$menu_superior_temp_array;
$temp3[$menu_grade_temp]=$menu_array[0];
$temp2=implode("/",$temp3);
if ($menu_array[0]==$menu_superior_temp_array[$temp1-1])
{
$temp5=$temp1-1;
$temp3[$menu_grade_temp]="";
$temp6=implode("/",$temp3);
echo "<a href=http://localhost/$PHP_SELF?menu_grade_temp=".$temp5."&menu_superior_temp=$temp6\">$menu_array[1]</a><br>";
my_menu($menu_content,$i,$temp1,$temp2);
}
else
{
$temp3[$menu_grade_temp+1]="";
$temp6=implode("/",$temp3);
echo "<a href=http://localhost/$PHP_SELF?menu_grade_temp=".$temp1."&menu_superior_temp=$temp6\">$menu_array[1]</a><br>";
}
}
}
}
// 连接 MySql 数据库
$db_host="192.168.0.3";
$db_user="webtest";
$db_password="batian";
$db_name="test";
mysql_connect($db_host,$db_user,$db_password);
mysql_select_db($db_name);

//从数据库中取得数据
$query_string="select * from menu order by menu_grade";
$db_data=mysql_query($query_string);

//第一次执行初始化
if ($menu_grade_temp=="")
{
$menu_superior_temp=0;
}

//将所有的信息读入数组，并统计数组个数
$i=0;
while (list($menu_id,$menu,$menu_grade,$menu_superior)=mysql_fetch_row($db_data))
{
$menu_content[$i]=$menu_id."/".$menu."/".$menu_grade."/".$menu_superior;
$i++;
}
my_menu($menu_content,$i,1,$menu_superior_temp);
?>
