import calendar
from datetime import datetime
from dateutil.relativedelta import relativedelta

import pandas as pd
import numpy as np
import operator
import json

import plotly
import plotly.plotly as py
import plotly.graph_objs as go
with open('plotly_auth.json') as f:
	plotly_auth = json.load(f)
plotly.plotly.sign_in(**plotly_auth)

import mysql.connector

import FoodBankManager
import GenerateMonthlyReport

class DBConn:
	def __init__(self, cred_file):
		database_info = None
		with open(cred_file) as f:
			database_info = json.load(f)
		self.db = mysql.connector.connect(**database_info)
		self.cur = self.db.cursor()
	
	def add_frame_date(self, id, url):
		sql = 'INSERT INTO `dashboard_data` (`frame_id`, `url`) VALUES (%s, %s);'
		values = (id, url)
		self.cur.execute(sql, values)
		self.db.commit()


def embedded_plot(graph):
	return "{}.embed".format(py.plot(graph, auto_open=False))

def graph_1(fbm):
	time = datetime.now() + relativedelta(months=0)
	food_data = GenerateMonthlyReport.RunMonthlyReport(fbm, month=time.month, year=time.year)
	
	if food_data.empty:
		return "/no_data.html"
	
	messy_data = GenerateMonthlyReport.PivotInventoryTable(food_data)
	useful_data = messy_data.to_dict()[('sum', 'Weight (lbs)')]
	sorted_data = zip(*sorted(useful_data.items(), key=operator.itemgetter(1), reverse=True))
	
	data = [go.Bar(x=sorted_data[0], y=sorted_data[1])]
	layout = go.Layout(
		title = "This Month's Donations",
		xaxis = {
			'type':  'category',
			'title': 'Source'
		},
		yaxis = {
			'title': 'Weight (lbs)',
			'rangemode': 'tozero'
		}
	)
	graph = {
		'data': data,
		'layout': layout
	}
	return embedded_plot(graph)

def graph_2345_data(fbm):
	now = datetime.now()
	end = datetime(now.year, now.month, 1)
	start = end + relativedelta(months=-12)
	food_data = fbm.GetFoodDonations(start, end)
	guest_data = fbm.GetGuestData(start, end)
	food_data[u'Donated On'] = pd.to_datetime(food_data[u'Donated On']).astype(datetime)
	food_data[u'Weight (lbs)'] = food_data[u'Weight (lbs)'].astype(float)
	guest_data[u'Outreach on'] = pd.to_datetime(guest_data[u'Outreach on']).astype(datetime)
	guest_data[u'Tracking Result'] = guest_data[u'Tracking Result'].astype(int)
	
	output = []
	period_start = start
	inventory = 0
	all_clients = set()
	
	for i in range(12):
		period_end = period_start + relativedelta(months=+1)
		
		food_month = food_data[(food_data[u'Donated On'] >= period_start) & (food_data[u'Donated On'] <= period_end)]
		guest_month = guest_data[(guest_data[u'Outreach on'] >= period_start) & (guest_data[u'Outreach on'] <= period_end)]
		
		intake_total = food_month[food_month[u'DonorCategory'] != u'Waste'][u'Weight (lbs)'].sum()
		waste_total =  food_month[food_month[u'DonorCategory'] == u'Waste'][u'Weight (lbs)'].sum()
		output_total = guest_month.shape[0] * 40
		food_out_total = waste_total + output_total
		inventory += (intake_total - waste_total - output_total)
		
		volunteer_hours = 0
		day_start = period_start
		for d in range(1, calendar.monthrange(day_start.year, day_start.month)[1] + 1):
			day_end = day_start + relativedelta(days=+1)
			outreach_day = guest_data[(guest_data[u'Outreach on'] >= day_start) & (guest_data[u'Outreach on'] <= day_end)]
			volunteer_hours += len(outreach_day[u'Volunteer'].unique())
			day_start = day_end
		
		clients = set(guest_month[u'Guest ID'].unique())
		month_clients = len(clients)
		new_clients = len([c for c in clients if c not in all_clients])
		all_clients = all_clients.union(clients)
		
		clients_served = guest_month[u'Tracking Result'].sum()
		
		output.append((period_start, intake_total, food_out_total, inventory, volunteer_hours, month_clients, new_clients, clients_served))
		period_start = period_end
	
	return zip(*output)

def graph_2(data):
	trace1 = {
		'x':    data[0],
		'y':    data[1],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'Food In',
		'yaxis':      'y1'
	}
	trace2 = {
		'x':    data[0],
		'y':    data[2],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'Food Out',
		'yaxis':      'y1'
	}
	trace3 = {
		'x':    data[0],
		'y':    data[3],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'Food Inventory',
		'yaxis':      'y1'
	}
	data = [trace1, trace2, trace3]
	layout = go.Layout(
		title = "Food In/Out/Inventory, Last 12 Months",
		showlegend = True,
		xaxis = {
			'type':  'category',
			'title': 'Date'
		},
		yaxis = {
			'title': 'Weight (lbs)',
			'rangemode': 'tozero'
		}
	)
	graph = {
		'data': data,
		'layout': layout
	}
	return embedded_plot(graph)

def graph_3(data):
	trace1 = {
		'x':    data[0],
		'y':    data[4],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'Volunteer Hours',
		'yaxis':      'y1'
	}
	data = [trace1]
	layout = go.Layout(
		title = "Volunteer Hours, Last 12 Months",
		xaxis = {
			'type':  'category',
			'title': 'Date'
		},
		yaxis = {
			'title': 'Hours',
			'rangemode': 'tozero'
		}
	)
	graph = {
		'data': data,
		'layout': layout
	}
	return embedded_plot(graph)

def graph_4(data):
	trace1 = {
		'x':    data[0],
		'y':    data[5],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'All Clients',
		'yaxis':      'y1'
	}
	trace2 = {
		'x':    data[0],
		'y':    data[6],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'New Clients',
		'yaxis':      'y1'
	}
	data = [trace1, trace2]
	layout = go.Layout(
		title = "Clients, Last 12 Months",
		showlegend = True,
		xaxis = {
			'type':  'category',
			'title': 'Date'
		},
		yaxis = {
			'title': 'Clients',
			'rangemode': 'tozero'
		}
	)
	graph = {
		'data': data,
		'layout': layout
	}
	return embedded_plot(graph)

def graph_5(data):
	trace1 = {
		'x':    data[0],
		'y':    data[7],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'People Served',
		'yaxis':      'y1'
	}
	data = [trace1]
	layout = go.Layout(
		title = "Total Served, Last 12 Months",
		xaxis = {
			'type':  'category',
			'title': 'Date'
		},
		yaxis = {
			'title': 'People',
			'rangemode': 'tozero'
		}
	)
	graph = {
		'data': data,
		'layout': layout
	}
	return embedded_plot(graph)


if __name__ == '__main__':
	fbm = FoodBankManager.FBM("mcfb.soxbox.co")
	db = DBConn('database_info.json')
	
	db.add_frame_date(1, graph_1(fbm))
	data = graph_2345_data(fbm)
	db.add_frame_date(2, graph_2(data))
	db.add_frame_date(3, graph_3(data))
	db.add_frame_date(4, graph_4(data))
	db.add_frame_date(5, graph_5(data))
