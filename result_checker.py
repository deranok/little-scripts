# A script to check ICAGH results
# It works by scraping the site. It's 2020, still no public
# API for the database :(
import sys
import re
import urllib.parse
import urllib.request

db_url = "http://ghanadatabank.com/icagresultschecking2/"
stu_id = None
month = None
year = None

#parse arguments
for v in sys.argv[1:]:
	if v.startswith("stu="):
		stu_id = v[4:]
	if v.startswith("month="):
		month = v[len("month="):]
	if v.startswith("year"):
		year = v[len("year="):]

if stu_id == None or month == None or year == None:
	print("There are missing arguments. Exiting.")
	help="""\
Usage: 
python3 result_checker.py stu=studentID month=month year=year
month is either Nov or May
Eg. python3 result_checker.py stu=111111 month=Nov year=2003"""
	print(help)
	sys.exit()

print(stu_id, month, year)
#form data
form_data = urllib.parse.urlencode({'stu': stu_id, 'month': month, 'year': year})
form_data = form_data.encode('ascii')

print("Requesting data")
try:
	res = urllib.request.urlopen(db_url, data=form_data)
except:
	print("Network problems... try again later.")
res = str(res.read(), 'utf-8')

print('Received data')
print("Parsing data")

not_found = re.compile('Results Not Found')
not_found = not_found.search(res)
if not_found:
	print('Results Not Found')
	sys.exit()

th = re.compile("<th>.*</th>")
td = re.compile("<td>.*</td>")
th = th.findall(res)
td = td.findall(res)

th = [s[4:-5] for s in th]
td = [s[4:-5] for s in td]

print("--##RESULTS##--")
for i, j in zip(th, td):
	print(i, ": ", j)
