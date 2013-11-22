//删除确认函数
function delconfirm(strTemp)
{
    if(strTemp == null) strTemp = "确定要删除吗？";
    if(confirm(strTemp))return true;
    return false;
}

function addInnerTags()
{
    var itemOriginal =document.getElementsByName("inner_start");
    var arr = new Array(itemOriginal.length);
    for(var j = 0; j < itemOriginal.length;j++){
        arr[j] = itemOriginal.item(j).value; }
    var itemOriginal1 =document.getElementsByName("inner_end");
    var arr1 = new Array(itemOriginal1.length);
    for(var j1 = 0; j1 < itemOriginal1.length;j1++){
        arr1[j1] = itemOriginal1.item(j1).value; }
    var str= ' <tr> <td> <input   type= "text "   name= "inner_start" size="21"> - <input   type= "text "   name= "inner_end" size="21"></td></tr><br>';
    document.getElementById("tags").innerHTML += str;
    var itemNew =document.getElementsByName("inner_start");
    for(var i=0;i<arr.length;i++){
        itemNew.item(i).value = arr[i];}
    var itemNew1 =document.getElementsByName("inner_end");
    for(var i1=0;i1<arr1.length;i1++){
        itemNew1.item(i1).value = arr1[i1];}
}
function addOutTags()
{
    var itemOriginal2 =document.getElementsByName("out_start");
    var arr2 = new Array(itemOriginal2.length);
    for(var j2 = 0; j2 < itemOriginal2.length;j2++){
        arr2[j2] = itemOriginal2.item(j2).value; }
    var itemOriginal3 =document.getElementsByName("out_end");
    var arr3 = new Array(itemOriginal3.length);
    for(var j3 = 0; j3 < itemOriginal3.length;j3++){
        arr3[j3] = itemOriginal3.item(j3).value; }
    var str1= '<tr> <td> <input   type= "text "   name= "out_start" size="21"> - <input   type= "text "   name= "out_end" size="21"></td></tr><br>';
    document.getElementById("tags1").innerHTML += str1;
    var itemNew2 =document.getElementsByName("out_start");
    for(var i2=0;i2<arr2.length;i2++){
        itemNew2.item(i2).value = arr2[i2];}
    var itemNew3 =document.getElementsByName("out_end");
    for(var i3=0;i3<arr3.length;i3++){
        itemNew3.item(i3).value = arr3[i3];}
}
function addInternetTags()
{
    var itemOriginal4 =document.getElementsByName("internet_start");
    var arr4 = new Array(itemOriginal4.length);
    for(var j4 = 0; j4 < itemOriginal4.length;j4++){
        arr4[j4] = itemOriginal4.item(j4).value; }
    var itemOriginal5 =document.getElementsByName("internet_end");
    var arr5 = new Array(itemOriginal5.length);
    for(var j5 = 0; j5 < itemOriginal5.length;j5++){
        arr5[j5] = itemOriginal5.item(j5).value; }
    var str2= ' <tr> <td> <input   type= "text "   name= "internet_start" size="21"> - <input   type= "text "   name= "internet_end" size="21"></td></tr><br>';
    document.getElementById("tags2").innerHTML += str2;
    var itemNew4 =document.getElementsByName("internet_start");
    for(var i4=0;i4<arr4.length;i4++){
        itemNew4.item(i4).value = arr4[i4];}
    var itemNew5 =document.getElementsByName("internet_end");
    for(var i5=0;i5<arr5.length;i5++){
        itemNew5.item(i5).value = arr5[i5];}
}
function addContactTags()
{
    var itemOriginal=document.getElementsByName("contact_name");
    var arr=new Array(itemOriginal.length);
    for(var i=0;i<itemOriginal.length;i++){
        arr[i]=itemOriginal.item(i).value;}
    var itemOriginal1=document.getElementsByName("contact_sex");
    var arr1=new Array(itemOriginal1.length);
    for(var i1=0;i1<itemOriginal1.length;i1++){
        arr1[i1]=itemOriginal1.item(i1).value;
    }
    var itemOriginal2=document.getElementsByName("phone");
    var arr2=new Array(itemOriginal2.length);
    for(var i2=0;i2<itemOriginal2.length;i2++){
        arr2[i2]=itemOriginal2.item(i2).value;
    }
    var itemOriginal3=document.getElementsByName("mail");
    var arr3=new Array(itemOriginal3.length);
    for(var i3=0;i3<itemOriginal3.length;i3++){
        arr3[i3]=itemOriginal3.item(i3).value;
    }
    var str3='联系人姓名：<input type="text" name="contact_name"><br>性别：<select name="contact_sex"><option value="MAN">男</option><option value="FEMALE">女</option></select><br>联系电话：<input type="text" name="phone"><br>email：<input type="text" name="mail"><br>';
    document.getElementById("tags3").innerHTML += str3;
    var itemNew =document.getElementsByName("contact_name");
    for(var i=0;i<arr.length;i++){
        itemNew.item(i).value = arr[i];}
    var itemNew1 =document.getElementsByName("contact_sex");
    for(var i1=0;i1<arr1.length;i1++){
        itemNew1.item(i1).value = arr1[i1];}
    var itemNew2 =document.getElementsByName("phone");
    for(var i2=0;i2<arr2.length;i2++){
        itemNew2.item(i2).value = arr2[i2];}
    var itemNew3 =document.getElementsByName("mail");
    for(var i3=0;i3<arr3.length;i3++){
        itemNew3.item(i3).value = arr3[i3];}

}
