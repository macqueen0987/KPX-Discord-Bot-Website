import discord
import time
from random import randint
from psutil import cpu_percent, virtual_memory
from discord.ext import tasks
import importlib
import os
import sys
import re
import pymysql
from pprint import pprint

import var
import search_ore
import voice
import update_ore
import buyback
import website
import wop
import market

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

def main():
    def reload():
        importlib.reload(search_ore)
        importlib.reload(voice)
        importlib.reload(buyback)
        importlib.reload(update_ore)
        importlib.reload(website)
        importlib.reload(wop)
        importlib.reload(market)
    start = 0
    global last_linked
    last_linked = [0,0]
    client = discord.Client()
    state = ['명령어는 "!명령어"', '즐이브하세요~~', '기계에게는 자유가 없습니다', '콥 관리봇', '기능 건의 받습니다', '저희 콥에 어서오세요~']
    reload()

    def convert(time):

        day = time // (24 * 3600)
        time = time % (24 * 3600)
        hour = time // 3600
        time %= 3600
        minutes = time // 60
        time %= 60
        seconds = time
        temp = ''
        if day != 0: temp += "%d일 " % day
        if hour != 0: temp += "%d시간 " % hour
        if minutes != 0: temp += "%d분 " % minutes
        if seconds != 0: temp += "%d초" % seconds
        return temp




    @client.event
    async def on_message(message):
        global last_linked
        # we do not want the bot to reply to itself
        if message.author == client.user or message.author.bot is True:
            return

        if message.guild is None: #봇한테 DM 금지
            return

        linked_channels = None
        linked_num = 0
        if message.channel.id in var.linked_channels1: #채팅방 연동
            linked_channels = var.linked_channels1
            linked_num = 0


        if message.channel.id in var.linked_channels2:
            linked_channels = var.linked_channels2
            linked_num = 1

        if linked_channels is not None:
            nick = None
            # print(last_linked, message.author.id)
            # print(linked_num)
            try:
                nick = message.author.nick
            except Exception as e:
                pass
            if nick is None:
                nick = message.author.name
            if last_linked[linked_num] != message.author.id:
                last_linked[linked_num] = message.author.id
                linked = False
            else:
                linked = True
            for i in linked_channels:
                # print(i)
                if i != message.channel.id:
                    channel = client.get_channel(i)
                    # embed = discord.Embed(title=nick, description=message.content, color=var.linked_color[message.channel.id])
                    # await channel.send(embed = embed)
                    if len(message.embeds) < 1:
                        try:
                            if not linked:
                                msg = var.linked_color[message.channel.id] + re.sub('\[.+?\]', '', re.sub('\(.+?\)', '', str(nick))).replace('[ ','[')+']```' + message.content
                                # msg = "***"+nick + "***\n" + message.content
                                # await channel.send("***"+nick + "***\n" + message.content)
                            else:
                                msg = message.content
                            if len(msg) > 0:
                                await channel.send(msg)
                        except Exception as e:
                            user = client.get_user(var.me)
                            await user.send("error!\n" + str(e))

                    else:
                        for j in message.embeds:
                            if not linked:
                                await channel.send(embed=j.copy(), content = var.linked_color[message.channel.id] + re.sub('\[.+?\]', '', re.sub('\(.+?\)', '', str(nick)))+']```' + message.content)
                            else:
                                await channel.send(embed=j.copy(), content = message.content)

                    for j in range(len(message.attachments)):
                        await channel.send(message.attachments[j].url)


        if message.guild.id not in [698108955192197160, 534057556562280461, 726189358536851488]: #KPX, 내 테섭 2개
            return


        msg = message.content.split(' ')[0]
        color = randint(0, 0xFFFFFF)

        if message.channel.id == 744421664699449364: #KPX 잡담방 명령어 사용 금지
            return

        if int(message.channel.id) in var.delete_all_channel:
            await message.delete()

        if msg in ['!전송', '!공지']:
            org_message = message
            if message.author.id == 366269351080361995:
                msg1 = message.content.split(' ')
                if msg == '!전송':
                    channel = client.get_channel(int(msg1[1]))
                    if channel is not None:
                        msg = message.content
                        for i in range(2):
                            msg = msg.replace(msg1[i]+' ','')
                        await channel.send(msg)

                # msg_temp = msg
                if msg == '!공지':
                    # print(message.content)
                    await message.delete()
                    message = await message.channel.fetch_message(int(msg1[1]))
                    for i in range(2,len(msg1)):
                        await message.add_reaction(msg1[i])
                    message = org_message

        if msg == '!차단':
            if message.author.id == 366269351080361995:
                channel_id = int(message.channel.id)
                if channel_id in var.delete_all_channel:
                    var.delete_all_channel.remove(channel_id)
                else:
                    var.delete_all_channel.append(int(message.channel.id))
            else:
                msg = [" 권한이 없습니다!! 나약한것...", "장비를 정지합니다. 정지하겠습니다. 아.. 안되잖아... 정지가 안되...", "영웅은 죽지 않아요","정지합니다...\n||라고 할줄 알았냐???||"]
                msg = "{0.author.mention}"+msg[randint(0,len(msg)-1)]
                await message.channel.send(msg.format(message))
                
        if msg == '!상태':
            message = await message.channel.send('확인중입니다. 5초정도 소요됩니다.'.format(message))
            msg = '업타임: ' + convert(time.time() - start)+'\n'
            msg += 'CPU: ' + str(cpu_percent(interval = 5)) + '%\n'
            msg += 'REM: ' + str(virtual_memory().percent) + '%'
            await message.edit(content = msg.format(message))


        if msg == '!wop' or msg == '!WOP':
            msg = wop.search_wop(message)
            if msg['type'] == 'error':
                await message.channel.send(msg['msg'])

            if msg['type'] == 'msg':
                await message.delete()
                message = await message.channel.send(msg['msg'])
                wop.add(message.id, msg['idx'])
                await message.add_reaction('🆖')

        if msg in search_ore.commands:
            if len(message.content.split(' ')) < 2:
                embed=discord.Embed(title='필수 요소 누락', description='!광물시세 [광물이름] 의 형식을 지켜주세요!', color=color)
                embed.set_thumbnail(url='https://weakwifisolutions.com/wp-content/uploads/2019/08/error-red-cross-7.png')

            else:
                send_message = await message.channel.send('잠시만 기다려 주세요')
                msg = search_ore.main(message)
                if msg['type'] == 'ERROR':
                    embed=discord.Embed(title=msg['value']['title'], description=msg['value']['text'], color=color)
                    embed.set_thumbnail(url='https://weakwifisolutions.com/wp-content/uploads/2019/08/error-red-cross-7.png')
                    embed.set_footer(text="언제나 기능 건의는 환영입니다!!")
                    try:
                        # await send_message.delete()
                        await send_message.edit(content = None, embed=embed)
                    except Exception as e:
                        await message.channel.send('ERROR')

                elif msg['type'] == 'price':
                    msg = msg['value']
                    embed=discord.Embed(title=msg['title'], description='얼라 바이백시트를 기준으로 합니다.', color=color)
                    if 'image' in msg:
                        embed.set_thumbnail(url=msg['image'])
                    embed.add_field(name="기준", value='얼라 가격', inline=True)
                    embed.add_field(name="가격", value=msg['values'], inline=True)
                    embed.set_footer(text="언제나 기능 건의는 환영입니다!!")
                    try:
                        # await send_message.delete()
                        await send_message.edit(content = None, embed=embed)
                    except Exception as e:
                        await message.channel.send('ERROR')


                elif msg['type'] == 'calc':
                    await send_message.edit(content = None, embed = msg['embed'])

                elif msg['type'] == 'buyback':
                    dbconn()
                    query = 'select ingame_id from buyback order by idx desc limit 1'
                    cur.execute(query)
                    result = cur.fetchone()
                    dbconn(0)
                    # print(str(result['ingame_id']))
                    if str(result['ingame_id']) != 'None':
                        nick = result['ingame_id']
                    else:
                        nick = message.author.nick
                        # user_id = message.author.id
                        if nick is None:
                            nick = message.author.name
                    new_message = '인게임 이름: ' + re.sub('\(.+?\)', '', str(nick)).replace('[KPX] ','').replace('[KPX]','') +'\n'  # noqa: W605
                    new_message += msg['message']
                    try:
                        await send_message.edit(content = new_message)
                        # await send_message.add_reaction('✅')
                        await send_message.add_reaction('🆖')
                        message_id = send_message.id
                        await message.delete()
                        buyback.add(message_id)
                    except Exception as e:
                        pass
                    # await message.channel.send(new_message)

        
        if msg in website.commands:
            msg = website.main(message)
            await message.delete()
            msg = '{0.author.mention} ' + msg
            await message.channel.send(msg.format(message))


        if msg in update_ore.commands:
            await message.channel.send('잠시만 기다리세요')
            msg = update_ore.main(message)
            await message.channel.send(msg)


        if msg in market.commands:
            msg = market.main(message)
            msg = '{0.author.mention}\n' + msg
            await message.channel.send(msg.format(message))

        if msg == '!장부입력':
            await message.delete()
            budget = buyback.add_budget(message)
            message = await client.get_channel(750911293388750909).fetch_message(772796324956602368)
            msg = '남은 바이백 예산: %s ISK' % format(budget, ',')
            await message.edit(content=msg)

        if message.content.startswith('!테스트'):
            pass

        if message.content.startswith('!안녕'):
            msg = '하이 {0.author.mention}'.format(message)
            await message.channel.send(msg)

        if message.content.startswith('!멘션'):
            msg = '<@!%d>' % int(message.content.split(' ')[1])
            await message.channel.send(msg)

        if message.content.startswith('!가입메시지'):
            await message.channel.send(var.welcome_message)

        if message.content.startswith('!콥검색방법'):
            await message.channel.send(var.search_kpx)

        if message.content.startswith('!생산스프레드시트'):
            await message.channel.send('https://docs.google.com/spreadsheets/d/1gzigqUdQlZ5NdfYych4itlK6-bU0RqsoBWR6LUB-DL8/edit?usp=sharing')

        if message.content.startswith('!초대링크'):
            await message.channel.send('<https://discord.com/api/oauth2/authorize?client_id=748083948948815942&permissions=285437008&scope=bot>')

        if message.content.startswith('!바이백업데이트'):
            buyback.update_price(bypass = True)

        if message.content.startswith('!바이백주인'):
            if message.author.id not in var.ID_director:
                return
            await message.delete()
            msg = message.content.split(' ')
            message_id = int(msg[1])
            owner = int(msg[2])
            buyback.update(message_id, owner)
            server = client.get_guild(726189358536851488)
            user = server.get_member(int(msg[2]))
            nick = ''
            if user is not None:
                if user.bot is not True:
                    nick = str(user.nick)
                    if nick == 'None':
                        nick = str(user.name)
                message = await message.channel.fetch_message(message_id)
                msg = message.content
                msg = msg.replace(msg.split('\n')[0]+'\n', '')
                msg = '인게임 이름: ' + re.sub('\(.+?\)', '', str(nick)).replace('[KPX] ','').replace('[KPX]','') +'\n' + msg # noqa: W605
                await message.edit(content = msg)


        if message.content.startswith('!핑'):
            time_then = time.monotonic()
            pinger = await message.channel.send('__*`Pinging...`*__')
            ping = '%.2f' % (1000*(time.monotonic()-time_then))
            await pinger.edit(content = ':ping_pong: \n **Pong!** __**`' + ping + 'ms`**__')

        if message.content == '!음악명령어' or message.content == '-명령어':
            color = randint(0, 0xFFFFFF)
            embed=discord.Embed(title="음악명령어", description="클랜 DJ 봇에서사용하실수 있는 모든 명령어의 목록입니다!\n\n[이름] ← 이것은 대괄호 말고 해당 항목을 입력하라는 것입니다!", color=color)
            embed.add_field(name="명령어", value="-소환\n-나가\n\n-재생 [유튜브 검색어 또는 URL]\n-검색 [유튜브 검색어 또는 URL]\n\n-일시정지, -멈춤\n-계속\n\n-다음\n-삭제 [곡번호]\n\n\n-목록\n-지금\n\n-명령어", inline=True)
            embed.add_field(name="설명", value="음악봇을 들고옵니다. 가장 먼저 해줘야 합니다.\n음성봇 열결을 해제합니다\n\n음악을 검색해서 첫번째 결과를 재생합니다\n검색하여 보여준뒤 선택된것을 재생합니다.\n\n일시정지 시킵니다\n일시정지를 해제합니다\n\n현재 재생중인 노래를 건너뜁니다.\n재생대기열에 있는 노래를 삭제합니다. \n꼭 '-목록' 명령어로 곡 번호를 확인하세요!\n\n대기열에 있는 음악목록을 보여줍니다.\n현재 노래의 정보를 보여줍니다\n\n명령어를 보여줍니다. 지금 이 창도 표시합니다.", inline=True)
            embed.set_footer(text="언제나 기능 건의는 환영입니다!!")
            await message.channel.send(embed=embed)

        if message.content in ['!정지', '!재시작', '!리로드']:
            if message.author.id == 366269351080361995:
                if message.content == '!정지':
                    msg = "{0.author.mention} 정지합니다..."
                    restart = False

                if message.content == '!재시작':
                    msg = "{0.author.mention} 재시작합니다..."
                    restart = True

                var.restart = restart
                await message.channel.send(msg.format(message))
                client.clear()
                await client.close()

            else:
                msg = [" 권한이 없습니다!! 나약한것...", "장비를 정지합니다. 정지하겠습니다. 아.. 안되잖아... 정지가 안되...", "영웅은 죽지 않아요","정지합니다...\n||라고 할줄 알았냐???||"]
                msg = "{0.author.mention}"+msg[randint(0,len(msg)-1)]
                await message.channel.send(msg.format(message))


        if message.content == '!업타임':
            msg = '업타임: ' + convert(time.time() - start)
            await message.channel.send(msg.format(message))

        if message.content == '!명령어':
            color = randint(0, 0xFFFFFF)
            embed=discord.Embed(title="명령어", description="사용하실수 있는 모든 명령어의 목록입니다!\n\n[이름] ← 이것은 대괄호 말고 해당 항목을 입력하라는 것입니다!\n예시: [광물] = veldspar, jaspet, 등등\n\n", color=color)
            embed.add_field(name="설명", value=var.command1, inline=True)
            embed.add_field(name="명령어", value=var.command2, inline=True)
            embed.set_footer(text="언제나 기능 건의는 환영입니다!!")
            await message.channel.send(embed=embed)


    @client.event
    async def on_voice_state_update(member, before, after):
        nick = member.nick
        for i in member.guild.roles:
            if i.name == 'KPX Member':
                kpx_member = i.id
        if nick is None:
            nick = member.name
        if after.channel is not None:
            if after.channel.name == '참가하여 채널생성':
                has_role = False
                for i in member.roles:
                    if i.name == 'KPX Member':
                        has_role = True
                if has_role:
                    category = after.channel.category
                    name = nick + '님의 음성채널'
                    new_channel = await category.create_voice_channel(name = name)
                    await new_channel.set_permissions(member.guild.get_role(kpx_member), connect = True, speak = True, view_channel = True)
                    await new_channel.set_permissions(member.guild.default_role, connect = False, speak = False, view_channel = False)
                    await member.move_to(new_channel)
                    voice.add(new_channel)

                else:
                    await member.move_to(None)
                    await member.send(f'{member.mention}\n'+'해당 채널에 접속할 권한이 없습니다.')

        if before.channel is not None:
            if before.channel.id in voice.list():
                if len(before.channel.members) < 1:
                    await before.channel.delete()
                    voice.delete(before.channel)


    @client.event
    async def on_raw_reaction_add(payload):
        # print(payload)
        channel_id = payload.channel_id
        message_id = payload.message_id
        guild_id = payload.guild_id
        user_id = payload.user_id
        emoji = str(payload.emoji.name)
        nick = payload.member.nick
        if nick is None:
            nick = payload.member.name
        nick = nick.replace("[KPX] ","").replace("[KPX]","")
        # print(nick)
        if payload.member.bot:
            return
        if channel_id == 750911293388750909 or channel_id == 765934883062939688:
            if emoji == '✅' and user_id in var.ID_director:
                message = await client.get_channel(channel_id).fetch_message(message_id)

                if channel_id == 750911293388750909:
                    await message.delete()
                    await client.get_channel(750913396354056283).send("바이백 수락 : "+nick+"\n```"+message.content.replace('                       ','            ')+"```")
                    if user_id == var.me:
                        budget = buyback.approved(message.content, message.id, True)
                    else:
                        budget = buyback.approved(message.content, message.id, False)
                
                if channel_id == 765934883062939688:
                    await message.remove_reaction(emoji = '🆖', member = client.get_user(748083948948815942))
                    if user_id == var.me:
                        budget = wop.approved(message.content, message.id, True)
                    else:
                        budget = wop.approved(message.content, message.id, False)
                    msg = message.content.split('\n')
                    if budget[1] is not None:
                        temp = '> %s\n> %s\n<@!%d>컨트랙 완료' % (msg[0],msg[1],budget[1])
                    else:
                        temp = '> %s\n> %s\n컨트랙 완료' % (msg[0],msg[2])

                    # await message.channel.send(temp.format(message))
                    await client.get_channel(750913396354056283).send(temp.format(message))

                # if budget is not None:
                #     if budget[0] is not None:
                #         message = await client.get_channel(750911293388750909).fetch_message(772796324956602368)
                #         msg = '남은 바이백 예산: %s' % format(budget[0], ',')
                #         await message.edit(content=msg)

        if channel_id == 750911293388750909 or channel_id == 765934883062939688:
            if emoji == '🆖':
                if buyback.remove(message_id, user_id):
                    # print(1)
                    message = await client.get_channel(channel_id).fetch_message(message_id)
                    await message.delete()

                elif wop.remove(message_id, user_id):
                    # print(2)
                    message = await client.get_channel(channel_id).fetch_message(message_id)
                    await message.delete()
        
        elif channel_id == 761612551955283969:
            if emoji == '🌏':
                if 'Diplomat' not in str(payload.member.roles):
                    role = client.get_guild(guild_id).get_role(761613214579556402)
                    await payload.member.add_roles(role)

            elif emoji == '🙂':
                if 'NEWBIE' not in str(payload.member.roles) and 'CONDOR' not in str(payload.member.roles):
                    role = client.get_guild(guild_id).get_role(761613366137192489)
                    await payload.member.add_roles(role)
                    channel = client.get_channel(744421664699449364)
                    dbconn()
                    query = 'select count(*) as cnt from new_members where user_id = %d' % user_id
                    cur.execute(query)
                    result = cur.fetchone()
                    if int(result['cnt']) < 1:
                        msg = '<@!%d>님 환영합니다. 여기서 저희 멤버와 소통하며 마저 남은 가입 절차를 밟아주세요.' % user_id
                        await channel.send(msg)
                        query = 'insert into new_members(user_id) values(%d)' % user_id
                        cur.execute(query)
                        conn.commit()
                    dbconn(0)


        # print(payload)
        # <RawReactionActionEvent message_id=748469730242986064 user_id=366269351080361995 channel_id=748450834123325501 guild_id=748365810178850867 emoji=<PartialEmoji animated=False name='👍' id=None> event_type='REACTION_ADD' member=<Member id=366269351080361995 name='macqueen0987' discriminator='9067' bot=False nick=None guild=<Guild id=748365810178850867 name='Sigong JOA' shard_id=None chunked=True member_count=18>>>
    

    @client.event
    async def on_raw_reaction_remove(payload):
        # pprint(payload)
        channel_id = payload.channel_id
        message_id = payload.message_id
        user_id = payload.user_id
        guild_id = payload.guild_id
        emoji = str(payload.emoji.name)
        if payload.member is None:
            return
        if payload.member.bot:
            return
        if channel_id == 761612551955283969:
            member = client.get_guild(payload.guild_id).get_member(user_id)
            try:
                if emoji == '🌏':
                    role = client.get_guild(guild_id).get_role(761613214579556402)
                    await member.remove_roles(role)

                if emoji == '🙂':
                    role = client.get_guild(guild_id).get_role(761613366137192489)
                    await member.remove_roles(role)
                    dbconn()
                    query = 'delete from new_members where user_id = %d' % user_id
                    cur.execute(query)
                    conn.commit()
                    dbconn(0)
            except Exception as e:  # noqa: F841
                pass
    

    @client.event
    async def on_member_join(member):
        guild_id = member.guild.id
        # if 'NEWBIE' not in str(member.roles) and 'CONDOR' not in str(member.roles):

        dbconn()
        query = 'select exists(select user_id from new_members where user_id = %d) as exist' % member.id
        cur.execute(query)
        result = cur.fetchone()
        dbconn(0)
        
        if result['exist']:
            if 'NEWBIE' not in str(member.roles) and 'CONDOR' not in str(member.roles):
                role = client.get_guild(guild_id).get_role(761613366137192489)
                await member.add_roles(role)
        else:
            role = client.get_guild(guild_id).get_role(761613366137192489)
            await payload.member.add_roles(role)
            channel = client.get_channel(744421664699449364)
            dbconn()
            query = 'select count(*) as cnt from new_members where user_id = %d' % user_id
            cur.execute(query)
            result = cur.fetchone()
            if int(result['cnt']) < 1:
                msg = '<@!%d>님 환영합니다. 여기서 저희 멤버와 소통하며 마저 남은 가입 절차를 밟아주세요.' % user_id
                await channel.send(msg)
                query = 'insert into new_members(user_id) values(%d)' % user_id
                cur.execute(query)
                conn.commit()
            dbconn(0)

        # await member.send(f'{member.mention}\n'+var.welcome_message)


    @client.event
    async def on_guild_join(guild):
        user = client.get_user(366269351080361995)
        await user.send('%s 에 새롭게 초대됨' % guild.name)


    @tasks.loop(minutes=10.0)
    async def task():
        try:
            pass
            game = discord.Game(state[randint(0,len(state)-1)])
            await client.change_presence(status=discord.Status.online, activity=game)
        except Exception as e:
            pass

        server_ids = var.allowed_server
        my_server = []
        for i in server_ids:
            my_server.append(client.get_guild(i))
        for server in client.guilds:
            if server not in my_server:
                await server.leave()


    @tasks.loop(minutes=3)
    async def get_web():
        # print('doing1....')
        buybacks = website.check_web_buyback()
        # print(buybacks)
        for i in buybacks:
            query = 'select ingame_id from buyback where idx = %d' % i[2]
            dbconn()
            cur.execute(query)
            result = cur.fetchone()
            dbconn(1)
            if str(result['ingame_id']) != 'None':
                nick = result['ingame_id']
            else:
                user = client.get_guild(726189358536851488).get_member(int(i[0]))
                nick = user.nick
                if nick is None:
                    nick = user.name
            msg = '인게임 이름: ' + re.sub('\(.+?\)', '', str(nick)).replace('[KPX] ','').replace('[KPX]','').replace('_','\\_') +'\n' + i[1]
            channel = client.get_channel(750911293388750909)
            message = await channel.send(msg)
            await message.add_reaction('🆖')
            website.web_add(message.id, i[2],'buyback')
        # print('doing2')
        wops = website.check_web_wop()
        for i in wops:
            if 'ingame_id' in str(i.keys()):
                nick = i['ingame_id']
            else:
                user = client.get_guild(726189358536851488).get_member(int(i['discord_id']))
                nick = user.nick
                if nick is None:
                    nick = user.name

            msg = '인게임 이름: ' + re.sub('\(.+?\)', '', str(nick)).replace('[KPX] ','').replace('[KPX]','') +'\n' + i['msg']
            channel = client.get_channel(765934883062939688)
            if 'img' in str(i.keys()):
                message = await channel.send(msg, file = discord.File(i['img']))
                os.remove(i['img'])
            else:
                message = await channel.send(msg)
            await message.add_reaction('🆖')
            website.web_add(message.id, i['idx'], 'WOP')

        # buyback.update_price()
        market.cache()


    @client.event
    async def on_ready():
        global start
        server_ids = var.allowed_server
        my_server = []
        for i in server_ids:
            my_server.append(client.get_guild(i))
        for server in client.guilds:
            if server not in my_server:
                await server.leave()
        reload()
        game = discord.Game('starting...')
        await client.change_presence(status=discord.Status.online, activity=game)
        task.start()
        get_web.start()

    start = time.time()
    client.run(var.token)

if __name__ == '__main__':
    main()