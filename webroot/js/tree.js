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

    $(popup).after($(popup_clone)
        .addClass('popup')
        .css("left", $("#TreeNode"+node_id).offset().left+$("#TreeNode"+node_id).width())
        .css("top", $("#TreeNode"+node_id).offset().top-80)
        .show()
    );

	$("body").bind('mouseup', HidePopups).bind('keyup', HidePopups);
}

var dragging = 0;
var dragging_mode = '';
var mouseX, mouseY;
var iPhone = (navigator.userAgent.indexOf('iPhone') != -1);
var static_mode = iPhone || (window.location.search.substring(1).indexOf('print')!=-1);


function MoveToNode(node_id) {
	var node = $('#TreeNode' + node_id);
	if (node) {
        $('#tree').css({
            'left': -$(node).position().left + $('#tree_window').width() / 2,
            'top': -$(node).position().top - $(node).height() + $('#tree_window').height() / 2})
	}
}

$(document).ready(function() {
    if (!static_mode) { // for printing or for iPhone browser (has drag scrolling already)
        $('#tree_window').mousedown(function(e) {
            if (!dragging) {
                dragging = 1;
                dragging_mode = 'tree';
                mouseX = getMouseX(e);
                mouseY = getMouseY(e);
            }
        });
        $('#zoom_track').mousedown(function() {
            if (!dragging) {
                dragging = 1;
                dragging_mode = 'zoom-track';
            }
        });
        $('#zoom_slider').mousedown(function() {
            if (!dragging) {
                dragging = 1;
                dragging_mode = 'zoom-slider';
            }
        });
        $(document).mouseup(function() {
            dragging = 0;
            dragging_mode = '';
        });

        $(document).mousemove(function(e) {
            if (dragging) {
                if ( e == null ) e = document.parentWindow.event;
                if (dragging==2) {
                    x = mouseX - getMouseX(e);
                    y = mouseY - getMouseY(e);
                    if (dragging_mode == 'tree') {
                        var pos = $('#tree').position();
                        $('#tree').css({'left': pos.left - x, 'top': pos.top - y})
                    } else if (dragging_mode == 'zoom-slider') {
                        slider_pos = Math.max(0, Math.min(143, $('#zoom_slider').position().top - y));
                        $('#zoom_slider').css({'top': slider_pos});
                        $('#tree').css({'fontSize': (143-slider_pos)/143*11+1});
                    } else if (dragging_mode == 'zoom-track') {

                    }
                }
                mouseX = getMouseX(e);
                mouseY = getMouseY(e);
                if (mouseX==null || mouseY==null) dragging = 0; else dragging = 2;
            }
        });

        $('#zoomer>img').click(function(e) {
            $('#tree').css({'left': $('#tree_window').width() / 2, 'top': $('#tree_window').height() / 2});
        });
    } else { // static_mode mode: Allow the device's drag-scrolling, rather than use javascript drag-scrolling.
        $('#zoomer').hide();
        $('#tree_window').css({'position': 'static'});

        var leftmost = 0;
        var topmost = 0;
        $('#tree li').each(function() {
            if ($(this).position().top < topmost) {
                topmost = $(this).position().top;
            }
            if ($(this).position().left < leftmost) {
                leftmost = $(this).position().left;
            }
        });

        $('#tree').css({'left': (-leftmost), 'top': (-topmost) + $('#header_container').height() + 5, 'margin': 5});
        $('#header_container').css({'position': 'fixed', 'z-index': 99999999});
        $('html, body').animate({
            scrollLeft: ($('.main:first').offset().left - (document.body.clientWidth/2)),
            scrollTop: ($('.main:first').offset().top)
        }, 500);
    }
});
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
