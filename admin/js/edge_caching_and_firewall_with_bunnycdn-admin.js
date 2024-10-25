(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( window ).load(function() {
		$("#show_bunnycdn_api_key").on("click", function () {

			var input = $("#edge_caching_and_firewall_with_bunnycdn_bunnycdn_api_key");
			var button = $(this);
			var type = input.attr("type");

			if (type == "text") {
				input.attr("type", "password");
				button.html("Show");
				return true;
			} 

			input.attr("type", "text");
			button.html("Hide");
		});	 

		$("#site_version_switching").on("click", function (e) {
			e.preventDefault()
			var switching_confirmation = confirm("Are you sure you want to switch WordPress from non-www to www?");
			if (switching_confirmation) {
				$("#edge_caching_and_firewall_with_bunnycdn_site_version").val("www");
				$("input[type='submit']").click();
				return false;
			}			
		});

	});

})( jQuery );
