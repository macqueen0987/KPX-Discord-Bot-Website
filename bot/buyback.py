from __future__ import print_function
import var
import pymysql
import pickle
import os.path
from googleapiclient.discovery import build
from google.auth.transport.requests import Request
import datetime
import json


conn = None
cur = None

commands = ['초기화 ']

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

def add(message_id):
    dbconn()
    query = 'select idx from buyback order by idx desc limit 1'
    cur.execute(query)
    result = cur.fetchone()['idx']

    query = 'update buyback set message_id = %d where idx = %d' % (message_id, result)
    cur.execute(query)
    conn.commit()
    dbconn(0)

def remove(message_id, user_id):
    query = 'select * from buyback where message_id = %d' % message_id
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)
    if result is None:
        return False
    if result is not None:
        if str(result['idx']) == 'None':
            return False

    if user_id in var.ID_director:
        query = 'delete from buyback where message_id = %d' % message_id
        dbconn()
        cur.execute(query)
        conn.commit()
        dbconn(0)
        return True

    else:
        if result['discord_id'] == user_id:
            query = 'delete from buyback where message_id = %d' % message_id
            dbconn()
            cur.execute(query)
            conn.commit()
            dbconn(0)
            return True
        else:
            return False


def approved(message_content, message_id, me):
    msg = message_content.split('\n')
    nick = msg[0].replace('인게임 이름: ','')
    total = int(msg[-1].replace('컨트랙 수량: ','').replace('컨트랙 총합: ','').replace(',','').replace('판매 금액: ',''))
    date = datetime.datetime.now().strftime("%Y.%m.%d")[2:]
    query = 'delete from buyback where message_id = %d' % message_id
    dbconn()
    cur.execute(query)
    conn.commit()
    dbconn(0)
    # if me:
    #     SPREADSHEET_ID = '1B7NjG0ksKD1y-tYUeewW5gY24E2bFvMqPPMUlHsJvlA'
    #     sheet = get_sheet()
    #     result = sheet.values().get(spreadsheetId=SPREADSHEET_ID, range='macqueen0987!B4:H').execute().get('values', [])
    #     num = len(result)
    #     resource = {"values": [[str(date), nick, '', total, '', '', '=H'+str(num+3)+'-E'+str(num+4)]]}
    #     sheet.values().append(spreadsheetId=SPREADSHEET_ID,  range='macqueen0987!B4:H',  body=resource,  valueInputOption="USER_ENTERED").execute()
    #     body = {"requests": [{"updateCells": {"range": {"sheetId": 770072509,"startRowIndex": num+3,"endRowIndex": num+4, "start_column_index": 0, "end_column_index" : 10},"rows": [{"values": [{"userEnteredFormat": {"backgroundColor": {"red": 1,"green":1,"blue" : 1}}}]}],"fields": "userEnteredFormat.backgroundColor"}}]}
    #     sheet.batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
    #     budget = int(sheet.values().get(spreadsheetId=SPREADSHEET_ID, range='macqueen0987!H'+str(num+4)+':H5'+str(num+4)).execute().get('values', [])[0][0].replace('₩','').replace(',',''))
    #     dbconn()
    #     query = 'update buyback_price set budget = %d' % budget
    #     cur.execute(query)
    #     conn.commit()
    #     dbconn(0)
    #     return [budget]



def update(message_id, discord_id):
    query = 'select idx from buyback where message_id = %d' % message_id
    dbconn()
    cur.execute(query)
    idx = cur.fetchone()['idx']
    query = 'select user_id from user_info where discord_id = %d' % discord_id
    cur.execute(query)
    result = cur.fetchone()
    web_id = ''
    if result is not None:
        if str(result['user_id']) != 'None':
            web_id = result['user_id']
    if len(web_id) > 0:
        query = 'update buyback set discord_id = %d, web_id= "%s" where idx = %d' % (discord_id, web_id, idx)
    else:
        query = 'update buyback set discord_id = %d, web_id=null where idx = %d' % (discord_id, idx)
    cur.execute(query)
    conn.commit()
    dbconn(0)


def set_budget(num):
    dbconn()
    query = 'update buyback_price set budget = %d' % num
    cur.execute(query)
    conn.commit()
    dbconn(0)


