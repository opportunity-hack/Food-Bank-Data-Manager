import calendar
import datetime

import pandas as pd
import numpy as np

from FoodBankManager import FBM

def FindLastMonthsDates():
	"""
	Finds the first and last days of last month
	:return: (first_of_month, last_of_month)
	:rtype: tuple(datetime.date, datetime.date)
	"""
	now = datetime.date.today()
	last_of_month = now.replace(day=1) - datetime.timedelta(days=1)
	first_of_month = last_of_month.replace(day=1)

	return first_of_month, last_of_month

def RunMonthlyReport(FBMInst,month=None, year=None):
	if month is not None and year is not None:
		start = datetime.date(year, month, 1)
		end = datetime.date(year, month, calendar.monthrange(year, month)[1])
	else:
		start, end = FindLastMonthsDates()

	return FBMInst.GetFoodDonations(start, end)

def PivotInventoryTable(df):
	df[["Weight (lbs)"]] = df[["Weight (lbs)"]].astype("float")
	df = pd.pivot_table(df, index=["DonorCategory"], values=["Weight (lbs)"], aggfunc=[np.sum])
	return df


if __name__ == '__main__':
	q = FBM("mcfb.soxbox.co")
	data = RunMonthlyReport(q, month=8, year=2018)
	data = PivotInventoryTable(data)
	print data
	writer = pd.ExcelWriter('output.xlsx')
	data.to_excel(writer, "September")
	writer.save()
