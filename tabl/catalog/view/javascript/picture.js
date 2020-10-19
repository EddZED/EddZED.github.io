
var createPicture;
$(window).on("load", function() {
function hashUpdate(){
var hash = window.location.hash;
if(hash.length){
$('#tabl_options a[href="' + hash + '"]').trigger('click');
}
}
$(window).on('hashchange', hashUpdate);
hashUpdate();
selTabl(999);
});
