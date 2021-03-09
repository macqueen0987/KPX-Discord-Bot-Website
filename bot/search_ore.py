import gspread
from oauth2client.service_account import ServiceAccountCredentials
import var
import requests
from bs4 import BeautifulSoup
import re
import discord
from random import randint
import json
import pymysql

ore = None
pi = None
mineral = None
conn = None
cur = None

ore_price_commands = ['!광물시세']
ore_calculator_commands = ['!광물계산기', '!바이백']
commands = ore_price_commands + ore_calculator_commands

def dbconn(num = 1):
    global conn, cur
    if num == 1:
        conn = pymysql.connect(host = "macqueen0987.kro.kr", port = 3306, user = "EVE", passwd = "password", db = "EVE", charset = "utf8")
        cur = conn.cursor(pymysql.cursors.DictCursor)
    else:
        conn.commit()
        conn.close()

def read_sheet():
    global ore, pi, mineral

    dbconn()
    query = 'select * from buyback_price'
    cur.execute(query)
    result = cur.fetchone()
    dbconn(0)

    result_ore = json.loads(result['ore'])
    result_mineral = json.loads(result['mineral'])
    result_pi = json.loads(result['pi1']) + json.loads(result['pi2'])

    dic = {}
    ore = []
    pi = []
    mineral = []
    for i in result_ore:
        ore.append(i[0])
        dic[i[0]] = int(round(float(i[1]),0))

    for i in result_mineral:
        ore.append(i[0])
        dic[i[0]] = int(round(float(i[1]),0))

    for i in result_pi:
        ore.append(i[0])
        dic[i[0]] = int(round(float(i[1]),0))

    return dic


# def find_image(name):
#     if name.lower() in str(var.ores['ore']).lower():
#         url = "https://wiki.eveuniversity.org/File:Ore_"+name.lower().replace(' ','')+".png"

#     elif name.lower() in str(var.ores['minerals']).lower():
#         url = "https://wiki.eveuniversity.org/File:Mineral_"+name.lower().replace(' ','')+".png"

#     srd_code = requests.get(url).text
#     soup = BeautifulSoup(srd_code, 'lxml')
#     today = soup.find_all('td')

#     href = str(today[1].find_all('a')).split('"')[1]
#     return "https://wiki.eveuniversity.org"+href


def ore_price(message):
    found = False
    list_of_hashes = read_sheet()
    if list_of_hashes == 'CON_ERROR':
        return list_of_hashes

    for i in list_of_hashes:
        if message.lower() in i.lower():
            found = True
            dic = {}
            dic['title'] = i
            dic['values'] = list_of_hashes[i]
            break

    if found:
        dic['image'] = 'http://macqueen0987.kro.kr/img/materials/%s.png' % dic['title'].lower().replace(' ','')


        return {'type': 'price', 'value' : dic}
    else:
        return {'type' : 'ERROR', 'value' : {'title' : message, 'text' : '이런 광물은 없습니다!'}}
        
        
