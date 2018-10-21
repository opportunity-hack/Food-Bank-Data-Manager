import calendar
from datetime import datetime

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


def graph_1(fbm):
	time = datetime.now()
	food_data = GenerateMonthlyReport.RunMonthlyReport(fbm, month=time.month, year=time.year)
	
	messy_data = GenerateMonthlyReport.PivotInventoryTable(food_data)
	useful_data = messy_data.to_dict()[('sum', 'Weight (lbs)')]
	sorted_data = zip(*sorted(useful_data.items(), key=operator.itemgetter(1), reverse=True))
	
	graph = [go.Bar(x=sorted_data[0], y=sorted_data[1])]
	return "{}.embed".format(py.plot(graph, auto_open=False))


if __name__ == '__main__':
	fbm = FoodBankManager.FBM("mcfb.soxbox.co")
	db = DBConn('database_info.json')
	
	db.add_frame_date(1, graph_1(fbm))
