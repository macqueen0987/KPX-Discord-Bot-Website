var temp;

function demand(material){
    document.getElementById(material).className = document.getElementById(material+'_demand').value.replace(' ','_');
}

function change_buyback(){
    var ajaxurl = './process/change_stats.php',
    data =  {'type': 'buyback'};
    $.post(ajaxurl, data, function (response) {
        if (response == 1) {
            alert('성공적으로 활성화 되었습니다.');
            document.getElementById('buyback_stats').innerHTML = '바이백 비활성화';
        }
        else{
            alert('성공적으로 비활성화 되었습니다.');
            document.getElementById('buyback_stats').innerHTML = '바이백 활성화';
        }
    });
}

function change_wop(){
    var ajaxurl = './process/change_stats.php',
    data =  {'type': 'wop'};
    $.post(ajaxurl, data, function (response) {
        if (response == 1) {
            alert('성공적으로 활성화 되었습니다.');
            document.getElementById('wop_stats').innerHTML = 'WOP 비활성화';
        }
        else{
            alert('성공적으로 비활성화 되었습니다.');
            document.getElementById('wop_stats').innerHTML = 'WOP 활성화';
        }
    });
}