def ore_calculator(message):
    msg = message.content.split(' ')[0]
    msg = message.content.replace(msg+' ','')
    list_of_hashes = read_sheet()
    cnt = {}
    cnt['ore'] = False; cnt['mineral'] = False; cnt['pi'] = False
    if list_of_hashes == 'CON_ERROR':
        return list_of_hashes

    msg = str(msg)

    nick = ''
    if '[' in msg and ']' in msg:
        nick = msg.replace(re.sub('\[.*?\]', '', str(msg)), '').replace('[','').replace(']','')
        msg = re.sub('\[.*?\]', '', str(msg))
        # print(nick)
    
    for i in [', ', ' ,', '\n ', ' \n']:
        # print(i)
        while i in msg:
            msg = msg.replace(i,i.replace(' ',''))
    if '\n' in msg:
        msg = msg.split('\n')
    else:
        msg = msg.split(',')

    temp = {}
    for i in msg:
        i = i.split(' ')
        try:
            temp[str(i[:-1]).replace('[','').replace(']','').replace(',', '').replace("'", '')] = int(i[-1])
        except Exception as e:  # noqa: F841
            return {'type' : 'ERROR' ,'value' : {'title' : '입력오류', 'text' : '숫자와 광물 이름을 한칸 띄워주세요'}}

    temp_arr = [[],[],[]]
    json_arr = {}
    json_arr_ore = {}
    json_arr_mineral = {}
    json_arr_pi = {}
    not_found = ''
    found = False
    error = False
    for i in temp.keys():
        found = False
        for j in list_of_hashes:
            if i.lower().replace(' ','') in j.lower().replace(' ','') and j.lower().replace(' ','') != '':
                found = True
                if j in ore:
                    num = 0
                    cnt['ore'] = True
                    json_arr_ore[j] = int(temp[i]*int(list_of_hashes[j]))
                if j in pi:
                    num = 1
                    cnt['pi'] = True
                    json_arr_pi[j] = int(temp[i]*int(list_of_hashes[j]))
                if j in mineral:
                    num = 2
                    cnt['mineral'] = True
                    json_arr_mineral[j] = int(temp[i]*int(list_of_hashes[j]))
                if calc_type == 1:
                    temp_arr[num].append({'col1' : j, 'col2' : str(int(temp[i]))+'\\*'+str(int(list_of_hashes[j])), 'col3' : int(temp[i]*int(list_of_hashes[j]))})
                else:
                    temp_arr[num].append({'text' : j + ': ' + str(int(temp[i])), 'num' : int(temp[i]*int(list_of_hashes[j]))})
                break

        if found is False:
            not_found += ' ' + i
            # print('not found: ' + str(i))
            error = True

    if error:
        return {'type' : 'ERROR' ,'value' : {'title' : '입력오류', 'text' : '입력하신 광물중 스프레드시트에 없는 광물이 있습니다! 다시 확인해 주세요 (%s)' % not_found}}

    dic = [{},{},{}]
    for j in range(3):
        num = 0
        if calc_type == 1:
            dic[j]['col1'] = ''
            dic[j]['col2'] = ''
            dic[j]['col3'] = ''
            for i in temp_arr[j]:
                num += int(i['col3'])
                dic[j]['col1'] += i['col1'] + '|'
                dic[j]['col2'] += i['col2'] + '|'
                dic[j]['col3'] += format(i['col3'], ',') + '|'

            dic[j]['num'] = num
            dic[j]['col1'] = dic[j]['col1'][:-1]
            dic[j]['col2'] = dic[j]['col2'][:-1]
            dic[j]['col3'] = dic[j]['col3'][:-1]

        else:
            dic[j]['text'] = ''
            for i in temp_arr[j]:
                num += int(i['num'])
                dic[j]['text'] += i['text'] + '|'

            dic[j]['num'] = num
            dic[j]['text'] = dic[j]['text'][:-1]


    if calc_type == 1:
        return create_embed({'type' : 'calc', 'value' : dic, 'cnt' : cnt})
    else:
        num = 0
        for i in dic:
            num += i['num']

        if dic[0]['num'] > 0:
            json_arr_ore['ore_total'] = dic[0]['num']
        if dic[1]['num']:
            json_arr_mineral['mineral_total'] = dic[1]['num']
        if dic[2]['num']:
            json_arr_pi['pi_total'] = dic[2]['num']

        json_arr['ore'] = json_arr_ore
        json_arr['mineral'] = json_arr_mineral
        json_arr['pi'] = json_arr_pi
        json_arr['total'] = num
        json_arr = str(json_arr)

        user_id = ''
        discord_id = message.author.id
        query = 'select user_id from user_info where discord_id = %d' % discord_id
        dbconn()
        cur.execute(query)
        result = cur.fetchone()
        if result is not None:
            user_id = result['user_id']

        if len(user_id) > 1:
            if len(nick) > 0:
                query = 'insert into buyback(discord_id, web_id, ingame_id, list_json) values(%d, "%s", "%s", "%s")' % (discord_id, user_id, nick, json_arr)
            else:
                query = 'insert into buyback(discord_id, web_id, list_json) values(%d, "%s", "%s")' % (discord_id, user_id, json_arr)
        else:
            if len(nick) > 0:
                query = 'insert into buyback(discord_id, ingame_id, list_json) values(%d, "%s", "%s")' % (discord_id, nick, json_arr)
            else:
                query = 'insert into buyback(discord_id, list_json) values(%d, "%s")' % (discord_id, json_arr)

        cur.execute(query)
        conn.commit()
        dbconn(0)

        return create_embed({'type' : 'buyback', 'value' : dic, 'cnt' : cnt})

