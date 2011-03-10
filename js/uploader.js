/*global jQuery*/
/*global fluid*/
/*global demo*/

var demo = demo || {};

(function ($, fluid) {
    demo.initUploader = function () {
    	var sessionID = Math.random().toString(16).substring(2);
    	
        // Load the Uploader's template via AJAX and inject it into this page.
        var templateURLSelector = "../infusion-mlam/src/webapp/components/uploader/html/Uploader.html .fl-uploader";
        $("#uploader-contents").load(templateURLSelector, null, function () {
            
            // Initialize the Uploader
            fluid.uploader(".flc-uploader", {
                components: {
                    strategy: {
                        options: {
                            flashMovieSettings: {
                                flashURL: "../infusion-mlam/src/webapp/lib/swfupload/flash/swfupload.swf",
                                flashButtonImageURL: "../infusion-mlam/src/webapp/components/uploader/images/browse.png"
                            }
                        }
                    }
                },
            	queueSettings: {
                    // Set the uploadURL to the URL for posting files to your server.
                    uploadURL: "uploader.php?session="+sessionID
                },
                listeners: {
                    onFileSuccess: function (file, responseText, xhr){
                        // the server code passes the new image URL in the responseText
                        $('#image-space').append('<img src="' + responseText + '" alt="' + file.name + '" class="image-frame" />');
                    },
                    onFileError: function (file, error, status, xhr){
                    	$('#server-error').append(file.name + " - " + xhr.responseText + "<br />");
                    }
                }
            });
        });
    };
})(jQuery, fluid);


  