def add_budget(message):
    msg = message.content.replace('!장부입력 ', '')

    date = datetime.datetime.now().strftime("%Y.%m.%d")[2:]
    msg = msg.split(', ')
    nick = msg[0]
    income = int(msg[1])
    loss = int(msg[2])
    sold = int(msg[3])
    tax = int(msg[4])
    other = msg[5]
    profit = income-loss+sold-tax
    if income == 0:
        income = ''
    if loss == 0:
        loss = ''
    if sold == 0:
        sold = ''
    if tax == 0:
        tax = ''

    SPREADSHEET_ID = '1B7NjG0ksKD1y-tYUeewW5gY24E2bFvMqPPMUlHsJvlA'
    sheet = get_sheet()
    result = sheet.values().get(spreadsheetId=SPREADSHEET_ID, range='macqueen0987!B4:H').execute().get('values', [])
    num = len(result)
    resource = {"values": [[str(date), nick, income, loss, sold, tax, '=H'+str(num+3)+'+D'+str(num+4)+'-E'+str(num+4)+'+F'+str(num+4)+'-G'+str(num+4), other]]}
    sheet.values().append(spreadsheetId=SPREADSHEET_ID,  range='macqueen0987!B4:I',  body=resource,  valueInputOption="USER_ENTERED").execute()
    body = {"requests": [{"updateCells": {"range": {"sheetId": 770072509,"startRowIndex": num+3,"endRowIndex": num+4, "start_column_index": 0, "end_column_index" : 10},"rows": [{"values": [{"userEnteredFormat": {"backgroundColor": {"red": 1,"green":1,"blue" : 1}}}]}],"fields": "userEnteredFormat.backgroundColor"}}]}
    sheet.batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
    if profit > 0:
        for i in range(9):
            body = {"requests": [{"updateCells": {"range": {"sheetId": 770072509,"startRowIndex": num+3,"endRowIndex": num+4, "start_column_index": i, "end_column_index" : i+1},"rows": [{"values": [{"userEnteredFormat": {"backgroundColor": {"red": 0,"green":1,"blue" : 1}}}]}],"fields": "userEnteredFormat.backgroundColor"}}]}
            sheet.batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
    budget = int(sheet.values().get(spreadsheetId=SPREADSHEET_ID, range='macqueen0987!H'+str(num+4)+':H'+str(num+4)).execute().get('values', [])[0][0].replace('₩','').replace(',',''))
    dbconn()
    query = 'update buyback_price set budget = %d' % budget
    cur.execute(query)
    conn.commit()
    dbconn(0)
    return budget


def update_price(bypass = False):
    dbconn()
    query = 'select updated from buyback_price'
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)
    elapse = datetime.datetime.now() - result['updated']
    elapse = divmod(elapse.total_seconds(), 3600)[0]
    # print(elapse)
    if elapse > 12 or bypass:

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


        SPREADSHEET_ID = '1sPgrotVoifLTRbAiwH2trjVXZlKLLpXpfg2iI-wDzR0'
        temp_arr = []
        ranges = ['Sell Contract Calculator!A6:D21', 'Sell Contract Calculator!E6:H13','Sell Contract Calculator!A24:D40', 'Sell Contract Calculator!E24:H40']
        for i in ranges:
            result = sheet.values().get(spreadsheetId=SPREADSHEET_ID, range=i).execute().get('values', [])
            for j in result:
                temp_arr.append([j[0],int(round(float(j[3].replace(',',''))*var.buyback_precentage))])

        query = 'select * from buyback_price'
        dbconn()
        cur.execute(query)
        result = cur.fetchone()
        dbconn(0)

        ore_arr = json.loads(result['ore'])
        mineral_arr = json.loads(result['mineral'])
        pi1_arr = json.loads(result['pi1'])
        pi2_arr = json.loads(result['pi2'])
        db_arr = [ore_arr, mineral_arr, pi1_arr, pi2_arr]

        for arr in db_arr:
            for i in range(len(arr)):
                for j in range(len(temp_arr)):
                    if arr[i][0].lower() == temp_arr[j][0].lower():
                        arr[i][1] = temp_arr[j][1]

        db_arr[1][0][1] = 1
        # print(db_arr[1])

                

        query = 'update buyback_price set updated = now(), ore = \'%s\', mineral = \'%s\', pi1 = \'%s\', pi2 = \'%s\'' % (json.dumps(db_arr[0]),json.dumps(db_arr[1]),json.dumps(db_arr[2]),json.dumps(db_arr[3]))
        dbconn()
        cur.execute(query)
        conn.commit()
        dbconn(0)
        
