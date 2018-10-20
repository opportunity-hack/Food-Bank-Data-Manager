/*
cxa-um.js - User management table script for CXA Auth LW web data framework.
Copyright (c) 2016 James Rowley

This file is part of CXA UI, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA UI.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

var tUsers={
	'userid':{
		type:'remote',
		style:'pre',
		mandatory:false
	},
	'name':{
		type:'text',
		style:'18',
		mandatory:true
	},
	'username':{
		type:'text',
		style:'18',
		mandatory:true
	},
	'email':{
		type:'text',
		style:'18',
		mandatory:true
	},
	'password':{
		type:'passwd',
		style:'18',
		mandatory:true
	},
	'otpsecret':{
		type:'text',
		style:'18',
		mandatory:false
	},
	'authorization':{
		type:'number',
		style:'7',
		mandatory:true
	},
	'editrow':{
		type:'editbtn',
		style:'post',
		mandatory:false
	},
	'data':{
		type:'data',
		rowclass:'row',
		delaction:'deluser',
		setaction:'setuser'
	}
};

var tApproveUsers={
	'userid':{
		type:'remote',
		style:'pre'
	},
	'name':{
		type:'remote',
		style:'25'
	},
	'username':{
		type:'remote',
		style:'25'
	},
	'email':{
		type:'remote',
		style:'25'
	},
	'approver':{
		type:'approver',
		style:'10'
	},
	'data':{
		type:'data',
		rowclass:'row',
		delaction:'dellimbouser'
	}
};

var interAddress="userinter.php";
var serverFail = false;
var serverError = function(dataa){
	if(dataa){
		alert("Server error: "+dataa);
	}else{
		alert("Could not contact server");
	}
	console.log(dataa);
	serverFail=true;
};
var refresh = function(){console.log("No mode!");};
var cElements=[];

function cxaTableUI(){
	refresh();
	$("#refresher").click(refresh);
	adjustHeader();
	$(window).resize(adjustHeader);
}

function cxaApproveUsers(){
	refresh = function(){
		doLimboUsers();
		serverFail = false;
	}
	cxaTableUI();
}

function cxaManageUsers(){
	refresh = function(){
		doUsers();
		serverFail = false;
	}
	cxaTableUI();
}

function adjustHeader(){
	$(".theader").css('padding-right', scrollWidth);
}

function scrollWidth(){
	var outer = $('<div></div>').css({visibility: 'hidden', width: 25, overflow: 'scroll'}).appendTo('body');
	var inner = $('<div></div>').css({width: '100%'}).appendTo(outer).outerWidth();
	outer.remove();
	return 25 - inner;
};

var ts={
	'td':'<td></td>',
	'tr':'<tr></tr>',
	'div':'<div></div>',
	'te':'<input type="text" />',
};

function populateTable(selector,array,data,table,post){
	var s=$(selector).empty();
	for(row in data){
		var c=array[row]={};
		c.props={};
		c.props.pos=row;
		c.row=$(ts.tr).appendTo(s).addClass(table.data.rowclass);
		for(i in table){
			switch(table[i].type){
				case "remote":
				case "number":
				case "text":
					c[i]=$(ts.td).appendTo(c.row).addClass('col-'+(table[i].style)).text(data[row][i]);
					break;
				case "id":
					c[i]=$(ts.td).appendTo(c.row).addClass('col-'+(table[i].style)).text(data[row][i]);
					c.props.moveup=$(ts.div).appendTo(c[i]).addClass('upbtn').click({position:row},function(event){bumpUp(event.data.position);});
					c.props.movedown=$(ts.div).appendTo(c[i]).addClass('downbtn').click({position:row},function(event){bumpDown(event.data.position);});
					break;
				case "editbtn":
					c[i]=$(ts.td).appendTo(c.row).addClass('col-'+(table[i].style));
					c.props.edit=$(ts.div).appendTo(c[i]).addClass('editbtn').click({c:c},function(event){openRow(event.data.c,table);});
					c.props.del=$(ts.div).appendTo(c[i]).addClass('delbtn').click({c:c},function(event){delRow(event.data.c,table);}).hide();
					c.props.del.attr('tip', 'REMOVE USER').mousemove(function(){
						ttc.show().css("left",event.pageX-127).css("top",event.pageY).text($(this).attr("tip"));
					}).mouseout(function(event){
						ttc.hide();
					});
					break;
				case "approver":
					c[i]=$(ts.td).appendTo(c.row).addClass('col-'+(table[i].style));
					c.props.approve=$(ts.div).appendTo(c[i]).addClass('editbtn').addClass('uneditbtn').click({c:c},function(event){approveUser(event.data.c);});
					c.props.authlevel=$(ts.te).appendTo(c[i]).addClass('cinput');
					c.props.del=$(ts.div).appendTo(c[i]).addClass('delbtn').click({c:c},function(event){delRow(event.data.c,table);});
					break;
				case "passwd":
					c[i]=$(ts.td).appendTo(c.row).addClass('col-'+(table[i].style)).html("<a onclick=\"getResetLink("+data[row]["userid"]+")\">get reset link</a>");
					break;
			}
		}
		if(typeof post === 'function'){post(c,row);}
		c.data=data[row];
	}
}

function openRow(ref,col){
	for(i in col){
		switch(col[i].type){
			case "number":
			case "text":
				ref[i].empty().addClass('editable');
				ref[i+'-i']=$(ts.te).appendTo(ref[i]).addClass('tinput').val(ref.data[i]);
				break;
			case "remote":
			case "id":
				ref[i].addClass('active-remote');
				break;
			case "editbtn":
				ref[i].addClass('active-remote');
				ref.props.del.show();
				break;
			case "passwd":
				ref[i].empty().addClass('editable');
				ref[i+'-i']=$(ts.te).appendTo(ref[i]).addClass('tinput');
				break;
		}
	}
	ref.props.edit.addClass('uneditbtn').unbind().click(function(){postRow(closeRow(ref,col),col);});
}

function closeRow(ref,col){
	var fail=false;
	for(i in col){
		if(col[i].mandatory){
			switch(col[i].type){
				case "passwd":
					if(!ref.data.userid && ref[i+'-i'].val()==""){
						ref[i].addClass('error');
						fail=true;
					}
					break;
				default:
					if(!ref[i+'-i'].val()){
						ref[i].addClass('error');
						fail=true;
					}
					break;
			}		
		}
	}
	if(!fail){
		for(i in col){
			switch(col[i].type){
				case "number":
					ref.data[i]=parseInt(ref[i+'-i'].val());
					ref[i+'-i'].remove();
					ref[i].removeClass('editable').text(ref.data[i]);
					break;
				case "text":
					ref.data[i]=ref[i+'-i'].val();
					ref[i+'-i'].remove();
					ref[i].removeClass('editable').text(ref.data[i]);
					break;
				case "remote":
				case "id":
					ref[i].removeClass('active-remote');
					break;
				case "editbtn":
					ref[i].removeClass('active-remote');
					ref.props.del.hide();
					break;
				case "passwd":
					if(ref[i+'-i'].val()!=""){
						ref.data[i]=ref[i+'-i'].val();
					}
					ref[i+'-i'].remove();
					ref[i].removeClass('editable').text(ref.data[i]);
					break;
			}
		}
		ref.props.edit.removeClass('uneditbtn').unbind().click(function(){openRow(ref,col);});
		return(ref.data);
	}
}

function postData(action,data,postfn){
	if(data){
		$.post(
			interAddress,
			{action:action,data:data},
			function(rtn){
				if(rtn!="ok"){
					serverError(rtn);
				}
				if(typeof(postfn)==='function'){
					postfn();
				}else{
					refresh();
				}
			}
		);
	}
}

function delRow(ref,col){
	postData(col.data.delaction,ref.data);
}

function postRow(data,col){
	if(data){
		if(data.id || data.userid){
			postData(col.data.setaction,data);
		}else{
			postData(col.data.newaction,data);
		}
	}
}

function doLimboUsers(){
	$.post(interAddress,{action:"getlimbousers",data:""},function(data){
		if(!data){
			serverError();
		}else{
			populateTable('#userboard', cElements, data, tApproveUsers);
		}
	},"json");
}

function approveUser(ref){
	var authlevel=ref.props.authlevel.val();
	if(authlevel!==""){
		authlevel=parseInt(authlevel);
		var data={userid:ref.data.userid,authlevel:authlevel};
		postData("approveuser",data);
	}else{
		alert("You must set an authorization level.");
	}
}

function doUsers(){
	$.post(interAddress,{action:"getusers",data:""},function(data){
		if(!data){
			serverError();
		}else{
			populateTable('#userboard', cElements, data, tUsers);
		}
	},"json");
}

function getResetLink(id){
	$.post(interAddress,{action:"resetuserpassword",data:id},function(data){
		if(typeof data === "string" && data.includes("http")){
			data = data.substring(0, data.length-2);
			window.open(data, '_blank').focus();
		}else{
			console.log(data);
		}
	},"text");
}