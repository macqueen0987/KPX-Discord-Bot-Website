from __future__ import print_function
import var
import pymysql
import pickle
import os.path
from googleapiclient.discovery import build
from google.auth.transport.requests import Request
import datetime
import json
import re
from time import sleep


conn = None
cur = None

def dbconn(num = 1):
    global conn, cur
    if num == 1:
        conn = pymysql.connect(host = "macqueen0987.kro.kr", port = 3306, user = "EVE", passwd = "password", db = "EVE", charset = "utf8")
        cur = conn.cursor(pymysql.cursors.DictCursor)
    else:
        conn.commit()
        conn.close()


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


def remove(message_id, user_id):
    query = 'select * from WOP where message_id = %d' % message_id
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
        query = 'delete from WOP where message_id = %d' % message_id
        dbconn()
        cur.execute(query)
        conn.commit()
        dbconn(0)
        return True

    else:
        if result['user_id'] == user_id:
            query = 'delete from WOP where message_id = %d' % message_id
            dbconn()
            cur.execute(query)
            conn.commit()
            dbconn(0)
            return True
        else:
            return False



def approved(message_content, message_id, me):
    query = 'select discord_id from WOP where message_id = %d' % message_id
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)
    discord_id = None
    if result is not None:
        if str(result['discord_id']) != 'None':
            discord_id = result['discord_id']
    msg = message_content.split('\n')
    nick = msg[0].replace('인게임 이름: ','')
    product = msg[1]
    for i in ['WTB ','WOP ', ' X1', ' X 1', 'x 1']:
        product = product.replace(i,'')
    date = datetime.datetime.now().strftime("%Y.%m.%d")[2:]
    query = 'delete from WOP where message_id = %d' % message_id
    dbconn()
    cur.execute(query)
    conn.commit()
    dbconn(0)
    if me:
        dbconn()
        query = 'select price_json from WOP_price'
        cur.execute(query)
        result = cur.fetchone()
        dbconn(0)

        for i in json.loads(result['price_json']):
            for j in i:
                if j[0].lower() == product.lower():
                    value = int(j[1].replace(' ISK','').replace(',',''))
        SPREADSHEET_ID = '1B7NjG0ksKD1y-tYUeewW5gY24E2bFvMqPPMUlHsJvlA'
        sheet = get_sheet()
        result = sheet.values().get(spreadsheetId=SPREADSHEET_ID, range='macqueen0987!B4:H').execute().get('values', [])
        num = len(result)
        resource = {"values": [[str(date), nick, '', '', value, round(value*0.04,0), '=H'+str(num+3)+'-G'+str(num+4)+'+F'+str(num+4),product]]}
        sheet.values().append(spreadsheetId=SPREADSHEET_ID,  range='macqueen0987!B4:H',  body=resource,  valueInputOption="USER_ENTERED").execute()

        body = {"requests": [{"updateCells": {"range": {"sheetId": 770072509,"startRowIndex": num+3,"endRowIndex": num+4, "start_column_index": 0, "end_column_index" : 10},"rows": [{"values": [{"userEnteredFormat": {"backgroundColor": {"red": 1,"green":1,"blue" : 1}}}]}],"fields": "userEnteredFormat.backgroundColor"}}]}
        sheet.batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()

        for i in range(9):
            body = {"requests": [{"updateCells": {"range": {"sheetId": 770072509,"startRowIndex": num+3,"endRowIndex": num+4, "start_column_index": i, "end_column_index" : i+1},"rows": [{"values": [{"userEnteredFormat": {"backgroundColor": {"red": 0,"green":1,"blue" : 1}}}]}],"fields": "userEnteredFormat.backgroundColor"}}]}
            sheet.batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
            
        budget = int(sheet.values().get(spreadsheetId=SPREADSHEET_ID, range='macqueen0987!H'+str(num+4)+':H'+str(num+4)).execute().get('values', [])[0][0].replace('₩','').replace(',',''))
        dbconn()
        query = 'update buyback_price set budget = %d' % budget
        cur.execute(query)
        conn.commit()
        dbconn(0)

    else:
        budget = None

    return [budget, discord_id]


def search_wop(message):
    dbconn()
    query = 'select price_json from WOP_price'
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)

    msg = message.content
    msg = msg.replace('!wop ','').replace('!WOP ','')

    nick = ''
    if '[' in msg and ']' in msg:
        nick = re.sub('\[.*?\]', '', str(msg))
        nick = msg.replace(nick, '').replace('[','').replace(']','')
        msg = msg.replace(nick,'').replace('[','').replace(']','')

    found = False
    type_=''
    types = ['trainer','issue','assault','guardian','covert']
    for i in types:
        if i in msg:
            msg.replace(i,'')
            type_ = i
    for i in json.loads(result['price_json']):
        for j in i:
            if msg.lower().replace(' ','') in j[0].lower().replace(' ',''):
                if len(type_) < 1:
                    for i in types:
                        if i not in j[0].lower().replace(' ',''):
                            ship = j[0]
                            found = True
    
                else:
                    if type_ in j[0].lower().replace(' ',''):
                        ship = j[0]
                        found = True

    if not found:
        return {'type':'error','msg': '해당 함선은 없습니다.'}

    
    query = 'select user_id from user_info where discord_id = %d' % message.author.id
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    web_id = ''
    if result is not None:
        if str(result['user_id'] != 'None'):
            web_id = result['user_id']

    if len(web_id) > 0:
        if len(nick) > 0:
            query = 'insert into WOP(discord_id, web_id, ingame_id, product) values(%d, "%s", "%s", "%s")' % (message.author.id, web_id, nick, ship)
        else:
            query = 'insert into WOP(discord_id, web_id, product) values(%d, "%s", "%s")' % (message.author.id, web_id, ship)

    else:
        if len(nick) > 0:
            query = 'insert into WOP(discord_id, ingame_id, product) values(%d, "%s", "%s")' % (message.author.id, nick, ship)
        else:
            query = 'insert into WOP(discord_id, product) values(%d, "%s")' % (message.author.id, ship)

    cur.execute(query)
    conn.commit()
    cur.execute('select idx from WOP order by idx desc limit 1')
    idx = cur.fetchone()['idx']
    dbconn(0)

    if len(nick) < 1:
        nick = message.author.nick
        if nick is None:
            nick = message.author.id
        nick = re.sub('\(.+?\)', '', str(nick)).replace('[KPX] ','').replace('[KPX]','')  # noqa: W605
    return {'type':'msg', 'msg':'인게임 이름: %s\nWTB WOP %s' % (nick, ship), 'idx':idx}


def add(message_id, idx):
    query = 'update WOP set message_id = %d where idx = %d' % (message_id, idx)
    dbconn()
    cur.execute(query)
    conn.commit()
    dbconn(0)