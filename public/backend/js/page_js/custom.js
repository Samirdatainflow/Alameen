$(".close-sidebar-btn").click(function(){
	var e=$(this).attr("data-class");
	$(".app-container").toggleClass(e);
	var i=$(this);i.hasClass("is-active")?i.removeClass("is-active"):i.addClass("is-active");
});
$(".vertical-nav-menu").metisMenu({
   toggle: false
 });
function showLoader(){
	$.blockUI({message:$(".loader-content"),css: {
		            border: 'none',
		            padding: '15px',
		            backgroundColor: '#FF606D',
		            '-webkit-border-radius': '15px',
		            '-moz-border-radius': '15px',
		            opacity: 1,
		            color: '#fff',
		            width: '150px',
		            left: '45%'
		        }});
}
function hideLoader(){
	$.unblockUI();
}


// Full screen
$("#full_screen").on('click',function(){
	if($(this).attr('data-full-screen')=="0")
	{
		$(this).attr('data-full-screen',"1");
		$(this).html('<i class="fa fa-compress" aria-hidden="true"></i> Minimize screen');
		openFullscreen();
	}
	else
	{
		closeFullscreen();
	}
	
})
/* Get the documentElement (<html>) to display the page in fullscreen */
var elem = document.documentElement;

/* View in fullscreen */
function openFullscreen() {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) { /* Safari */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE11 */
    elem.msRequestFullscreen();
  }
}

/* Close fullscreen */
function closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.webkitExitFullscreen) { /* Safari */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE11 */
    document.msExitFullscreen();
  }
  $("#full_screen").html('<i class="fa fa-expand" aria-hidden="true"></i> Full screen');
  $("#full_screen").attr('data-full-screen',"0");
}

document.addEventListener('fullscreenchange', exitHandler);
document.addEventListener('webkitfullscreenchange', exitHandler);
document.addEventListener('mozfullscreenchange', exitHandler);
document.addEventListener('MSFullscreenChange', exitHandler);

function exitHandler() {
    if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        $("#full_screen").html('<i class="fa fa-expand" aria-hidden="true"></i> Full screen');
  		$("#full_screen").attr('data-full-screen',"0");
    }
} 

// Mobile menu
$(".mobile-toggle-nav").click(function(){
	$(this).toggleClass("is-active");
	$(".app-container").toggleClass("sidebar-mobile-open");
});
$(".mobile-toggle-header-nav").click(function(){
	$(this).toggleClass("active");
	$(".app-header__content").toggleClass("header-mobile-open")
});
// Get cart count
$.ajax({
	url : base_url+"/get-count-of-cart-product", 
	type: 'POST',
	headers: {
	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	},
	dataType:'json',
	beforeSend:function(){  
		//showLoader();
	},
	success: function(res){
		$('#cart_count').html(res);
		$('#hidden_cart_count').val(res);
	},
	error: function(e) {
		hideLoader();
	},
	complete:function(){
		hideLoader();
	}
});
$(document).keydown(function(event) { 
  if (event.keyCode == 27) {
    $('#CommonModal').modal('hide');
    $('#OrderPreviewModal').modal('hide');
    $('#LocationModal').modal('hide');
  }
});