calc_type = 1
def main(message):
    global calc_type
    msg = message.content.split(' ')[0]
    other = message.content.replace(msg+' ','')
    if msg in ore_price_commands:
        msg = ore_price(other)

    if msg in ore_calculator_commands:
        if msg == '!바이백':
            if message.channel.id not in [750911293388750909, 534057557069660181]:
                return {'type' : 'ERROR', 'value':{'title':'채널오류', 'text' : '알맞은 채널을 사용해 주세요!'}}
            else:
                calc_type = 0
        else:
            calc_type = 1
        msg = ore_calculator(message)

    if msg == 'CON_ERROR':
        return {'type' : 'ERROR', 'value':{'title':'연결오류', 'text' : '구글 스프레드시트에 연결할수 없습니다... 잠시후 다시 시도하세요.'}}
    return msg


def create_embed(msg):
    if msg['type'] == 'calc':
        num = 0
        cnt = msg['cnt']
        msg = msg['value']
        color = randint(0, 0xFFFFFF)
        embed=discord.Embed(title='광물계산기', description='모든 광물값은 얼라 바이백가로 계산되었습니다.', color=color)
        if cnt['ore'] is True:
            embed.add_field(name="Raw Ore", value=msg[0]['col1'].replace('|', '\n'), inline=True)
            embed.add_field(name="수량*가격", value=msg[0]['col2'].replace('|', '\n'), inline=True)
            embed.add_field(name="종류별 가격", value=msg[0]['col3'].replace('|', '\n'), inline=True)
            num += msg[0]['num']
            if True in [cnt['mineral'], cnt['pi']]:
                embed.add_field(name='Raw Ore 총합', value=format(msg[0]['num'],','), inline=False)
            else:
                embed.add_field(name="총합", value=format(msg[0]['num'],','))
        if cnt['mineral'] is True:
            embed.add_field(name="Mineral", value=msg[2]['col1'].replace('|', '\n'), inline=True)
            embed.add_field(name="수량*가격", value=msg[2]['col2'].replace('|', '\n'), inline=True)
            embed.add_field(name="종류별 가격", value=msg[2]['col3'].replace('|', '\n'), inline=True)
            num += msg[2]['num']
            if True in [cnt['mineral'], cnt['pi']]:
                embed.add_field(name='Mineral 총합', value=format(msg[2]['num'],','), inline=False)
            else:
                embed.add_field(name="총합", value=format(msg[2]['num'],','))
        if cnt['pi'] is True:
            embed.add_field(name="PI", value=msg[1]['col1'].replace('|', '\n'), inline=True)
            embed.add_field(name="수량*가격", value=msg[1]['col2'].replace('|', '\n'), inline=True)
            embed.add_field(name="종류별 가격", value=msg[1]['col3'].replace('|', '\n'), inline=True)
            num += msg[1]['num']
            if True in [cnt['ore'], cnt['pi']]:
                embed.add_field(name='PI 총합', value=format(msg[1]['num'], ','), inline=False)
            else:
                embed.add_field(name="총합", value=format(msg[1]['num'],','), inline=False)
        if sum([cnt['ore'], cnt['mineral'], cnt['pi']]) > 1:
            embed.add_field(name="총합", value=format(num,','))
        embed.set_footer(text="언제나 기능 건의는 환영입니다!!")

        return {'type' : 'calc', 'embed':embed}

    elif msg['type'] == 'buyback':
        cnt = msg['cnt']
        msg = msg['value']
        num = 0

        new_message = ''
        # new_message = '인게임 이름: ' + re.sub('\(.+?\)', '', str(nick)).replace('[KPX]','') +'\n'  # noqa: W605
        if cnt['ore'] is True:
            new_message += '바이백 대상: Ore\n바이백 수량: ' + msg[0]['text'].replace('|', '\n                       ') + '\n'
            new_message += '컨트랙 수량: ' + str(format(msg[0]['num'], ','))
            num += msg[0]['num']
            if cnt['mineral'] is True or cnt['pi'] is True:
                new_message += '\n\n'
        if cnt['mineral'] is True:
            new_message += '바이백 대상: Minerals\n바이백 수량: ' + msg[2]['text'].replace('|', '\n                       ') + '\n'
            new_message += '컨트랙 수량: ' + str(format(msg[2]['num'], ','))
            num += msg[2]['num']
            if cnt['pi'] is True:
                new_message += '\n\n'
        if cnt['pi'] is True:
            new_message += '바이백 대상: Planetary Material\n바이백 수량: ' + msg[1]['text'].replace('|', '\n                       ') + '\n'
            new_message += '컨트랙 수량: ' + str(format(msg[1]['num'],','))
            num += msg[1]['num']
        if sum([cnt['ore'], cnt['mineral'], cnt['pi']]) > 1:
            new_message += '\n\n컨트랙 총합: ' + str(format(num, ','))

        return {'type' : 'buyback', 'message' : new_message}