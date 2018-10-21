import json
import sys
import os

import smtplib
from email.MIMEMultipart import MIMEMultipart
from email.MIMEText import MIMEText
from email.MIMEBase import MIMEBase
from email import encoders

config_data = None
 
with open("email_config.json", "r") as f:
	config_data = json.load(f)
 
fromaddr = config_data["From"]
toaddr = sys.argv[1]
 
msg = MIMEMultipart()
 
msg['From'] = fromaddr
msg['To'] = toaddr
msg['Subject'] = sys.argv[2]
 
body = sys.argv[3]
 
msg.attach(MIMEText(body, 'plain'))
 
filename = os.path.basename(sys.argv[4])
attachment = open(sys.argv[4], "rb")
 
part = MIMEBase('application', 'octet-stream')
part.set_payload((attachment).read())
encoders.encode_base64(part)
part.add_header('Content-Disposition', "attachment; filename= %s" % filename)
 
msg.attach(part)
 
server = smtplib.SMTP('smtp.gmail.com', 587)
server.starttls()
server.login(fromaddr, config_data["Password"])
text = msg.as_string()
server.sendmail(fromaddr, toaddr, text)
server.quit()