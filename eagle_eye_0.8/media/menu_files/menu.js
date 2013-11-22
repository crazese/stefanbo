var preload_imgs = new Array();
var hide_menu_cols;
var show_menu_cols;
var last_node;
function init_menu() {
	// load menu vars
	var framset_menu = parent.window.document.getElementById('framset_menu');
	hide_menu_cols = framset_menu.getAttribute('hide_menu_width')+',*';
	show_menu_cols = framset_menu.getAttribute('show_menu_width')+',*';
	// check if menu is hidden
	var framset_menu = parent.window.document.getElementById('framset_menu');
	if( framset_menu.cols == hide_menu_cols ) {
		menu_toggle();
	}
}

////////////////////////////////////////
// menu items mouse over / mouse out
////////////////////////////////////////

function menu_item_over( el ) {
	el.className = el.className + '_over';
}

function menu_item_out( el ) {
	el.className = el.className.replace('_over','');
}

function show_menu_item( id ) {
	var el;
	var open = new Array();
	el = document.getElementById(id);
	while( el.parentNode ){
		el = el.parentNode;
		if( el.className && el.className.indexOf('_sub') != -1 ) {
			open.push( el.previousSibling );
		}
	}
	open.reverse();
	for( e in open ) {
		menu_item_toggle( open[e] );
	}
}

// show specified menu
function show_menu( id ) {
	var el;
	el = document.getElementById(id);
	if( el ) {
		menu_item_toggle( el );
	}
}

function getNextSibling(startBrother){
 var  endBrother=startBrother.nextSibling;
  while(endBrother.nodeType!=1){
    endBrother = endBrother.nextSibling;
  }
  return endBrother;
}

function close_last_node(){
    if(last_node){
        var next =  getNextSibling(last_node);
        next.style.display = 'none'; 
        var imgs = last_node.getElementsByTagName('img');
        for( i=0 ; i<imgs.length; i++ ) {
            imgs[i].src = imgs[i].src.replace('_open','_close');
        }
    }

}

function menu_item_toggle( el ) {
    if(el.className.indexOf("item1")!=-1){
        if(el!=last_node){
            close_last_node();
            last_node=el;
        }
    }
    toggle(getNextSibling(el));
	var imgs = el.getElementsByTagName('img');
	for( i=0 ; i<imgs.length; i++ ) {
		imgs[i].src = (imgs[i].src.indexOf('_open') == -1) ? imgs[i].src.replace('_close','_open') : imgs[i].src.replace('_open','_close');
	}
	btn_redraw();
}

function btn_redraw() {
	var el;
	for( i=0; i<document.images.length; i++ ){
		el = document.images[i];
		if( el.className == 'btn' ) {
			el.style.visibility = 'hidden';
			el.style.visibility = 'visible';
		}
	}
}


function menu_toggle( tab_el ) {
	var el = parent.window.document.getElementById('framset_menu');
	// toggle frame
	if( el.cols != hide_menu_cols ) {
		// create close tab
		var tab = document.createElement('img');
		tab.id = 'menu_tab_open';
		tab.src = tab_el.src.replace('_close','_open');
		tab.style.position = 'absolute';
		tab.style.left = '0px';
		tab.style.top = '20px';
		tab.style.cursor = 'pointer';
		tab.onclick = menu_toggle;
		document.body.appendChild( tab );
		// resize down
		el.cols = hide_menu_cols;
	} else {
		// remove tab img
		if( document.getElementById('menu_tab_open') ) document.body.removeChild( document.getElementById('menu_tab_open') );
		// resize up
		el.cols = show_menu_cols;
	}
}

function toggle( el ) {
    try{
        el.style.display = el.style.display != "block" ? 'block' : 'none'; 
    }catch(e){
    }
}

function alertr( myvar , max ) {
	var myvar, max;
	if( ! max ) max = 20;
	i=0;
	for( e in myvar ) {
		alert(e);
		if( i > max ) break;
		i++;
	}
}

function in_array( $value , $arr ) {
	var $value, $arr;
	for( e in $arr ) {
		if ( $arr[e] == $value ) return true;
	}
	return false;
}

function array_key( $value , $arr ) {
	var $value, $arr;
	for( e in $arr ) {
		if ( $arr[e] == $value ) return e;
	}
	return false;
}
