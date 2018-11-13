/*
cxa-table.js - Database table frontend interface.
Copyright (c) 2018 James Rowley

This file is part of CXA Auth LW, which is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License.
You should have received a copy of this license with CXA Auth LW.
If not, to view a copy of the license, visit https://creativecommons.org/licenses/by-nc-sa/3.0/us/legalcode
*/

var elems = {
	'table': '<table></table>',
	'td':    '<td></td>',
	'tr':    '<tr></tr>',
	'div':   '<div></div>',
	'text':  '<input type="text" />',
	'a':     '<a></a>'
};

function scroll_width ()
{
	var outer = $('<div></div>').css({visibility: 'hidden', width: 25, overflow: 'scroll'}).appendTo('body');
	var inner = $('<div></div>').css({width: '100%'}).appendTo(outer).outerWidth();
	outer.remove();
	return 25 - inner;
}


function TableRow (table, primary_key, data)
{
	this.table = table; // instanceof Table
	this.primary_key = primary_key;
	
	this.row = $(elems.tr);
	if ('row_class' in this.table.specification.data)
	{
		this.row.addClass(this.table.specification.data.row_class);
	}
	this.table.table.append(this.row);
	
	this.cells = {};
	for (var column in this.table.cell_classes)
	{
		this.cells[column] = new (this.table.cell_classes[column])(this, column);
	}
		
	if (data !== undefined)
	{
		this.set_data(data);
	}
}

TableRow.prototype.remove = function ()
{
	this.row.empty();
	this.row.remove();
	delete this;
};

TableRow.prototype.set_data = function (data)
{
	for (var column in this.cells)
	{
		if (column in data)
		{
			this.cells[column].set_data(data[column]);
		}
	}
};

TableRow.prototype.get_data = function ()
{
	data = {}
	
	for (var column in this.cells)
	{
		value = this.cells[column].get_data();
		if (value !== null)
		{
			data[column] = value;
		}
	}
	
	return data;
};

TableRow.prototype.open = function ()
{
	this.row.addClass('active-remote');
	
	for (var column in this.cells)
	{
		this.cells[column].open();
	}
};

TableRow.prototype.close = function ()
{
	ok = true;
	
	for (var column in this.cells)
	{
		ok = ok && this.cells[column].validate();
	}
	
	if (ok)
	{
		data = this.get_data();
		data[this.table.specification.data.row_pkid] = this.primary_key;
		
		action = null;
		if (true)
		{
			action = this.table.specification.data.set_action;
		}
		else
		{
			// There are not yet tables implemented with the new action
			action = this.table.specification.data.new_action;
			delete data[this.table.specification.data.row_pkid];
		}
		
		$.post(
			this.table.specification.data.address,
			{
				'action': action,
				'data':   data
			},
			(function(row){return function (resp)
			{
				if (resp != 'ok')
				{
					console.error('Server error when updating row ('+row.primary_key+'): ' + resp);
					row.table.refresh();
				}
				else
				{
					for (var column in row.cells)
					{
						row.cells[column].close();
					}
					
					row.row.removeClass('active-remote');
				}
			}}(this))
		);
	}
};

TableRow.prototype.del = function ()
{
	data = {};
	data[this.table.specification.data.row_pkid] = this.primary_key;
	
	$.post(
		this.table.specification.data.address,
		{
			'action': this.table.specification.data.del_action,
			'data':   data
		},
		(function(row){return function (resp)
		{
			if (resp != 'ok')
			{
				console.error('Server error when deleting row ('+row.primary_key+') :' + resp);
				row.table.refresh();
			}
			else
			{
				row.remove();
				delete row.table.data_rows[this.primary_key];
			}
		}}(this))
	);
};


