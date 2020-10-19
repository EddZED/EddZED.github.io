document.addEventListener("DOMContentLoaded", function() {  
var lazyloadImages;if ("IntersectionObserver" in window) {    
lazyloadImages = document.querySelectorAll(".v_lazy");    
var imageObserver = new IntersectionObserver(function(entries, observer) {entries.forEach(function(entry) 
{  if (entry.isIntersecting) {    
var image = entry.target;    image.src = image.dataset.src;    image.classList.remove("v_lazy");  
if (image.parentElement.querySelector(".loader")) { image.parentElement.querySelector(".loader").remove();} 
if (image.parentElement.parentElement.querySelector(".loader")) { 
image.parentElement.parentElement.querySelector(".loader").remove();}   
imageObserver.unobserve(image);  }});    });    
lazyloadImages.forEach(function(image) {imageObserver.observe(image);    });  } 
else {var lazyloadThrottleTimeout;    lazyloadImages = document.querySelectorAll(".v_lazy");  
function lazyload () {if(lazyloadThrottleTimeout) {  clearTimeout(lazyloadThrottleTimeout);}    
lazyloadThrottleTimeout = setTimeout(function() {  
var scrollTop = window.pageYOffset;  lazyloadImages.forEach(function(img) {
	if(img.offsetTop < (window.innerHeight + scrollTop)) {  img.src = img.dataset.src; 
	img.classList.remove('v_lazy');}  });  if(lazyloadImages.length == 0) 
	{     document.removeEventListener("scroll", lazyload);    
window.removeEventListener("resize", lazyload);   
 window.removeEventListener("orientationChange", lazyload);  }}, 20);    }    
 document.addEventListener("scroll", lazyload);    
 window.addEventListener("resize", lazyload);    
 window.addEventListener("orientationChange", lazyload);  }})