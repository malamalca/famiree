/**
 * imgnotes jQuery plugin
 * version 0.1
 *
 * Copyright (c) 2008 Dr. Tarique Sani <tarique@sanisoft.com>
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * @URL      http://www.sanisoft.com/blog/2008/05/26/img-notes-jquery-plugin/
 * @Example  example.html
 *
 **/

//Wrap in a closure
(function($) {

	$.fn.imgNotes = function(o) {
        options = {
            template: "<div class='note'></div>"
        };
        if(typeof o != "undefined"){
            $.extend(options, o);
        }

        console.log(options);

		if(typeof options.notes != "undefined"){
			notes = options.notes;
		}

		image = this;

		imgOffset = $(image).offset();

		$(notes).each(function(){
			appendnote(this);
		});

		$(image).hover(
			function(){
				$('.note').show();
			},
			function(){
				$('.note').hide();
			}
		);

		addnoteevents();

		$(window).resize(function () {
			$('.note').remove();

			imgOffset = $(image).offset();

			$(notes).each(function(){
				appendnote(this);
			});

			addnoteevents();

		});
	}

	function addnoteevents() {
		$('.note').hover(
			function(){
				$('.note').show();
				$(this).next('.notep').show();
				$(this).next('.notep').css("z-index", 10000);
			},
			function(){
				$('.note').show();
				$(this).next('.notep').hide();
				$(this).next('.notep').css("z-index", 0);
			}
		);
	}


	function appendnote(note_data){

		var note_left  = parseInt(imgOffset.left) + parseInt(note_data.x1);
		var note_top   = parseInt(imgOffset.top) + parseInt(note_data.y1);
        var note_p_top = note_top + parseInt(note_data.height)+5;

        var note_template = options.template;

        if (typeof note_data.id != "undefined") {
            note_template = note_template.replace(/__id__/g, note_data.id);
        }

		note_area_div = $(note_template).css({ left: note_left + 'px', top: note_top + 'px', width: note_data.width + 'px', height: note_data.height + 'px' });

		if (typeof note_data.url != "undefined") {
			$(note_area_div).addClass("note_link").click(
				function(){
					document.location.href = note_data.url;
				}
			);
		}

		note_text_div = $('<div class="notep" >'+note_data.note+'</div>').css({ left: note_left + 'px', top: note_p_top + 'px', width: note_data.width + 'px'});

		$('body').append(note_area_div);
		$('body').append(note_text_div);
	}

// End the closure
})(jQuery);