function TableHeaderRow (table)
{
	this.table = table;
	
	this.header_container = $(elems.div);
	this.header_container.addClass('theader');
	this.table.container.prepend(this.header_container);
	
	this.header_table = $(elems.table);
	this.header_table.addClass('teamtable').addClass('alpha');
	this.header_container.append(this.header_table);
	
	this.header_row = $(elems.tr);
	this.header_table.append(this.header_row);
	
	this.header_data_columns = {};
	
	for (var col_id in this.table.specification.columns)
	{
		var column = this.table.specification.columns[col_id];
		
		this.header_data_columns[col_id] = $(elems.td);
		this.header_data_columns[col_id].addClass(column.cell_style);
		
		var label = col_id;
		if ('label' in column)
		{
			label = column.label;
		}
		this.header_data_columns[col_id].text(label);
		
		if ('label_tip' in column)
		{
			this.header_data_columns[col_id].addClass('hastip');
			this.header_data_columns[col_id].attr('tip', column.label_tip);
		}
		
		this.header_row.append(this.header_data_columns[col_id]);
	}
	
	this.header_adjust_column = $(elems.td);
	this.header_adjust_column.addClass('final-column');
	this.header_row.append(this.header_adjust_column);
	
	this.adjust_header();
	$(window).resize(function(){this.adjust_header});
}

TableHeaderRow.prototype.remove = function ()
{
	this.header_container.empty();
	this.header_container.remove();
};

TableHeaderRow.prototype.adjust_header = function ()
{
	this.header_adjust_column.css('padding-right', scroll_width);
};


function TableTailRow (table)
{
	// No tables exist yet with new action
	this.table = table;
}

TableTailRow.prototype.remove = function ()
{
	
};

TableTailRow.prototype.new_row = function ()
{
	
};


function TableCell (row, column)
{
	// Base class, should be heavily subclassed
	this.row = row; // instanceof TableRow
	this.column = column;
	this.create();
	
	if ('remote' in this.row.table.specification.columns[this.column])
	{
		this.remote = this.row.table.specification.columns[this.column];
	}
	else
	{
		this.remote = false;
	}
}

TableCell.prototype.create = function ()
{
	this.container = $(elems.td);
	if ('cell_style' in this.row.table.specification.columns[this.column])
	{
		this.container.addClass(this.row.table.specification.columns[this.column].cell_style);
	}
	this.container.appendTo(this.row.row);
	
	this.is_open = false;
	this.open_class = 'editable';
	this.error_class = 'error';
	this.open_input = $(elems.text);
	this.open_input.addClass('tinput');
};

TableCell.prototype.remove = function ()
{
	this.container.remove();
};

TableCell.prototype.set_data = function (data)
{
	this.container.text(data);
	this.open_input.val(data);
};

TableCell.prototype.get_data = function ()
{
	return this.open_input.val();
};

TableCell.prototype.open = function ()
{
	this.container.addClass(this.open_class);
	
	if (!this.remote)
	{
		this.container.empty();
		this.container.append(this.open_input);
	}
	
	this.is_open = true;
};

TableCell.prototype.validate = function ()
{
	if (this.is_open)
	{
		if (('mandatory' in this.row.table.specification.columns[this.column]
			&& this.row.table.specification.columns[this.column].mandatory == false)
			|| this.open_input.val())
		{
				
			this.container.removeClass(this.error_class);
			return true;
		}
		else
		{
			this.container.addClass(this.error_class);
			return false;
		}
	}
	else
	{
		// Validating while not open is an unsupported scenario.
		return null;
	}
};

TableCell.prototype.close = function ()
{
	this.container.removeClass(this.open_class);
	
	if (!this.remote)
	{
		this.open_input.detach();
		this.container.text(this.open_input.val());
	}
	
	this.is_open = false;
};


var TableCellClasses = {};


TableCellClasses.Number = function () {TableCell.apply(this, arguments);};
TableCellClasses.Number.prototype = Object.create(TableCell.prototype);
TableCellClasses.Number.prototype.constructor = TableCell;

