from __future__ import print_function
import var
import pymysql
import pickle
import os.path
from googleapiclient.discovery import build
from google.auth.transport.requests import Request
import datetime
import json
from PIL import Image
import io

conn = None
cur = None

activate_command = ['!활성화']
commands = activate_command


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


def main(message):
    msg = message.content
    command = msg.split(' ')[0]
    
    if command in activate_command:
        msg = activate(message)

    return msg


def updpate():
    pass



def activate(message):
    kpx_member = False
    director = False
    for i in message.author.roles:
        if i.name == 'Condor':
            kpx_member = True
        
        if i.name == 'Director':
            director = True

    if not kpx_member:
        return '아직 KPX의 멤버가 아닙니다! 디렉진에게 문의해 주세요'

    try:
        web_id = message.content.split(' ')[1]
    except Exception as e:
        pass
    discord_id = message.author.id

    query = 'select exists(select * from user_info where discord_id = "%d") as exist' % discord_id
    dbconn()
    cur.execute(query)
    if cur.fetchone()['exist']:
        if director:
            query = 'update user_info set activated = true, user_type = 1, activated_datetime = now() where discord_id = %d' % discord_id
        else:
            query = 'update user_info set activated = true, user_type = 0, activated_datetime = now() where discord_id = %d' % discord_id
        cur.execute(query)
        conn.commit()
        dbconn(0)
        return '성공적으로 활성화 되었습니다.'
    else:
        dbconn(0)

    try:
        query = 'select exists(select * from user_info where user_id = "%s") as exist' % web_id
    except Exception as e:
        return "웹에 가입된 아이디를 입력해주세요!"
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)
    if not result:
        return '아직 가입이 안된 아이디 입니다. 다시 한번 확인해 주세요.'


    dbconn()
    if director:
        query = 'update user_info set activated = true, user_type = 1, discord_id = %d, activated_datetime = now() where user_id = "%s"' % (discord_id, web_id)
    else:
        query = 'update user_info set activated = true, user_type = 0, discord_id = %d, activated_datetime = now() where user_id = "%s"' % (discord_id, web_id)

    cur.execute(query)
    conn.commit()

    return '성공적으로 활성화 되었습니다.'


def check_web_buyback():
    query = 'delete from buyback where discord_id is null'
    dbconn()
    cur.execute(query)
    conn.commit()
    query = 'select * from buyback where message_id is null'
    cur.execute(query)
    results = cur.fetchall()
    dbconn(0)

    temp = []
    msg = ''
    for result in results:
        msg = ''
        user = result['discord_id']
        arr = json.loads(result['list_json'].replace("'",'"'))
        if len(arr['ore']) > 0:
            msg += '바이백 대상: Ore\n바이백 수량: '
            for i in arr['ore'].keys():
                if i != 'ore_total':
                    msg += '%s: %d|' % (i, arr['ore'][i])
            msg = msg[:-1]
            if (len(arr['mineral']) + len(arr['pi'])) > 0:
                msg += '\n컨트랙 수량: %s' % format(int(arr['ore']['ore_total']),',')

        if len(arr['mineral']) > 0:
            if len(arr['ore']) > 0 :
                msg += '\n\n'
            msg += '바이백 대상: Mineral\n바이백 수량: '
            for i in arr['mineral'].keys():
                if i != 'mineral_total':
                    msg += '%s: %d|' % (i, arr['mineral'][i])
            msg = msg[:-1]
            if (len(arr['ore']) + len(arr['pi'])) > 0:
                msg+= '\n컨트랙 수량: %s' % format(int(arr['mineral']['mineral_total']),',')

        if len(arr['pi']) > 0:
            if (len(arr['ore']) + len(arr['mineral'])) > 0:
                msg+= '\n\n'
            msg += '바이백 대상: Pi\n바이백 수량: '
            for i in arr['pi'].keys():
                if i != 'pi_total':
                    msg += '%s: %d|' % (i, arr['pi'][i])
            msg = msg[:-1]
            if (len(arr['ore']) + len(arr['mineral'])) > 0:
                msg += '\n컨트랙 수량: %s' % format(int(arr['pi']['pi_total']) ,',')

        if (len(arr['ore']) + len(arr['mineral']) + len(arr['pi'])) > 0:
            msg+= '\n\n'
            msg += '컨트랙 총합: ' + str(format(arr['total'], ','))

        msg = msg.replace('|', '\n                       ').replace('_',' ')

        temp.append([user, msg, result['idx']])
    return temp

def web_add(message_id, idx, db):
    query = 'update %s set message_id = %d where idx = %d' % (db, message_id, idx)
    dbconn()
    cur.execute(query)
    if db == 'WOP':
        query = 'update WOP set kill_log = null, killed = true where idx = %d' % idx
        cur.execute(query)
        conn.commit()
    dbconn(0)



def check_web_wop():
    # print('getting')
    img_num = 0
    query = 'select * from WOP where message_id is null'
    dbconn()
    cur.execute(query)
    results = cur.fetchall()
    # print(results)
    dbconn(0)
    arr = []
    for result in results:
        # print(result)
        # print(str(result['kill_log']))
        temp = {}
        temp['idx'] = result['idx']
        if len(str(result['kill_log'])) > 10:
            img = io.BytesIO(result['kill_log'])
            img=Image.open(img)
            width, height = img.size
            # print(width,height)

            background = Image.new('RGB', (width, height), (255, 255, 255))
            background.paste(img)
            background.save(str(img_num)+'.jpg')
            temp['img'] = str(img_num)+'.jpg'
            img_num = img_num + 1

        if str(result['ingame_id']) != 'None':
            temp['ingame_id'] = result['ingame_id']
        else:
            temp['discord_id'] = result['discord_id']

        msg = 'WTB WOP %s' % result['product']
        temp['msg'] = msg
        arr.append(temp)

    # print(arr)
    return arr