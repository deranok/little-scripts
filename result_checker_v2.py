import argparse
import sys
import urllib.parse
import urllib.request

parser = argparse.ArgumentParser()
parser.add_argument("-i", 
                     metavar= "student_id",
                     help="eg. 224419",
                     dest="std_id",
                     required=True)
parser.add_argument("-l",
                    choices=['1', '2', '3'],
                    help="part taken eg. 3",
                    dest='std_lvl',
                    required=True)
parser.add_argument("-m", 
                     choices = ['may', 'nov'],
                     help="month of sitting",
                     dest='month',
                     required=True)
parser.add_argument("-y",
                     metavar="year_of_sitting",
                     help="v.2 only supports sittings from 2020, see v.1 for older sittings",
                     dest='year',
                     required=True)
args = parser.parse_args()



unparse_levels = {'1': 'one', '2': 'two', '3': 'three'}
unparse_months = {'may': 'May', 'nov': 'November'}
std_id = args.std_id
month = unparse_months[args.month]
year = args.year 
std_lvl = unparse_levels[args.std_lvl]

moz_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:94.0) Gecko/20100101 Firefox/94.0"

req_url = "https://resultchecker.icagh.org/" + f'{std_lvl}'
req_params = urllib.parse.urlencode({f'l{std_lvl}': std_id, 'month': month, 'year': year})
req_url = req_url + '?' + req_params
req_headers = {'User-Agent': moz_agent}
req = urllib.request.Request(req_url, headers = req_headers)
print("requesting ", req_url)
req_resp = urllib.request.urlopen(req)

#print(req_resp.read().decode('utf-8'))

buf = req_resp.read().decode('utf-8')

buf = buf.split('\n')

buf = [l.lstrip() for l in buf]
tables = []
inrow = False
for l in buf:
  if l.startswith("<tr"):
    tables.append([])
    inrow = True
    continue
  if l.startswith("</tr"):
    inrow = False
  if inrow:
    tables[-1].append(l)

if len(tables) < 2 :
  print("no result found")
  sys.exit()

def shave_tags(l):
  l = l.removeprefix("<th>")
  l = l.removesuffix("</th>")
  l = l.removeprefix("<td>")
  l = l.removesuffix("</td>")
  return l

def make_groups(n, arr):
  res = []
  for idx, elem in enumerate(arr):
    if idx % n == 0:
      res.append([])
    res[-1].append(elem)
  return res

tables = [[shave_tags(l) for l in arr] for arr in tables]
tables = [[l for l in arr 
           if l != ''
           if l != "Result"
           if l != '|' 
           if l != ':'
           ] for arr in tables]
tables[0] = [tuple(l) for l in make_groups(2, tables[0])]
tables[1] = zip(tables[1], tables[2])
tables = [tables[0], list(tables[1])]

for l in tables[0]:
  print("{0:10}: {1:20}".format(*l))
for l in tables[1]:
  print("{0:20}: {1:20}".format(*l))
