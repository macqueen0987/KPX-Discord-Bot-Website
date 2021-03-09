import var
import pymysql
import random
import string

conn = None
cur = None
reserve_commands = ['!예약']
confirm_commands = ['!예약수락', '!예약거절']
log_commands = ['!예약기록']
complete_commands = ['!예약완료']
change_my_stats_command = ['!예약차단', '!예약허용', '!예약상태']
show_commands = ['!거절예약보기', '!예약보기', '!미확인예약보기', '!완료예약보기']
commands = reserve_commands + confirm_commands + log_commands + complete_commands + change_my_stats_command + show_commands

def dbconn(num = 1):
    global conn, cur
    if num == 1:
        conn = pymysql.connect(host = "macqueen0987.kro.kr", port = 3306, user = "EVE", passwd = "password", db = "EVE", charset = "utf8")
        cur = conn.cursor(pymysql.cursors.DictCursor)
    else:
        conn.commit()
        conn.close()

def main(message):
    global conn, cur
    msg = message.content.split(' ')[0]
    if msg in reserve_commands:
        msg = reserve(message)

    elif msg in confirm_commands:
        msg = confirm(message)

    elif msg in change_my_stats_command:
        msg = change_my_stats(message)

    elif msg in complete_commands:
        msg = complete(message)

    elif msg in show_commands:
        msg = show(message)

    return msg


def confirm(message):
    global conn, cur
    randkey = message.content.split(' ')[1].replace(' ','')
    if '수락' in message.content:
        stats = 1
        ending = '받아들이셨습니다'
    elif '거절' in message.content:
        stats = 2
        ending = '거절하셨습니다'
    else:
        return {'type' : 'error', 'title':'형식에러', 'text' : '![예약수락/예약거절] [고유키] 의 형식을 지켜주세요'}
    
    query = 'select completed from reserve where order_key = "%s"' % randkey
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    dbconn(1)
    if result is None:
        return {'type' : 'error', 'title':'코드 에러', 'text' : '고유코드가 잘못되었습니다. 다시 확인해 보세요.'}

    dbconn()
    query = 'update reserve set accepted = %d, accepted_time = now() where order_key = "%s"' % (stats, randkey)
    cur.execute(query)
    conn.commit()
    dbconn(1)

    dbconn()
    query = 'select * from reserve where order_key = "%s"' % randkey
    cur.execute(query)
    result = cur.fetchone()
    dbconn(1)
    producer = int(result['producer'])
    consumer = int(result['consumer'])
    product = result['product']
    price = int(result['price'])
    quantity = int(result['quantity'])
    text = ' 님이 귀하께서 주문하신 %s를(을) 개당 %d의 가격으로 %d개 의 예약을 %s' % (product, price, quantity, ending)
    return {'type':'success', 'value' : {'text' : text, 'send_to': consumer, 'mention' : producer}}


def reserve(message):
    global conn, cur
    if len(message.content.split(',')) < 3 or len(message.content.split(',')) > 3:
        return {'type' : 'error', 'title':'형식에러', 'text' : '!예약 [사용자멘션] [상품], [가격], [수량] 을 지켜주세요'}
    msg = message.content

    producer = message.content.split(' ')[1]
    consumer = message.author.id

    msg = msg.replace(producer + ' ','').replace(msg.split(' ')[0] + ' ','')
    for i in ['<', '>', '@', '!']:
        producer = producer.replace(i,'')

    producer = int(producer)

    dbconn()
    query = 'select stats from reserve_stats where id = %d' % producer
    cur.execute(query)
    result = cur.fetchone()
    dbconn(1)
    if result is not None:
        if result['stats'] == 1:
            return {'type' : 'error', 'title':'판매자가 예약을 차단함', 'text' : '판매자가 현재는 예약을 받을수 없는 상태입니다.'}

    msg = msg.replace(', ',',')
    product = msg.split(',')[0]
    price = int(msg.split(',')[1])
    quantity = int(msg.split(',')[2])
    # print(consumer, producer, product, price, quantity)

    dbconn()
    randkey = ''
    for i in range(5):
        randkey += str(random.choice(string.ascii_letters+'1234567890'))
    query = 'select order_key from reserve'
    cur.execute(query)
    result = cur.fetchall()
    temp_arr = []
    for i in result:
        temp_arr.append(i['order_key'])
    while True:
        if randkey not in temp_arr:
            break

        else:
            for i in range(5):
                randkey += str(random.choice(string.ascii_letters+'1234567890'))


    query = 'insert into reserve(producer, consumer, order_key, product, quantity, price) values(%d, %d, "%s", "%s", %d, %d)' % (producer, consumer, randkey, product, quantity, price)
    cur.execute(query)
    dbconn(2)

    value = " 님이 %s 를 %d 의 가격에 %d 만큼 주문하셨습니다.\n\n수락: !예약수락 %s\n거절: !예약거절 %s" % (product, price, quantity, randkey, randkey)
    return {'type':'success', 'value' : {'text' : value, 'send_to': producer, 'mention' : consumer}}