TableCellClasses.Number.prototype.validate = function ()
{
	if (this.is_open)
	{
		if (('mandatory' in this.row.table.specification.columns[this.column]
			&& this.row.table.specification.columns[this.column].mandatory == false)
			|| !isNaN(this.open_input.val()))
		{
				
			this.container.removeClass(this.error_class);
			return true;
		}
		else
		{
			this.container.addClass(this.error_class);
			return false;
		}
	}
	else
	{
		// Validating while not open is an unsupported scenario.
		return null;
	}
};

TableCellClasses.Number.prototype.get_data = function ()
{
	return Number(this.open_input.val());
};


TableCellClasses.Integer = function () {TableCell.apply(this, arguments);};
TableCellClasses.Integer.prototype = Object.create(TableCell.prototype);
TableCellClasses.Integer.prototype.constructor = TableCell;

TableCellClasses.Integer.prototype.validate = function ()
{
	if (this.is_open)
	{
		if (('mandatory' in this.row.table.specification.columns[this.column]
			&& this.row.table.specification.columns[this.column].mandatory == false)
			|| this.open_input.val().match(/^-{0,1}\d+$/))
		{
				
			this.container.removeClass(this.error_class);
			return true;
		}
		else
		{
			this.container.addClass(this.error_class);
			return false;
		}
	}
	else
	{
		// Validating while not open is an unsupported scenario.
		return null;
	}
};

TableCellClasses.Integer.prototype.get_data = function ()
{
	return parseInt(this.open_input.val());
};


TableCellClasses.Text = function () {TableCell.apply(this, arguments);};
TableCellClasses.Text.prototype = Object.create(TableCell.prototype);
TableCellClasses.Text.prototype.constructor = TableCell;


TableCellClasses.EditButton = function () {TableCell.apply(this, arguments);};
TableCellClasses.EditButton.prototype = Object.create(TableCell.prototype);
TableCellClasses.EditButton.prototype.constructor = TableCell;

TableCellClasses.EditButton.prototype.create = function ()
{
	this.container = $(elems.td);
	if ('cell_style' in this.row.table.specification.columns[this.column])
	{
		this.container.addClass(this.row.table.specification.columns[this.column].cell_style);
	}
	this.container.appendTo(this.row.row);
	
	this.is_open = false;
	this.open_class = 'editable';
	this.error_class = 'error';
	this.open_input = $(elems.text);
	this.open_input.addClass('tinput');
	
	this.button_edit = $(elems.div);
	this.button_edit.addClass('editbtn');
	this.button_edit.click({real_this: this}, this.dispatch_edit_click);
	this.button_edit.appendTo(this.container);
	
	this.button_delete = $(elems.div);
	this.button_delete.addClass('delbtn');
	this.button_delete.click({row:this.row}, function(event){event.data.row.del();});
	this.button_delete.appendTo(this.container);
	this.button_delete.hide()
	/*
	this.button_delete.attr('tip', 'REMOVE USER');
	this.button_delete.mousemove(function (event)
		{
			ttc.show();
			ttc.css('left', event.pageX-127);
			ttc.css('top',  event.pageY);
			ttc.text($(this).attr('tip'));
		});
	this.button_delete.mouseout(function (event)
		{
			ttc.hide();
		});
	*/
};

TableCellClasses.EditButton.prototype.set_data = function (data)
{
	// Pass
};

TableCellClasses.EditButton.prototype.open = function (data)
{
	this.button_edit.addClass('uneditbtn');
	this.button_delete.show();
	this.is_open = true;
};

TableCellClasses.EditButton.prototype.close = function (data)
{
	this.button_edit.removeClass('uneditbtn');
	this.button_delete.hide();
	this.is_open = false;
};

TableCellClasses.EditButton.prototype.dispatch_edit_click = function (event)
{
	if (event.data.real_this.is_open)
	{
		event.data.real_this.row.close();
	}
	else
	{
		event.data.real_this.row.open();
	}
}


