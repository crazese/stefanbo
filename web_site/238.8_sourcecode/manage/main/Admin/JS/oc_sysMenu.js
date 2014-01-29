/*
Javascript for oc web admin system
author:shemily
created:2007-03-11
*/
var lastShow_MenuId;
var lastShow_topMenu;

function showTopMenu(menuId){
	/*--change menu back goround color--*/
	try{
		var lastMenuObj=document.getElementById(lastShow_MenuId);
		lastMenuObj.style.backgroundColor="";
	}catch(exception){}
	
	lastShow_MenuId="ocTopMenu" + menuId;
	var menuObj=document.getElementById("ocTopMenu" + menuId);
	menuObj.style.backgroundColor="#BDCBE7";
	/*--change menu back goround color--*/
	
	/*--show top menu--*/
	try{
		var firstTopMenuObj=document.getElementById("topmenushow1");
		firstTopMenuObj.style.display="none";
		var lastTopMenuObj=document.getElementById(lastShow_topMenu);
		lastTopMenuObj.style.display="none";
	}catch(exception){}
	
	lastShow_topMenu="topmenushow" + menuId;
	var topMenuObj=document.getElementById("topmenushow" + menuId);
	topMenuObj.style.display="block";
	/*--show top menu--*/
}

function showSubMenu(menuId){
	try{
		var subMenuObj=document.getElementById("menushow" + menuId);
		var subImgObj=document.getElementById("topimg" + menuId);
		if(subMenuObj.style.display=="block"){
			subMenuObj.style.display="none";
			subImgObj.src="images/menu/arrow_show.gif";
		}else{
			subMenuObj.style.display="block";
			subImgObj.src="images/menu/arrow_top.gif";
		}
		
	}catch(exception){}
}