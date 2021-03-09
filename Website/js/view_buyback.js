function calc(material_name) {
    var num = document.getElementById(material_name+'_count').value;
    var price = document.getElementById(material_name+'_price').innerHTML;
    var material_price = num*price;
    document.getElementById(material_name+'_total_price').innerHTML = material_price;

    var material_prices = document.getElementsByClassName("material_price");
    var sum = 0;
    for (var i = material_prices.length - 1; i >= 0; i--) {
        sum = sum + parseInt(material_prices[i].innerHTML);
    }

    document.getElementById('total_price').innerHTML=sum.toLocaleString('en');
}