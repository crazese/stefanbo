<style type='text/css'>
body {
     font-family:"Lucida sans unicode", sans-serif;
     font-size:12px;
     margin:0;
     padding:0;
     height:100%;
     }
#basis {
     display:inline;
     position:relative;
     }
#calender {
     position:absolute;
     top:30px;
     left:0;
     width:220px;
     background-color:#fff;
     border:3px solid #ccc;
     padding:10px;
     z-index:10;
     }
#control {
     text-align:center;
     margin:0 0 5px 0;
     }
#control select {
     font-family:"Lucida sans unicode", sans-serif;
     font-size:11px;
     margin:0 5px;
     vertical-align:middle;
     }
#calender .controlPlus {
     padding:0 5px;
     text-decoration:none;
     color:#333;
     }
#calender table {
     empty-cells: show;
     width:100%;
     font-size:11px;
     table-layout:fixed;
     }
#calender .weekdays td{
     text-align:right;
     padding:1px 5px 1px 1px;
     color:#333;
     }
#calender .week td {
     text-align:right;
     cursor:pointer;
     border:1px solid #fff;
     padding:1px 4px 1px 0;
     }
#calender .week .today { 
     background-color:#ccf;
     border-color:#ccf;
     }
#calender .week .holiday {
     font-weight: bold;
     }
#calender .week .hoverEle {
     border-color:#666;
     background-color:#99f;
     color:#000;
     }

</style>
<script  type="text/javascript">
     var allMonth=[31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
     var allNameOfWeekDays=["Mo","Tu", "Mi", "Do", "Fr", "Sa", "Su"];
     var allNameOfMonths=["Januar","Februar","March","April","May","June","July","August","September","Oktober","November","December"];
     var newDate=new Date();
     var yearZero=newDate.getFullYear();
     var monthZero=newDate.getMonth();
     var day=newDate.getDate();
     var currentDay=0, currentDayZero=0;
     var month=monthZero, year=yearZero;
     var yearMin=2000, yearMax=2010;
     var target='';
     var hoverEle=false;
     function setTarget(e){
          if(e) return e.target;
          if(event) return event.srcElement;
     }
     function newElement(type, attrs, content, toNode) {
          var ele=document.createElement(type);
          if(attrs) {
               for(var i=0; i<attrs.length; i++) {
                    eval('ele.'+attrs[i][0]+(attrs[i][2] ? '=\u0027' :'=')+attrs[i][1]+(attrs[i][2] ? '\u0027' :''));
               }
          }
          if(content) ele.appendChild(document.createTextNode(content));
          if(toNode) toNode.appendChild(ele);
          return ele;
     }
     function setMonth(ele){month=parseInt(ele.value);calender()}
     function setYear(ele){year=parseInt(ele.value);calender()}
     function setValue(ele) {
          if(ele.parentNode.className=='week' && ele.firstChild){
               var dayOut=ele.firstChild.nodeValue;
               if(dayOut < 10) dayOut='0'+dayOut;
               var monthOut=month+1;
               if(monthOut < 10) monthOut='0'+monthOut;
               target.value=year+'-'+monthOut+'-'+dayOut;
               removeCalender();
          }
     }
     function removeCalender() {
          var parentEle=document.getElementById("calender");
          while(parentEle.firstChild) parentEle.removeChild(parentEle.firstChild);
          document.getElementById('basis').parentNode.removeChild(document.getElementById('basis'));
     }          
     function calender() {
          var parentEle=document.getElementById("calender");
          parentEle.onmouseover=function(e) {
               var ele=setTarget(e);
               if(ele.parentNode.className=='week' && ele.firstChild && ele!=hoverEle) {
                    if(hoverEle) hoverEle.className=hoverEle.className.replace(/hoverEle ?/,'');
                    hoverEle=ele;
                    ele.className='hoverEle '+ele.className;
               } else {
                    if(hoverEle) {
                         hoverEle.className=hoverEle.className.replace(/hoverEle ?/,'');
                         hoverEle=false;
                    }
               }
          }
          while(parentEle.firstChild) parentEle.removeChild(parentEle.firstChild);
          function check(){
               if(year%4==0&&(year%100!=0||year%400==0))allMonth[1]=29;
               else allMonth[1]=28;
          }
          function addClass (name) { if(!currentClass){currentClass=name} else {currentClass+=' '+name} };
          if(month < 0){month+=12; year-=1}
          if(month > 11){month-=12; year+=1}
          if(year==yearMax-1) yearMax+=1;
          if(year==yearMin) yearMin-=1;
          check();
          var control=newElement('p',[['id','control',1]],false,parentEle);
          var controlPlus=newElement('a', [['href','javascript:month--;calender()',1],['className','controlPlus',1]], '<', control);
          var select=newElement('select', [['onchange',function(){setMonth(this)}]], false, control);
          for(var i=0; i<allNameOfMonths.length; i++) newElement('option', [['value',i,1]], allNameOfMonths[i], select);
          select.selectedIndex=month;
          select=newElement('select', [['onchange',function(){setYear(this)}]], false, control);
          for(var i=yearMin; i<yearMax; i++) newElement('option', [['value',i,1]], i, select);
          select.selectedIndex=year-yearMin;
          controlPlus=newElement('a', [['href','javascript:month++;calender()',1],['className','controlPlus',1]], '>', control);
          check();
          currentDay=1-new Date(year,month,1).getDay();
          if(currentDay > 0) currentDay-=7;
          currentDayZero=currentDay;
          var newMonth=newElement('table',[['cellSpacing',0,1],['onclick',function(e){setValue(setTarget(e))}]], false, parentEle);
          var newMonthBody=newElement('tbody', false, false, newMonth);
          var tr=newElement('tr', [['className','head',1]], false, newMonthBody);
          tr=newElement('tr', [['className','weekdays',1]], false, newMonthBody);
          for(i=0;i<7;i++) td=newElement('td', false, allNameOfWeekDays[i], tr);     
          tr=newElement('tr', [['className','week',1]], false, newMonthBody);
          for(i=0; i<allMonth[month]-currentDayZero; i++){
               var currentClass=false;               
               currentDay++;
               if(currentDay==day && month==monthZero && year==yearZero) addClass ('today');
               if(currentDay <= 0 ) {
                    if(currentDayZero!=-7) td=newElement('td', false, false, tr);
               }
               else {
                    if((currentDay-currentDayZero)%7==0) addClass ('holiday');
                    td=newElement('td', (!currentClass ? false : [['className',currentClass,1]] ), currentDay, tr);
                    if((currentDay-currentDayZero)%7==0) tr=newElement('tr', [['className','week',1]], false, newMonthBody);
               }
               if(i==allMonth[month]-currentDayZero-1){
                    i++;
                    while(i%7!=0){i++;td=newElement('td', false, false, tr)};
               }
          }
     }
     function showCalender(ele) {
          if(document.getElementById('basis')) { removeCalender() }
          else {
               target=document.getElementById(ele.id.replace(/for_/,'')); 
               var basis=ele.parentNode.insertBefore(document.createElement('div'),ele);
               basis.id='basis';
               newElement('div', [['id','calender',1]], false, basis);
               calender();
          }
     }
</script>