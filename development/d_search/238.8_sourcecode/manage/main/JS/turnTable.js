// Created 2006-11-07
function checkNum(obj){
	var page;
	page=document.getElementById(obj).value;
	var re=/\D/;
	if(re.test(page)){
		alert("Please input a Num!");
		document.getElementById(obj).focus();
		return false;
	}
	else if(page==""){
		return false;
	}
	return true;
}

function goPage(thisPage,inputOhj){
	if(checkNum(inputOhj)){
		location.href=thisPage+"?page="+document.getElementById(inputOhj).value;
	}
}

function openWithSubwindow(url,width,height,top,left){
	var d=new Date();
	window.open(url,"newpage"+d.getSeconds(),"width="+width+",height="+height+",top="+top+",left="+left+",toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no,fullscreen=2");
}

function ClickDel(url){
	if(confirm("Are you sure Delete£¿")){
		location.href=url;
	}
}