TableCellClasses.Approver = function () {TableCell.apply(this, arguments);};
TableCellClasses.Approver.prototype = Object.create(TableCell.prototype);
TableCellClasses.Approver.prototype.constructor = TableCell;

TableCellClasses.Approver.prototype.create = function ()
{
	this.remote = true;
	
	this.container = $(elems.td);
	if ('cell_style' in this.row.table.specification.columns[this.column])
	{
		this.container.addClass(this.row.table.specification.columns[this.column].cell_style);
	}
	this.container.appendTo(this.row.row);
	
	this.is_open = false;
	this.open_class = 'editable';
	this.error_class = 'error';
	
	this.button_approve = $(elems.div);
	this.button_approve.addClass('editbtn');
	this.button_approve.addClass('uneditbtn');
	this.button_approve.click({approver:this}, function(event){event.data.approver.approve();});
	this.button_approve.appendTo(this.container);
	
	this.input = $(elems.text);
	this.input.addClass('cinput');
	this.input.appendTo(this.container);
	
	this.button_delete = $(elems.div);
	this.button_delete.addClass('delbtn');
	this.button_delete.click({row:this.row}, function(event){event.data.row.del();});
	this.button_delete.appendTo(this.container);
};

TableCellClasses.Approver.prototype.set_data = function (data)
{
	this.input.val(data);
};

TableCellClasses.Approver.prototype.get_data = function ()
{
	return parseInt(this.input.val());
};

TableCellClasses.Approver.prototype.validate = function ()
{
	if (this.input.val().match(/^-{0,1}\d+$/))
	{
		this.container.removeClass(this.error_class);
		return true;
	}
	else
	{
		this.container.addClass(this.error_class);
		return false;
	}
};

TableCellClasses.Approver.prototype.approve = function()
{
	if (this.validate())
	{
		data = {};
		data[this.row.table.specification.data.row_pkid] = this.row.primary_key;
		data[this.column] = this.get_data();
		
		$.post(
			this.row.table.specification.data.address,
			{
				'action': this.row.table.specification.columns[this.column].action,
				'data':   data
			},
			(function(cell){return function (resp)
			{
				if (resp != 'ok')
				{
					console.error('Server error when approving row ('+cell.row.primary_key+') :' + resp);
					cell.row.table.refresh();
				}
				else
				{
					cell.row.remove();
				}
			}}(this))
		);
	}
}


TableCellClasses.Password = function () {TableCell.apply(this, arguments);};
TableCellClasses.Password.prototype = Object.create(TableCell.prototype);
TableCellClasses.Password.prototype.constructor = TableCell;

TableCellClasses.Password.prototype.create = function ()
{
	this.container = $(elems.td);
	if ('cell_style' in this.row.table.specification.columns[this.column])
	{
		this.container.addClass(this.row.table.specification.columns[this.column].cell_style);
	}
	this.container.appendTo(this.row.row);
	
	this.is_open = false;
	this.open_class = 'editable';
	this.error_class = 'error';
	
	this.open_input = $(elems.text);
	this.open_input.addClass('tinput');
	
	this.reset_link = $(elems.a);
	this.reset_link.text('get reset link');
	this.reset_link.css('cursor', 'pointer');
	this.reset_link.click({cell: this}, function(event){event.data.cell.get_reset_link()});
	this.reset_link.appendTo(this.container);
};

TableCellClasses.Password.prototype.set_data = function (data)
{
	// Pass
};

TableCellClasses.Password.prototype.get_data = function ()
{
	if (this.open_input.val() == '')
	{
		return null;
	}
	else
	{
		return this.open_input.val();
	}
};

TableCellClasses.Password.prototype.open = function ()
{
	this.container.addClass(this.open_class);
	
	if (!this.remote)
	{
		this.reset_link.detach();
		this.container.append(this.open_input);
	}
	
	this.is_open = true;
};

