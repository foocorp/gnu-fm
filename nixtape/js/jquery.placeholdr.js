/**
* Placeholdr: a jQuery Plugin
* @author: Jonathan Stahlhacke
* @url: http://www.porkcullis.com/lab/placeholdr/
* @documentation: http://www.porkcullis.com/lab/placeholdr/
* @published: 2009-09-02
* @license: MIT
*
*/
if(typeof jQuery != 'undefined') {
	jQuery(function($) {
		$.fn.extend({
			placeholdr: function(options) {
				var settings = $.extend({}, $.fn.placeholdr.defaults, options);
			
				return this.filter(":text").each(
					function() {
						if($.fn.jquery < '1.2.6') {return;}
						var $t = $(this);
						var o = $.metadata ? $.extend({}, settings, $t.metadata()) : settings;
						
						$t.addClass(settings.className);
						
						var phtext = $t.val();
						if (settings.placeholderText != null)
						{
							phtext = settings.placeholderText;
							if ($t.val() == "")
							{
								$t.val(phtext);
							}
							else
							{
								$t.removeClass(settings.className);
							}
						}
						$t.data("placeholdr_text", phtext);
						
						$t.focus(function(){
							if ($(this).hasClass(settings.className))
							{
								$(this).removeClass(settings.className);
								$(this).val("");
							}
						});
						$t.blur(function(){
							if ($(this).val() == "")
							{
								$(this).addClass(settings.className);
								$(this).val($(this).data("placeholdr_text"));
							}
						});
						if (settings.clearOnSubmit)
						{
							$t.parents("form").submit(function(){
								if ($t.hasClass(settings.className))
								{
									$t.val("");
								}
							});
						}
					}
				);
			}
		});
		
		/**
		* Set your Plugin Defaults Here…
		*/
		$.fn.placeholdr.defaults = {
				className: 'placeholder',
				placeholderText: null,
				clearOnSubmit: true
			};
	});
}
