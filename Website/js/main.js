document.addEventListener('scroll', function(){

    var h = document.documentElement, 
        b = document.body,
        st = 'scrollTop',
        sh = 'scrollHeight';

    var percent = (h[st]||b[st]) / ((h[sh]||b[sh]) - h.clientHeight) * 100;
    
    var s = document.getElementById('scroll');
    s.setAttribute('x2', percent+'%');
    
});


function show_load(){
    document.getElementById('body').style.display = 'none';
    document.getElementById('spinner').style.display = 'block';
}