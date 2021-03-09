var slideIndex = 0;
var in_out = 0;
function showSlides() {
  in_out = 1 - in_out;
  var i;
  var slides = document.getElementsByClassName("banner");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  slideIndex++;
  if (slideIndex > slides.length) {slideIndex = 1}    

  slides[slideIndex-1].style.display = "block";
  if (in_out == 1){
  setTimeout(showSlides, 20000);
  }
  else{setTimeout(showSlides, 3000);}
}