def change_my_stats(message):
    msg = message.content
    my_id = int(message.author.id)

    if '차단' in msg:
        stats = 1
    elif '허용' in msg:
        stats = 0
    elif '상태' in msg:
        dbconn()
        query = 'select stats from reserve_stats where id = %d' % my_id
        cur.execute(query)
        result = cur.fetchone()
        dbconn(1)
        if len(result) < 0:
            return {'type':'simple', 'text' : '먼저 허용, 차단중 하나를 해주세요!'}
        stats = result['stats']
        if stats == 1:
            text = '현재 예약을 차단하는 상태입니다'
        else:
            text = '현재 예약을 허용하는 상태입니다'
        return {'type':'simple', 'text' : text}
    
    dbconn()
    query = 'select * from reserve_stats where id = %d' % my_id
    cur.execute(query)
    result = cur.fetchone()
    dbconn(1)
    if result is None:
        query = 'insert into reserve_stats(id, stats) values(%d, %d)' % (my_id, stats)
    else:
        query = 'update reserve_stats set stats = %d where id = %d' % (stats, my_id)

    dbconn()
    cur.execute(query)
    conn.commit()
    dbconn(1)

    return {'type':'simple', 'text' : '성공적으로 변경하였습니다.'}


def complete(message):
    msg = message.content
    if len(msg.split(' ')) < 2:
        return {'type' : 'error', 'title':'형식에러', 'text' : '!예약완료 [고유키] 의 형식을 지켜주세요'}

    key = msg.split(' ')[1].replace(' ','')

    query = 'select completed from reserve where order_key = "%s"' % key
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    dbconn(1)
    if result is None:
        return {'type' : 'error', 'title':'코드 에러', 'text' : '고유코드가 잘못되었습니다. 다시 확인해 보세요.'}
    if int(result['completed']) == 1:
        return {'type' : 'error', 'title':'이미 완료됨', 'text' : '이미 완료된 건입니다.'}

    query = 'update reserve set completed = 1, completed_time = now() where order_key = "%s"' % key
    dbconn()
    cur.execute(query)
    conn.commit()
    dbconn(1)

    query = 'select * from reserve where order_key = "%s"' % key
    dbconn()
    cur.execute(query)
    result = cur.fetchone()
    dbconn(1)
    producer = int(result['producer'])
    consumer = int(result['consumer'])
    product = result['product']
    price = int(result['price'])
    quantity = int(result['quantity'])
    accepted_time = result['accepted_time']
    order_time = result['order_time']
    completed_time = result['completed_time']

    text = ' 님이 귀하꼐서 요청하신 예약을 완료하셨습니다!\n\n세부정보\n품목: %s\n개당가격: %d\n수량: %d\n예약요청일: %s\n예약수락일: %s\n예약완료일: %s' % (product, price, quantity, order_time, accepted_time, completed_time)


    return {'type':'success', 'value' : {'text' : text, 'send_to': consumer, 'mention' : producer}}


def show(message):
    msg = message.content
    my_id = message.author.id

    query1 = 'select * from reserve where consumer = %d and accepted = %d and completed = %d'
    query2 = 'select * from reserve where producer = %d and accepted = %d and completed = %d'
    accepted = 0
    completed = 0

    if '미확인' in msg:
        accepted = 0
    elif '거절' in msg:
        accepted = 2
    elif '완료' in msg:
        accepted = 1
        completed = 1
    else:
        accepted = 1

    dbconn()
    query = query1 % (my_id, accepted, completed)
    cur.execute(query)
    result1 = cur.fetchall()

    query = query2 % (my_id, accepted, completed)
    cur.execute(query)
    result2 = cur.fetchall()
    dbconn(1)

    temp = '생산자, 품목, 개당가격, 수량, 고유코드 순으로 표시됩니다.\n'
    temp_arr = []
    num = 0
    num1 = 0
    # print(len(result1), len(result2))
    if len(result1) < 1 and len(result2) < 1:
        return {'type':'simple', 'text' : '확인하지 않으신 계약이 없습니다.'}

    if len(result1)>0:
        temp += '\n자신이 요청한 예약입니다.\n'
        for i in result1:
            temp += str(num1+1)+'. |'+str(num)+'|, ' + i['product'] + ', ' + str(i['price']) + ', ' + str(i['quantity']) + ', ||' + i['order_key'] + '||\n'
            temp_arr.append(i['producer'])
            num += 1; num1 +=1

    if len(result2) > 0:
        temp += '\n자신이 맡은 예약입니다.\n'
        for i in result2:
            temp += str(num1+1)+'. |'+str(num)+'|, ' + i['product'] + ', ' + str(i['price']) + ', ' + str(i['quantity']) + ', ||' + i['order_key'] + '||\n'
            temp_arr.append(i['consumer'])
            num += 1; num1 +=1

    return {'type': 'show', 'value' : {'text':temp, 'list':temp_arr}}