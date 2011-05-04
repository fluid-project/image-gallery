/*global jQuery*/
/*global fluid*/

var demo = demo || {};

(function ($, fluid) {
    demo.initUploader = function () {
        var sessionID = Math.random().toString(16).substring(2);
    
        // Load the Uploader's template via AJAX and inject it into this page.
        var templateURLSelector = "../infusion/src/webapp/components/uploader/html/Uploader.html .fl-uploader";
        var defaultAllowedFileType = "*.gif;*.jpeg;*.jpg;*.png;*.tiff;*.tif";
        var defaultFileSizeLimit = "20480";
        var defaultfileUploadLimit = 0;
        
        var instantiateUploader = function (fileType, fileSize, fileUpload) {
        	$("#multi-file-uploader").load(templateURLSelector, null, function () {
            
                // Initialize the Uploader
                fluid.uploader(".flc-uploader", {
                    components: {
                        strategy: {
                            options: {
                                flashMovieSettings: {
                                    flashURL: "[INFUSION_PATH]/src/webapp/lib/swfupload/flash/swfupload.swf",
                                    flashButtonImageURL: "[INFUSION_PATH]/src/webapp/components/uploader/images/browse.png"
                                }
                            }
                        }
                    },
                    queueSettings: {
                        // Set the uploadURL to the URL for posting files to your server.
                        uploadURL: "uploader.php?session=" + sessionID,
                        fileTypes: fileType,
                        fileSizeLimit: fileSize,
                        fileUploadLimit: fileUpload
                    },
                    listeners: {
                        onFileSuccess: function (file, responseText, xhr) {
                            // the server code passes the new image URL in the responseText
                            $('#image-space').append('<img src="' + responseText + '" alt="' + file.name + '" class="image-frame" />');
                        },
                        onFileError: function (file, error, status, xhr) {
                            $('#server-error').append(file.name + " : Failed uploading. HTTP Status Code: " + status + "<br />");
                        }
                    }
                });
            });
        };
        
        $("input").change(function (){
        	var allowedFileType = $.trim($("#allowed-file-type").val());
        	var fileSizeLimit = $.trim($("#file-size-limit").val());
        	var fileUploadLimit = $.trim($("#file-queue-limit").val());
        	
        	allowedFileType = (allowedFileType === "") ? defaultAllowedFileType : allowedFileType;
        	fileSizeLimit = (fileSizeLimit === "") ? defaultFileSizeLimit : parseInt(fileSizeLimit);
        	fileUploadLimit = (fileUploadLimit === "") ? defaultfileUploadLimit : parseInt(fileUploadLimit);
        	
//        	alert("file type: "+ allowedFileType+"; file size: "+fileSizeLimit+"; file queue: "+ fileUploadLimit);
        	instantiateUploader(allowedFileType, fileSizeLimit, fileUploadLimit);
        });

        instantiateUploader(defaultAllowedFileType, defaultFileSizeLimit, defaultfileUploadLimit);
    };
})(jQuery, fluid);