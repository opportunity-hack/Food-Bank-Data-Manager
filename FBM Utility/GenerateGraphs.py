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
plotly.tools.set_credentials_file(username='ferret_guy', api_key='b2ztoCdM68H6rBxqiiez')

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
	time = datetime.now()
	food_data = GenerateMonthlyReport.RunMonthlyReport(fbm, month=time.month, year=time.year)
	
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
			'title': 'Weight (lbs)'
		}
	)
	graph = {
		'data': data,
		'layout': layout
	}
	return embedded_plot(graph)

def graph_2(fbm):
	now = datetime.now()
	end = datetime(now.year, now.month + 1, 1)
	start = end + relativedelta(months=-12)
	food_data = fbm.GetFoodDonations(start, end)
	guest_data = fbm.GetGuestData(start, end)
	food_data[u'Donated On'] = pd.to_datetime(food_data[u'Donated On']).astype(datetime)
	food_data[u'Weight (lbs)'] = food_data[u'Weight (lbs)'].astype(float)
	guest_data[u'Outreach on'] = pd.to_datetime(guest_data[u'Outreach on']).astype(datetime)
	
	output = []
	period_start = start
	inventory = 0
	
	for i in range(12):
		period_end = period_start + relativedelta(months=+1)
		
		food_month = food_data[(food_data[u'Donated On'] >= period_start) & (food_data[u'Donated On'] <= period_end)]
		guest_month = guest_data[(guest_data[u'Outreach on'] >= period_start) & (guest_data[u'Outreach on'] <= period_end)]
		
		intake_total = food_month[food_month[u'DonorCategory'] != u'Waste'][u'Weight (lbs)'].sum()
		waste_total =  food_month[food_month[u'DonorCategory'] == u'Waste'][u'Weight (lbs)'].sum()
		output_total = guest_month.shape[0] * 40
		
		inventory += (intake_total - waste_total - output_total)
		output.append((period_start, intake_total, waste_total + output_total, inventory))
		
		period_start = period_end
	
	output = zip(*output)
	trace1 = {
		'x':    output[0],
		'y':    output[1],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'Food In',
		'yaxis':      'y1'
	}
	trace2 = {
		'x':    output[0],
		'y':    output[2],
		'mode': 'lines',
		'line': {
			'width': 3
		},
		'fill':       'none',
		'name':       'Food Out',
		'yaxis':      'y1'
	}
	trace3 = {
		'x':    output[0],
		'y':    output[3],
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
			'title': 'Weight (lbs)'
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
	db.add_frame_date(2, graph_2(fbm))
