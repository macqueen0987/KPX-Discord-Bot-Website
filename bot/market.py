import requests
from bs4 import BeautifulSoup
import re
import pymysql
import json
import var
import datetime

conn = None
cur = None

commands = ['!마켓']
def dbconn(num = 1):
    global conn, cur
    if num == 1:
        conn = pymysql.connect(host = "macqueen0987.kro.kr", port = 3306, user = "EVE", passwd = "password", db = "EVE", charset = "utf8")
        cur = conn.cursor(pymysql.cursors.DictCursor)
    else:
        conn.commit()
        conn.close()

def cache():
    dbconn()
    query = 'select updated from market'
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)
    elapse = datetime.datetime.now() - result['updated']
    elapse = divmod(elapse.total_seconds(), 60)[0]
    if elapse > 10:
        url1 = "http://api.falz.io:7777/market/manufacture/getFetchList"
        url2 = 'http://api.falz.io:7777/market/manufacture/getAllLatestOrders'
        srd_code1 = requests.get(url1).text
        soup1 = str(BeautifulSoup(srd_code1, 'lxml'))
        soup1 = re.sub('<.+?>', '', soup1, 0).strip()

        srd_code2 = requests.get(url2).text
        soup2 = str(BeautifulSoup(srd_code2, 'lxml'))
        soup2 = re.sub('<.+?>', '', soup2, 0).strip()


        dbconn()
        query = "update market set updated = now(), list='%s', orders='%s'" % (soup1.replace("'",'_'), soup2.replace("'",'_'))
        cur.execute(query)
        conn.commit()
        dbconn(0)


def search(product):
    dbconn()
    query = 'select * from market'
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)
    listed = json.loads(result['list'].replace('_',"'"))
    orders = json.loads(result['orders'])
    sell_price = 0
    buy_price = 0
    found = False

    for i in listed.keys():
        if listed[i].lower().replace(' ','') == product.lower().replace(' ',''):
            # print(i)
            sell_order = orders[i]['sellOrders']
            buy_order = orders[i]['buyOrders']
            cnt = 0
            num = 0
            for j in sell_order:
                num += j['price'] * j['vol_entered']
                cnt += j['vol_entered']
            sell_price = num//cnt

            cnt = 0
            num = 0
            for j in buy_order:
                num += j['price'] * j['vol_entered']
                cnt += j['vol_entered']
            buy_price = num//cnt
            found = True
            break

    if found:
        return 'Sell: %s ISK\nBuy: %s ISK' % (format(sell_price), format(buy_price))
    else:
        return '해당 품목을 찾을수 없습니다!'

def main(message):
    msg = message.content
    command = msg.split(' ')[0]
    msg = msg.replace(msg.split(' ')[0] + ' ', '')
    msg = search(msg)
    return msg