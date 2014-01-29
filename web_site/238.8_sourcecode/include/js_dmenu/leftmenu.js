stBM(2,"tree003e",[1,"/images/images_dmenu","","blank.gif",0,"left","default","hand",1,0,-1,-1,-1,"none",0,"#000000","transparent","","repeat",0,"defButton_f%20.gif","defButton_uf.gif",9,9,0,"line_def0.gif","line_def1.gif","line_def2.gif","line_def3.gif",0,0,0,0,"center",0,0]);
stBS("p0",[0,0]);
function hightlight(name)
{
   var menu=_STJS.getStructByName(name);
     if(menu&&menu.className=="UITree")
     {
        for(var j=menu.subTrees.length-1;j>=0;j--)
               for(var k=0;k<menu.subTrees[j].nodes.length;k++)
                {
                     var n=menu.subTrees[j].nodes[k];
                      if(n.link!="#_nolink"&&_STJS.getABS(n.link)==window.location)
                      { 
                               n.select();
                               stExpandSubTree(name,n.pSubTreeId);
                                  return;
                       }
                 }
      }
}

_STJS.loads.push(new Function("hightlight('tree003e')"));

stIT("p0i0",["硕思闪客精灵","/product/flashdecompiler/index.htm","_self","","","","",18,18,"bold 9pt 'Arial'","#000000","none","transparent","bg_menu1.gif","no-repeat","bold 9pt 'Arial'","#000000","underline","transparent","bg_menu1.gif","no-repeat","bold 9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","bold 9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stBS("p1",[],"p0");
stIT("p1i0",["产品简介","/product/flashdecompiler/index.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i1",["主要特性","/product/flashdecompiler/features.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i2",["免费下载","/product/flashdecompiler/download.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i3",["在线定购","/shopping/?ProductID=1","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i4",["产品截图","/product/flashdecompiler/screenshot.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i5",["所获奖项","/product/flashdecompiler/awards.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i6",["用户评价","/product/flashdecompiler/review.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i7",["版本历史","/product/flashdecompiler/whatsnew.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i8",["常见问题","/product/flashdecompiler/faq.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i9",["升级方法","/product/flashdecompiler/upgrade.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stIT("p1i10",["客户支持","/support/support.htm","_self","","","","",18,18,"9pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","9pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat","9pt 'Arial'","#FFFFFF","none","transparent","bg_menu2.gif","no-repeat",1,0,"left","middle",0,0]);
stES();
stIT("p0i1",["","","_self","","","","",177,1,"bold 1pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","bold 1pt 'Arial'","#000000","underline","#FFFFFF","","no-repeat","bold 1pt 'Arial'","#000000","none","#FFFFFF","","no-repeat","bold 1pt 'Arial'","#000000","none","#FFFFFF","","no-repeat",1,0,"left","middle",0,0]);
stIT("p0i2",["硕思闪客之锤","/product/swfquicker/index.htm",,,,,,18,18,"bold 9pt 'Arial'",,,"transparent","bg_menu1.gif",,"bold 9pt 'Arial'",,,"transparent","bg_menu1.gif",,"bold 9pt 'Arial'","#FFFFFF",,"transparent","bg_menu2.gif",,"bold 9pt 'Arial'","#FFFFFF",,"transparent","bg_menu2.gif"],"p0i1");
stBS("p2",[],"p0");
stIT("p2i0",["产品简介",,,,,,,,,"9pt 'Arial'",,,"#FFFFFF","",,"9pt 'Arial'",,,"#FFFFFF","",,"9pt 'Arial'",,,,,,"9pt 'Arial'"],"p0i2");
stIT("p2i1",["主要特性","/product/swfquicker/features.htm"],"p2i0");
stIT("p2i2",["免费下载","/product/swfquicker/download.htm"],"p2i0");
stIT("p2i3",["在线定购","/shopping/?ProductID=2"],"p2i0");
stIT("p2i4",["产品截图","/product/swfquicker/screenshot.htm"],"p2i0");
stIT("p2i5",["所获奖项","/product/swfquicker/awards.htm"],"p2i0");
stIT("p2i6",["用户评价","/product/swfquicker/review.htm"],"p2i0");
stIT("p2i7",["范例欣赏","http://www.sothink.com/product/swfquicker/samples/index.htm"],"p2i0");
stIT("p2i8",["用户作品","http://www.sothink.com/product/swfquicker/customers.htm"],"p2i0");
stIT("p2i9",["版本历史","/product/swfquicker/whatsnew.htm"],"p2i0");
stIT("p2i10",["常见问题","/product/swfquicker/faq.htm"],"p2i0");
stIT("p2i11",["教程中心","http://www.sothink.com/product/swfquicker/tutorial.htm"],"p2i0");
stIT("p2i12",["客户支持","/support/support.htm"],"p2i0");
stIT("p2i13",["升级方法","/product/swfquicker/upgrade.htm"],"p2i0");
stES();
stIT("p0i3",[,,,,,,,1],"p0i1");
stIT("p0i4",["硕思闪客巫师","/product/swfeasy/index.htm"],"p0i2");
stBS("p3",[],"p0");
stIT("p3i0",[,"/product/swfeasy/index.htm"],"p2i0");
stIT("p3i1",[,"/product/swfeasy/features.htm"],"p2i1");
stIT("p3i2",[,"/product/swfeasy/download.htm"],"p2i2");
stIT("p3i3",[,"/shopping/?ProductID=3"],"p2i3");
stIT("p3i4",[,"/product/swfeasy/screenshot.htm"],"p2i4");
stIT("p3i5",[,"/product/swfeasy/awards.htm"],"p2i5");
stIT("p3i6",[,"/product/swfeasy/review.htm"],"p2i6");
stIT("p3i7",[,"http://www.sothink.com/product/swfeasy/samples/index.htm"],"p2i7");
stIT("p3i8",[,"http://www.sothink.com/product/swfeasy/customers.htm"],"p2i8");
stIT("p3i9",[,"/product/swfeasy/whatsnew.htm"],"p2i9");
stIT("p3i10",[,"/product/swfeasy/faq.htm"],"p2i10");
stIT("p3i11",[,"http://www.sothink.com/product/swfeasy/tutorial.htm"],"p2i11");
stIT("p3i12",[],"p2i12");
stIT("p3i13",[,"/product/swfeasy/upgrade.htm"],"p2i13");
stES();
stIT("p0i5",[],"p0i3");
stIT("p0i6",["硕思魔法菜单","/product/dhtmlmenu/index.htm"],"p0i2");
stBS("p4",[],"p0");
stIT("p4i0",[,"/product/dhtmlmenu/index.htm"],"p2i0");
stIT("p4i1",[,"/product/dhtmlmenu/features.htm"],"p2i1");
stIT("p4i2",[,"/product/dhtmlmenu/download.htm"],"p2i2");
stIT("p4i3",[,"/shopping/?ProductID=4"],"p2i3");
stIT("p4i4",[,"/product/dhtmlmenu/screenshot.htm"],"p2i4");
stIT("p4i5",[,"/product/dhtmlmenu/samples/index.htm"],"p2i7");
stIT("p4i6",[,"http://www.sothink.com/product/dhtmlmenu/customers.htm"],"p2i8");
stIT("p4i7",[,"/product/dhtmlmenu/awards.htm"],"p2i5");
stIT("p4i8",[,"/product/dhtmlmenu/review.htm"],"p2i6");
stIT("p4i9",[,"/product/dhtmlmenu/whatsnew.htm"],"p2i9");
stIT("p4i10",[,"/product/dhtmlmenu/faq.htm"],"p2i10");
stIT("p4i11",[,"http://www.sothink.com/tutorials/index.php?c=3&sid=e3a5533e6644a4d013beb08adfc10a52"],"p2i11");
stIT("p4i12",["疑难解答","http://www.sothink.com/product/dhtmlmenu/troubleshooter/index.htm"],"p2i0");
stIT("p4i13",[,"/product/dhtmlmenu/upgrade.htm"],"p2i13");
stES();
stIT("p0i7",[],"p0i3");
stIT("p0i8",["FVEC","/product/fvec/index.htm"],"p0i2");
stBS("p5",[],"p0");
stIT("p5i0",[,"/product/fvec/index.htm"],"p2i0");
stIT("p5i1",["在线演示","http://flashvideo.sothinkmedia.com/flash-video-encoder-command-line/"],"p2i0");
stIT("p5i2",[,"/product/fvec/download.htm"],"p2i2");
stIT("p5i3",["视频分享解决方案","/product/fvec/solution.htm"],"p2i0");
stIT("p5i4",[,"/product/fvec/order.htm"],"p2i3");
stIT("p5i5",["许可信息","/product/fvec/license.htm"],"p2i0");
stIT("p5i6",["用户手册","/product/fvec/help.htm"],"p2i0");
stIT("p5i7",[,"/product/fvec/awards.htm"],"p2i5");
stIT("p5i8",[,"/product/fvec/whatsnew.htm"],"p2i9");
stES();

stEM();
