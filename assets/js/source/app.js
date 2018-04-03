(function($){
	WebFontConfig = {
		google: { families: [ 'Material+Icons' ] },
		active: function() {
			sessionStorage.fonts = true;
		}
	};
	
	var $source = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
	
	(function() {
		var wf = document.createElement('script');
		wf.src = $source;
		wf.type = 'text/javascript';
		wf.async = 'true';
	
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(wf, s);
	})();

	$(document).ready(function(){
		$('.gallery').find('br').detach();
	});
})(jQuery);