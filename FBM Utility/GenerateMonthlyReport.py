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

def PiviotInvintoryTable(df):
	print df
	df[["Weight (lbs)"]] = df[["Weight (lbs)"]].astype("float")
	df = pd.pivot_table(df, index=["DonorCategory"], values=["Weight (lbs)"], aggfunc=[np.sum])
	return df

def MonthlyGuestData(FBMInst,month=None, year=None):
	if month is not None and year is not None:
		start = datetime.date(year, month, 1)
		end = datetime.date(year, month, calendar.monthrange(year, month)[1])
	else:
		start, end = FindLastMonthsDates()

	data = FBMInst.GetGuestData(start, end)

	data[["Tracking Result"]] = data[["Tracking Result"]].astype("int")

	return data

if __name__ == '__main__':
	# set unlimited table display size
	pd.set_option('display.expand_frame_repr', False)
	q = FBM("mcfb.soxbox.co")
	data = MonthlyGuestData(q, month=8, year=2018)
	print data["Tracking Result"].sum()
	print data
