import calendar
import datetime

import pandas as pd
import numpy as np
import openpyxl

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

	data = FBMInst.GetFoodDonations(start, end)

	data[["Weight (lbs)"]] = data[["Weight (lbs)"]].astype("float")

	return data

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

def WriteSummeryData(q, ws, origin=(1,1),  month=None, year=None):
	donor_catagories = ["Grocery", "Org/Corp", "Individual", "Church", "Purchased", "Senior program"]
	if month is not None and year is not None:
		start = datetime.date(year, month, 1)
		end = datetime.date(year, month, calendar.monthrange(year, month)[1])
	else:
		start, end = FindLastMonthsDates()

	donation_data = RunMonthlyReport(q, month=start.month, year=start.year)
	user_data = MonthlyGuestData(q, month=start.month, year=start.year)

	ws.column_dimensions[ws.cell(row=origin[0], column=origin[1]).column].width = 14

	for cell_row in range(origin[0], origin[0] + len(donor_catagories) + 3 + 1):
		ws.cell(row=cell_row, column=origin[1]).style = "Input"

	ws.cell(row=origin[0], column=origin[1]).value = "{}".format(start.strftime("%B %Y"))
	ws.cell(row=origin[0], column=origin[1]).style = "Headline 4"
	ws.cell(row=origin[0], column=origin[1]).alignment = openpyxl.styles.Alignment(horizontal='center')
	for i, item in enumerate(donor_catagories):
		ws.cell(row=origin[0] + i + 1, column=origin[1]).value = donation_data[donation_data["DonorCategory"] == item].sum()["Weight (lbs)"]
	ws.cell(row=origin[0] + len(donor_catagories) + 1, column=origin[1]).value = "=SUM({}:{})".format(ws.cell(row=origin[0] + 2, column=origin[1] + 1).coordinate, ws.cell(row=origin[0] + len(donor_catagories) + 1, column=origin[1] + 1).coordinate)
	ws.cell(row=origin[0] + len(donor_catagories) + 1, column=origin[1]).style = "Calculation"
	ws.cell(row=origin[0] + len(donor_catagories) + 2, column=origin[1]).value = donation_data[donation_data["DonorCategory"] == "Waste"].sum()["Weight (lbs)"]
	ws.cell(row=origin[0] + len(donor_catagories) + 3, column=origin[1]).value = "={}-{}".format(ws.cell(row=origin[0] + len(donor_catagories) + 2, column=origin[1] + 1).coordinate, ws.cell(row=origin[0] + len(donor_catagories) + 3, column=origin[1] + 1).coordinate)
	ws.cell(row=origin[0] + len(donor_catagories) + 3, column=origin[1]).style = "Calculation"

def WriteSummeryLabel(ws, origin=(1,1)):
	ws.column_dimensions[ws.cell(row=origin[0], column=origin[1]).column].width = 17
	donor_catagories = ["Grocery", "Org/Corp", "Individual", "Church", "Purchased", "Senior program"]
	for i, item in enumerate(donor_catagories):
		ws.cell(row=origin[0] + i + 1, column=origin[1]).value = item
	ws.cell(row=origin[0] + len(donor_catagories) + 1, column=origin[1]).value = "Total Food Income"
	ws.cell(row=origin[0] + len(donor_catagories) + 2, column=origin[1]).value = "Waste"
	ws.cell(row=origin[0] + len(donor_catagories) + 3, column=origin[1]).value = "Total"

	for cell_row in range(origin[0], origin[0] + len(donor_catagories) + 3 + 1):
		ws.cell(row=cell_row, column=origin[1]).style = "Headline 4"

def WriteExcelSheet(month=None, year=None):
	if month is not None and year is not None:
		start = datetime.date(year, month, 1)
		end = datetime.date(year, month, calendar.monthrange(year, month)[1])
	else:
		start, end = FindLastMonthsDates()

	q = FBM("mcfb.soxbox.co")
	donation_data = RunMonthlyReport(q, month=start.month, year=start.year)
	user_data = MonthlyGuestData(q, month=start.month, year=start.year)

	wb = openpyxl.Workbook()
	ws = wb.active
	WriteSummeryLabel(ws, origin=(2, 1))
	for month in range(1, 12 + 1):
		WriteSummeryData(q, ws, origin=(2, month+1), month=month, year=year)
	wb.save("test.xlsx")

if __name__ == '__main__':
	pd.set_option('display.expand_frame_repr', False)

	print WriteExcelSheet(month=8, year=2018)
