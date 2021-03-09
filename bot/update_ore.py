import gspread
from oauth2client.service_account import ServiceAccountCredentials



commands = ['!시트업데이트']
def read_sheet():
    try:
        scope = ['https://spreadsheets.google.com/feeds', 'https://www.googleapis.com/auth/drive',]
        json_file_name = 'vast-ethos-251302-b8a92651b359.json'
        credentials = ServiceAccountCredentials.from_json_keyfile_name('./client_secrets.json', scope)
        gc = gspread.authorize(credentials)
        spreadsheet_url = 'https://docs.google.com/spreadsheets/d/1mU5Iho48rYVXrtGKdREN5-fqmQoOAZ9-1hH8Q2e4KUk/edit#gid=0'
        # 스프레스시트 문서 가져오기
        doc = gc.open_by_url(spreadsheet_url)
        # 시트 선택하기
        worksheet = doc.worksheet('PriceAndDemand')
        ore = worksheet.range('A5:A20')
        mineral = worksheet.range('G5:G12')
        pi1 = worksheet.range('A25:A41')
        pi2 = worksheet.range('G25:G41')
        search_arr = [ore,mineral,pi1,pi2]
        return [search_arr, worksheet]
    except Exception as e:
        return 'ERROR'


def update(message):
    read_sheet_value = read_sheet()
    if read_sheet_value == 'ERROR':
        return '연결에서 에러가 발생했습니다.'

    try:
        search_arr = read_sheet_value[0]
        worksheet = read_sheet_value[1]
        ore_arr = []
        price_arr = []
        message = message.replace(', ',',').replace(' ,',',').split(',')
        # print(message)
        for i in message:
            price = i.split(' ')[-1]
            i = i.replace(price,'').replace(' ','').lower()
            ore_arr.append(i)
            price_arr.append(price)

        first = [['B',5], ['H',5], ['B',25], ['H',25]]
        cell_arr = []
        found_cnt = 0
        for i in range(4):
            for j in range(len(ore_arr)):
                for h in range(len(search_arr[i])):
                    if ore_arr[j] in search_arr[i][h].value.lower().replace(' ',''):
                        found_cnt += 1
                        cell = first[i][0]+str(first[i][1]+h)
                        # print(search_arr[i][h].value, cell)
                        cell_arr.append([cell, price_arr[j]])

        if found_cnt < len(ore_arr):
            return '없는 이름이 존재합니다'

        for i in range(len(cell_arr)):
            worksheet.update_acell(cell_arr[i][0], cell_arr[i][1])

        return '성공적으로 업데이트 했습니다.'
    except Exception as e:
        return '업데이트 과정에서 에러가 발생했습니다'


def main(message):
    msg = message.content
    msg = msg.replace(msg.split(' ')[0]+ ' ', '')
    msg = update(msg)
    return msg