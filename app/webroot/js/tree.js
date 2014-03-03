$(document).ready(function() {
    

});

function HidePopups(event) {
	var el = event.target;
	if(el.tagName.toUpperCase() == "SELECT" || el.tagName.toUpperCase() == "INPUT") return false;
	if(el.className.toUpperCase()== "TOGGLE") return true;
	$(".popup").each(function(){ $(this).hide(); });
	$("body").unbind('mouseup', HidePopups).unbind('keyup', HidePopups);
	return true;
} 

function ShowPopup(node_id) {
	$(".popup").each(function(){ $(this).remove(); });
	
	var popup = $("#HiddenPopup");
	var popup_clone = $(popup).clone();
	
	$(popup_clone).html($(popup_clone).html().replace(/__n__/ig, node_id));
	
	$(popup).after($(popup_clone).addClass('popup').css("left", $("#TreeNode"+node_id).offset().left+$("#TreeNode"+node_id).width()).css("top", $("#TreeNode"+node_id).offset().top-80).show());
	
	$("body").bind('mouseup', HidePopups).bind('keyup', HidePopups);
}

function el_id(x) { return document.getElementById(x); }
var dragging = 0;
var dragging_mode = '';
var tree = el_id('tree');
var mouseX, mouseY;
var iPhone = (navigator.userAgent.indexOf('iPhone') != -1);
var static_mode = iPhone || (window.location.search.substring(1).indexOf('printmode')!=-1);


function MoveToNode(node_id) {
	var node = $('#TreeNode' + node_id);
	if (node) {
		tree.style.left = - $(node).position().left + 'px';
		tree.style.top = - $(node).position().top - $(node).height() + 'px';
	}
}

if (!static_mode) { // for printing or for iPhone browser (has drag scrolling already)
	el_id('tree_window').onmousedown = function() {
		if (!dragging) {
			dragging = 1;
			dragging_mode = 'tree';
		}
	}
	el_id('zoom_track').onmousedown = function() {
		if (!dragging) {
			dragging = 1;
			dragging_mode = 'zoom-track';
		}
	}
	el_id('zoom_slider').onmousedown = function() {
		if (!dragging) {
			dragging = 1;
			dragging_mode = 'zoom-slider';
		}
	}
	document.onmouseup = function() {
		dragging = 0;
		dragging_mode = '';
	}
	document.onmousemove = function(e) {
		if (dragging) {
			if ( e == null ) e = document.parentWindow.event;
			if (dragging==2) {
				x = mouseX-getMouseX(e);
				y = mouseY-getMouseY(e);
				if (dragging_mode=='tree') {
					tree.style.left = parseInt(tree.style.left+'0')-x+'px';
					tree.style.top = parseInt(tree.style.top+'0')-y+'px';
	//				tree.scrollLeft += x;
	//				tree.scrollTop += y;
				} else if (dragging_mode=='zoom-slider') {
					slider_pos = Math.max(0, Math.min(143, parseInt(el_id('zoom_slider').style.top+'0')-y));
					el_id('zoom_slider').style.top = slider_pos+'px';
					tree.style.fontSize = (143-slider_pos)/143*11+1+'px';
				} else if (dragging_mode=='zoom-track') {
					
				}
			}
			mouseX = getMouseX(e);
			mouseY = getMouseY(e);
			if (mouseX==null || mouseY==null)
				dragging = 0;
			else dragging = 2;
		}
	}
	
	el_id('zoom_track').parentNode.getElementsByTagName('img')[0].onclick=function() {
		tree.style.left = 0;
		tree.style.top = 0;
	}
} else { // static_mode mode: Allow the device's drag-scrolling, rather than use javascript drag-scrolling.
	el_id('tree_centerer').style.left = el_id('tree_centerer').style.top = el_id('tree_centerer').style.margin = 0;//document.body.style.minWidth
	el_id('zoomer').style.display = 'none';
	el_id('tree_window').style.position = 'static';
	//document.body.style.backgroundColor = el_id('tree_window').style.backgroundColor;
	var nodes = tree.getElementsByTagName('li');
	var leftmost = 0;
	var topmost = 0;
	function fixPositioning() {
		if (nodes[1].offsetTop < 0) {
			for (i=0; i<nodes.length; i++) {
				if (nodes[i].offsetTop < topmost) {
					topmost = nodes[i].offsetTop;
				}
				if (nodes[i].offsetLeft < leftmost) {
					leftmost = nodes[i].offsetLeft;
				}
			}
			tree.style.left = (0-leftmost)+'px';
			tree.style.top = (0-topmost)+'px';
			if (iPhone) {
				doScroll(0-leftmost-(document.body.clientWidth/2)+(nodes[1].clientWidth/2), 0-topmost-(document.body.clientHeight/2)+(nodes[1].clientHeight/2));
			}
		} else {
			setTimeout(fixPositioning, 1);
		}
	}
	fixPositioning();
	function doScroll(x, y) {
		document.documentElement.scrollLeft = x;
		document.documentElement.scrollTop = y;
		document.body.scrollLeft = x;
		document.body.scrollTop = y;
		window.pageXOffset = x;
		window.pageYOffset = y;
		window.scrollX = x;
		window.scrollY = y;
	}
}
function getMouseX(evt) {
	if (evt.pageX) return evt.pageX;
	else if (evt.clientX)
		return evt.clientX + (document.documentElement.scrollLeft ?
		document.documentElement.scrollLeft :
		document.body.scrollLeft);
	else return null;
}
function getMouseY(evt) {
	if (evt.pageY) return evt.pageY;
	else if (evt.clientY)
		return evt.clientY + (document.documentElement.scrollTop ?
		document.documentElement.scrollTop :
		document.body.scrollTop);
	else return null;
}