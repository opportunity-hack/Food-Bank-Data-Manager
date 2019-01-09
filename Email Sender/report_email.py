import datetime
import mysql
import json
import sys
import os
from dateutil.relativedelta import relativedelta

sys.path.append(os.path.dirname(os.path.realpath(__file__)) + '/../FBM Utility/')

from GenerateMonthlyReport import WriteExcelSheet
from EmailSender import send_email

email_body = """
Please see the attached monthly report, generated on {}
""".format(datetime.datetime.now().strftime("%Y-%m-%d"))

if __name__ == '__main__':
    with open(os.path.dirname(os.path.realpath(__file__)) + '/../FBM Utility/database_info.json') as f:
        database_info = json.load(f)

    db = mysql.connector.connect(**database_info)
    cur = db.cursor()

    cur.execute("SELECT * FROM report_emails")
    email_list = cur.fetchall()
    email_list = tuple(i[1] for i in email_list)

    now = datetime.datetime.now()
    # Reset to first of last month
    rdate = datetime.datetime(now.year, now.month, 1) + relativedelta(months=-1)

    report_filename = WriteExcelSheet(os.path.abspath("../FBM Utility/out/Report {}-{}".format(rdate.month, rdate.year)),
                                        month=rdate.month, year=rdate.year)

    for email in email_list:
        send_email(email, "Matthews Crossing Report for {}".format(rdate.strftime("%Y-%m")),
                    email_body, report_filename)

    os.remove(report_filename)
