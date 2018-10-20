import argparse
import sys
import re
sys.path.insert(0, '../FBM Utility')
from FoodBankManager import FBM

# ['Donor ID', 'First Name', 'Suffix', 'Zip/Postal Code', 'Donation Count', 'Salutation Greeting (Dear So and So)', 'Primary Phone Number', 'Company / Organization Name', 'Last Name', 'Middle Name', 'Email Address', 'Street Address', 'City/Town']

def get_email(donor_list):
	email = re.compile(r".+?@.+?\..+")
	r = []
	for line in donor_list:
		try:
			if email.match(line[10]):
				r.append(line)
		except IndexError:
			pass
	return r


def get_mail(donor_list):
	# Feilds that need to at lease exist for this to be a valid mailig address
	valid_list = [1, 3, 11, 12]
	r = []
	for item in donor_list:
		try:
			for i in valid_list:
				if len(item[i] <= 1):
					break
				r.append(item)
		except IndexError:
			pass
	return r

if __name__ == '__main__':
	url = 'mcfb.soxbox.co'
	parser = argparse.ArgumentParser(description='Mail Merge')
	parser.add_argument('-e', action="store_true", default=False,
					help='Generate Email Mail Messages')
	parser.add_argument('-m', action="store_true", default=False,
					help='Generate Mail Messages')
	parser.add_argument("-i", dest="filename", required=True,
					help="File Template")
	parser.add_argument("-u", dest="user", required=True,
					help="FBM User")
	parser.add_argument("-p", dest="password", required=True,
					help="FBM Pass")			
	args = parser.parse_args()
	food_bank = FBM(url)
	print 'Connecting to ' + url
	food_bank.auth(args.user, args.password)
	print 'Getting donor list'
	donors = food_bank.GetDonors()
	print next(donors)
	if args.e:
		for item in get_email(donors):
			print item

	if args.m:
		for item in get_mmail(donors):
			print item
