import requests
import time
import json
import csv
import sys


class FBM():
	def __init__(self, url):
		self.url = url
		self.session = requests.Session()

	def auth(self, user, password):
		payload = {
			'username': user,
			'password': password,
			'location': '1',
			'action': 'Login'
		}
		self.session.post('https://' + self.url + '/login/', data=payload)

	def GetDonors(self):
		try:
			return self.donor_table
		except AttributeError:
			payload = {
				'fileName': "",
				'col[donors.donors_79fe2d07e8]': '1',
				'col[donors.firstName]': '1',
				'col[donors.middleName]': '1',
				'col[donors.lastName]': '1',
				'col[donors.donors_e0feeaff84]': '1',
				'col[donors.donors_730b308554]': '1',
				'col[donors.donors_b4d4452788]': '1',
				'col[donors.streetAddress]': '1',
				'col[donors.city]': '1',
				'col[donors.zipCode]': '1',
				'col[donors.donors_6213775871]': '1',
				'col[donations.donationTypeSum]': '1',
				'conditions[type]': 'And',
				'conditions[1][field]': 'donors.created_at',
				'conditions[1][action]': 'dlte',
				'conditions[1][value]': time.strftime('%Y-%m-%d'),
				'conditions[1][id]': '1',
				'conditions[1][blockType]': 'item',
				'conditions[1][parent]': "",
				'blockCount': '2'
			}
			r = self.session.post('https://' + self.url +
							'/reports/donor/donors/csv/',
							data=payload,
							stream=True)
			r.raw.decode_content = True
			self.donor_table = csv.reader(str(r.raw.data).split('\n'))
		return self.donor_table

	def GetDonations(self):
		try:
			return self.donation_table
		except AttributeError:
			payload = {
				'fileName': '',
				'donation_type': '0',
				'col[donors.id]': '1',
				'col[donors.firstName]': '1',
				'col[donors.middleName]': '1',
				'col[donors.lastName]': '1',
				'col[donors.donors_e0feeaff84]': '1',
				'col[donors.donors_b4d4452788]': '1',
				'col[donors.city]': '1',
				'col[donors.state]': '1',
				'col[donors.zipCode]': '1',
				'col[donors.created_at]': '1',
				'col[donors.donors_6213775871]': '1',
				'col[donations.donationType_id]': '1',
				'col[donations.donations_1b458b4e6a]': '1',
				'col[donations.donation_at]': '1',
				'col[donations.donations_41420c6893]': '1',
				'col[donations.donations_f695e975c6]': '1',
				'conditions[type]': 'And',
				'conditions[1][field]': 'donations.donation_at',
				'conditions[1][action]': 'dlte',
				'conditions[1][value]': time.strftime('%Y-%m-%d'),
				'conditions[1][id]': '1',
				'conditions[1][blockType]': 'item',
				'conditions[1][parent]': '',
				'blockCount': '2'
			}
			r = self.session.post('https://' + self.url +
							'/reports/donor/donations/csv/',
							data=payload,
							stream=True)
			r.raw.decode_content = True
			self.donation_table = csv.reader(str(r.raw.data).split('\n'))
		return self.donation_table

	def PostDonation(self, D_id, dollars, pounds, D_type, date):
		donation_type = [
			"",
			"Individual Donor",
			"Churches/Places of Worship",
			"Grants/Foundations",
			"Business/Corporation/Organization",
			"Fundraising Events",
			"Board of Directors",
			"Recurring Monthly Donation",
			"NTFH Event",
			"Other Revenue"
		]

		payload = {
		'action': 'Save Donation & close',
		'donationType_id': '1',
		'donation_at': date,
		'donations_1b458b4e6a': donation_type[int(D_type)],
		'donations_e0a1fae0a3': dollars,
		'donations_f695e975c6': pounds
		}

		r = self.session.post('https://' + self.url +
						'/create-new-donation/create/did:' + str(D_id) + '/',
						data=payload)
		return r.status_code

	def AddDonor(self, donor_json):
		params = json.loads(donor_json)
		payload = {
			'donors_1f13985a81': 'N/A',
			'firstName': params['first'],
			'lastName': params['last'],
			'donors_e0feeaff84': params['email'],
			'donors_730b308554': 'N/A',
			'streetAddress': params['street'],
			'city': params['town'],
			'state': params['state'],
			'zipCode': params['zip'],
			'donorType_id': '1',
			'action': 'Save'
		}
		r = self.session.post('https://' + self.url +
						'/create-new-donation/donor/create/',
						data=payload)
		return r.status_code
		

if __name__ == '__main__':
	if len(sys.argv) < 4:
		print "Usage: 'task' 'user' 'pass' etc..."
		exit(1)
	q = FBM("mcfb.soxbox.co")
	q.auth(sys.argv[2], sys.argv[3])
	if sys.argv[1] == "donors":
		donor_list = q.GetDonors()
		headers = next(donor_list)
		for row in donor_list:
			print "{"
			for a, b in zip(row, headers):
				print "\"" + b + "\": \"" + a + "\","
			print "},"
	elif sys.argv[1] == "add_donor":
		# json formatted input wih the following params
		# first, last, email, street, tow, state, zip
		print q.AddDonor(sys.argv[4])
	elif sys.argv[1] == "add_donation":
		# type user pass donor_id pounds donation_type date (YYYY-MM-DD)
		print q.PostDonation(sys.argv[4], 0, sys.argv[5], sys.argv[6], sys.argv[7])
	
