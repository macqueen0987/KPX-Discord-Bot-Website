function calc(material_name) {
    var num = document.getElementById(material_name+'_count').value;
    var price = document.getElementById(material_name+'_price').innerHTML;
    var material_price = num*price;
    document.getElementById(material_name+'_total_price').innerHTML = material_price;
    document.getElementById(material_name+'_submit_quantity').value = num;

    var temp_sum = 0;
    var sum = 0;

    var material_prices = document.getElementsByClassName("ore_material_price");
    for (var i = material_prices.length - 1; i >= 0; i--) {
        temp_sum = temp_sum + parseInt(material_prices[i].innerHTML);
    }
    document.getElementById('ore_submit_total').value = temp_sum;
    sum = sum + temp_sum;

    temp_sum = 0;
    material_prices = document.getElementsByClassName("mineral_material_price");
    for (var i = material_prices.length - 1; i >= 0; i--) {
        temp_sum = temp_sum + parseInt(material_prices[i].innerHTML);
    }
    document.getElementById('mineral_submit_total').value = temp_sum;
    sum = sum + temp_sum;

    temp_sum = 0;
    material_prices = document.getElementsByClassName("pi_material_price");
    for (var i = material_prices.length - 1; i >= 0; i--) {
        temp_sum = temp_sum + parseInt(material_prices[i].innerHTML);
    }
    document.getElementById('pi_submit_total').value = temp_sum;
    sum = sum + temp_sum;
    // alert(sum);
    document.getElementById('total_price').innerHTML=sum.toLocaleString('en');
    document.getElementById('submit_total_price').value = sum;
}