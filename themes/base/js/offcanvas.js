jQuery(document).ready(function($){
	var $offcanvas = $('.t4-offcanvas');
	setTimeout(function(){
		$offcanvas.show();
	},300);
	$('.t4-wrapper').addClass('c-offcanvas-content-wrap');
	$offcanvas.offcanvas({
		triggerButton: '#triggerButton' ,
		onOpen: function () {
			bodyScrollLock.disableBodyScroll ($('.t4-off-canvas-body').get(0))
		},
		onClose: function () {
			setTimeout(function(){
				bodyScrollLock.enableBodyScroll ($('.t4-off-canvas-body').get(0))
			},300);
		}
	})
});