// JavaScript Document
var xmlHttp;
function createXMLHttpRequest() 
{ 
if(window.XMLHttpRequest) 
{ 
xmlHttp=new XMLHttpRequest();
}
else if(window.ActiveXObject) 
{ 
try
{ 
xmlHttp=new ActiveX0bject("Msxml2.XMLHTTP");
} 
catch(e) 
{} 
try
{ 
xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
}
catch(e)
{} 
if(!xmlHttp) 
{ 
window.alert("Can't create XMLHttpRequest"); 
return false; 
} 
} 
}
function startRequest(url) 
{ 
createXMLHttpRequest();
xmlHttp.open("GET",url,true); 
xmlHttp.onreadystatechange=handleStateChange; 
xmlHttp.send(null); 
//startRequest2();
} 

function handleStateChange() 
{ 
if(xmlHttp.readyState==4) 
{
if(xmlHttp.status==200) 
{   
  document.getElementById('showimage').src = "/images/checkcode/check.php?code="+xmlHttp.responseText;
  //alert("http://www.sothink.com/support/comment/checknum.php?code="+xmlHttp.responseText);
} 
} 
}

function showElementById(id)
{
	document.getElementById(id).style.display="";
}