TableCellClasses.Password.prototype.close = function ()
{
	this.container.removeClass(this.open_class);
	
	if (!this.remote)
	{
		this.open_input.detach();
		this.container.append(this.reset_link);
	}
	
	this.is_open = false;
};

TableCellClasses.Password.prototype.validate = function ()
{
	if (this.is_open)
	{
		if (('mandatory' in this.row.table.specification.columns[this.column]
			&& this.row.table.specification.columns[this.column].mandatory == false)
			|| this.row.primary_key == null
			|| !this.open_input.val())
		{
				
			this.container.removeClass(this.error_class);
			return true;
		}
		else
		{
			this.container.addClass(this.error_class);
			return false;
		}
	}
	else
	{
		// Validating while not open is an unsupported scenario.
		return null;
	}
};

TableCellClasses.Password.prototype.get_reset_link = function ()
{
	$.post(
		this.row.table.specification.data.address,
		{
			'action': this.row.table.specification.columns[this.column].reset_action,
			'data':   this.row.primary_key
		},
		(function(cell){return function(resp)
		{
			if (typeof resp === 'string' && resp.includes('http'))
			{
				resp = resp.substring(0, resp.length-2);
				window.open(resp, '_blank').focus();
			}
			else
			{
				console.error('Error while getting reset link for password on row ('+cell.row.primary_key+'): '+resp);
			}
		}}(this)),
		'text'
	);
}


function Table (container, specification)
{
	this.container = container;
	this.specification = specification;
	
	this.cell_classes = {};
	for (var col_id in this.specification.columns)
	{
		var column = this.specification.columns[col_id];
		
		if (('cell_class' in column) && (column.cell_class in TableCellClasses))
		{
			this.cell_classes[col_id] = TableCellClasses[column.cell_class];
		}
		else
		{
			console.error('Inavlid column specification "'+col_id+'":', column);
			delete this.specification.columns[col_id];
		}
	}
	
	this.table_container = $(elems.div);
	this.table_container.attr('id', 'tmain')
	this.container.append(this.table_container);
	
	this.table = $(elems.table);
	this.table.addClass('teamtable');
	this.table_container.append(this.table);
	
	this.header_row = new TableHeaderRow(this);
	this.tail_row = new TableTailRow(this);
	
	this.data_rows = {};
	
	this.server_fail = false;
	this.refresh();
}

Table.prototype.remove = function ()
{
	this.container.empty();
};

Table.prototype.refresh = function ()
{
	var table = this;
	$.post(
		this.specification.data.address,
		{
			action: this.specification.data.get_action,
			data: (this.specification.data.get_parameters || '')
		},
		(function(table){return function (data)
		{
			console.log(data);
			if (!Array.isArray(data))
			{
				table.server_error();
			}
			else
			{
				table.populate(data);
			}
		}}(table)),
		'json'
	).fail((function(table){return function (data){
			table.server_error(data.responseText);
	}}(table)));
};

Table.prototype.server_error = function (data)
{
	if (data)
	{
		if (data.length < 100)
		{
			alert('Server error: '+data);
		}
		else
		{
			alert('Server error');
		}
	}
	else
	{
		alert('Could not contact server');
	}
	
	console.error('Invalid response: ', data);
	this.server_fail = true;
};

Table.prototype.populate = function (data)
{
	var row_ids = [];
	
	for (var row_index = 0; row_index < data.length; row_index++)
	{
		var row_data = data[row_index];
		var row_id = row_data[this.specification.data.row_pkid];
		row_ids.push(row_id);
		
		if (row_id in this.data_rows)
		{
			this.data_rows[row_id].set_data(row_data);
			this.table.append(this.data_rows[row_id].row); // ensure ordering is correct
		}
		else
		{
			this.data_rows[row_id] = new TableRow(this, row_id, row_data);
		}
	}
	
	for (var row_id in this.data_rows)
	{
		if (row_ids.indexOf(row_id) < 0)
		{
			this.data_rows[row_id].remove();
			delete this.data_rows[row_id];
		}
	}
};
