"""
BEFORE RUNNING:
---------------
1. If not already done, enable the Google Sheets API
   and check the quota for your project at
   https://console.developers.google.com/apis/api/sheets
2. Install the Python client library for Google APIs by running
   `pip install --upgrade google-api-python-client`
"""
from __future__ import print_function
from pprint import pprint
import var
import pymysql
import pickle
import os.path
from googleapiclient.discovery import build
from google.auth.transport.requests import Request
import datetime
import json

def get_sheet():
    creds = None
    if os.path.exists('token.pickle'):
        with open('token.pickle', 'rb') as token:
            creds = pickle.load(token)
    # If there are no (valid) credentials available, let the user log in.
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            creds.refresh(Request())
        else:
            print('no creds')

    service = build('sheets', 'v4', credentials=creds)

    # Call the Sheets API
    sheet = service.spreadsheets()
    return sheet


def dbconn(num = 1):
    global conn, cur
    if num == 1:
        conn = pymysql.connect(host = "macqueen0987.kro.kr", port = 3306, user = "EVE", passwd = "password", db = "EVE", charset = "utf8")
        cur = conn.cursor(pymysql.cursors.DictCursor)
    else:
        conn.commit()
        conn.close()



SPREADSHEET_ID = '1mU5Iho48rYVXrtGKdREN5-fqmQoOAZ9-1hH8Q2e4KUk'
temp_arr = []
sheet = get_sheet()

dbconn()
query = 'select * from buyback_price'
cur.execute(query)
result = cur.fetchone()
dbconn(0)
ore = json.loads(result['ore'])
mineral = json.loads(result['mineral'])
pi = json.loads(result['pi1']) + json.loads(result['pi2'])
value = []
value.append(str(result['updated']))
for i in ore:
    value.append(i[1])

for i in mineral:
    value.append(i[1])

# print(value)
resource = {"values": [value]}
sheet.values().append(spreadsheetId=SPREADSHEET_ID,  range='ore_mineral_log!A1:Y',  body=resource,  valueInputOption="USER_ENTERED").execute()
value = []
value.append(str(result['updated']))
for i in pi:
    if i[2] == 'Urgent':
        value.append(str(round(int(i[1])/1.1,0)).replace('.0',''))
    else:
        value.append(i[1])
resource = {"values": [value]}
sheet.values().append(spreadsheetId=SPREADSHEET_ID,  range='PI_log!A1:AI',  body=resource,  valueInputOption="USER_ENTERED").execute()
# print(value)