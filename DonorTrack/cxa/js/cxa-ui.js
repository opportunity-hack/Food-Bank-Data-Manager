/*
cxa-ui.js - UI script for CXA UI web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA UI, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
All versions of this file, including previous versions, are subject to this license and no other Creative Commons license.
You should have received a copy of this license with CXA UI.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

$(document).ready(CXAUI);

var lastDrawer=false;

function CXAUI(){
	window.setTimeout(function ()
		{
			$("#welcomebar").slideUp(300);
		}
		,3000);
	
	$(".drawer-handle").each(function ()
		{
			$(this).click(function ()
				{
					var sel= "#d" + this.id.substring(2);
					
					if (lastDrawer != sel)
					{
						$(lastDrawer).slideUp();
						lastDrawer=sel;
					}
					
					$(sel).slideToggle();
				});
		});
	
	window.cxaui_ttc = $('<div></div>');
	window.cxaui_ttc.addClass('ttc');
	window.cxaui_ttc.appendTo($("body"));
	
	$(".hastip").each(function ()
		{
			cxaui_add_tip($(this));
		});
}

function cxaui_add_tip(jq, tip)
{
	if (tip !== undefined)
	{
		jq.attr('tip', tip);
	}
	
	jq.mousemove(function (event)
		{
			window.cxaui_ttc.show();
			window.cxaui_ttc.css('left', event.pageX + 10);
			window.cxaui_ttc.css('top', event.pageY);
			window.cxaui_ttc.text($(this).attr('tip'));
		});
	
	jq.mouseout(function (event)
		{
			window.cxaui_ttc.hide();
		});
}