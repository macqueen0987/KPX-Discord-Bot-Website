function select(name){
    document.getElementById('wop_product').value = name.replace(/_/gi," ");
    window.scrollTo(0,document.body.scrollHeight);
}



function search(){
    var subject = document.getElementById("search").value;
    var pool = [document.querySelectorAll(".product_name1") , document.querySelectorAll(".product_name2") , document.querySelectorAll(".product_name3") , document.querySelectorAll(".product_name4")];
        if (subject.length > 0) {

        pool.forEach( function(element) {
            for (var i = 0; i < element.length; i++) {
                if (element[i].innerHTML.toLowerCase().includes(subject.toLowerCase())) {
                    element[i].className += " hit";
                    document.getElementById(element[i].id+'_price').className+= ' hit';
                }
                else{
                    element[i].classList.remove("hit");
                    document.getElementById(element[i].id+'_price').classList.remove('hit');
                }
            }
        });
    }else{
        pool.forEach( function(element) {
            for (var i = 0; i < element.length; i++) {
                element[i].classList.remove("hit");
                document.getElementById(element[i].id+'_price').classList.remove('hit');
            }
        });
    }
}

