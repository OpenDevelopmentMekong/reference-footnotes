(function($) {

	tinymce.create('tinymce.plugins.ReferenceFootnotes', {
		/**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
		init : function(ed, url) {
			ed.addCommand('referencefootnote', function() {
				ed.windowManager.open({
					id : 'reference-footnotes',
					title: 'Reference Footnote',
					width : 600 + parseInt(ed.getLang('example.delta_width', 0)),
					height : 250 + parseInt(ed.getLang('example.delta_height', 0)),
					wpDialog : true
				}, {
					plugin_url : url
				});
			});

			ed.addButton('referencefootnote', {
				title : 'Reference Footnote',
				cmd : 'referencefootnote',
				image : url + '/../reference-footnote-icon.png'
			});

		},

		/**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
		createControl : function(n, cm) {
			return null;
		},

		/**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
		getInfo : function() {
			return {
				longname : 'Reference Footnote',
				author : 'ODC: Huy Eng',
				authorurl : 'https://opendevelopmentmekong.net/',
				//infourl : 'https://opendevelopmentmekong.net/',
				version : "1.0.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('referencefootnote', tinymce.plugins.ReferenceFootnotes);

	function insert_reference_footnote(){
        var plugin_name = '#reference-footnotes';
		$(plugin_name).on('submit', function(evt) {
			var $content = $(plugin_name + '-content'),
			    reference_footnote = $.trim($content.val());
			// Now that we have the footnote content, clear the textarea
			$content.val('');
			if (reference_footnote.length)
                // Set the regex string
                var regex = /(https?:\/\/([-\w\.]+)+(:\d+)?(\/([-\w\/_\.]*(\?\S+)?)?)?)/ig
                // Replace plain text links by hyperlinks
                var reference_footnote_update = reference_footnote.replace(regex, "<a href='$1' target='_blank'>$1</a>");
            	tinymce.execCommand('mceInsertContent', false, '[ref]' + reference_footnote_update + '[/ref]');
			    tinymce.activeEditor.windowManager.close();
			    evt.preventDefault();
		});
		$(plugin_name + '-cancel').on('click', function(evt) {
			evt.preventDefault();
			tinymce.activeEditor.windowManager.close();
		});
		// Customize toolbar icon img element to support hover and image sprite (has to be window.onLoad because toolbar is not yet loaded)
		/* $(window).on('load', function() {
			$('.mce_simple-footnote img').css({
				height: '40px'
			}).on('mouseenter', function() {
				$(this).css('margin-top', '-20px');
			}).on('mouseleave', function() {
				$(this).css('marginTop', '');
			}).parent().css({
				height: '20px',
				overflow: 'hidden'
			});
		}); */
    }

	 jQuery(document).ready(insert_reference_footnote);

})(jQuery);