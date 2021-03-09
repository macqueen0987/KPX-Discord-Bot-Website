import pymysql


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


def add(channel):
    dbconn()
    query = 'insert into created_voice_channels(channels) values(%d)' % channel.id
    cur.execute(query)
    conn.commit()
    dbconn(1)


def list():
    query = 'select * from created_voice_channels'
    dbconn()
    cur.execute(query)
    result = cur.fetchall()
    dbconn(1)
    temp_arr = []
    for i in result:
        temp_arr.append(i['channels'])

    return temp_arr


def delete(channel):
    query = 'delete from created_voice_channels where channels = %d' % channel.id
    dbconn()
    cur.execute(query)
    conn.commit()
    dbconn(1)