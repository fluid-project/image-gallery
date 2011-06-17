<?php
define('FLUID_IG_INCLUDE_PATH', 'include/');
include(FLUID_IG_INCLUDE_PATH . "vitals.inc.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Uploader</title>
        
        <link rel="stylesheet" type="text/css" href="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/fss/css/fss-reset.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/fss/css/fss-layout.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/css/Uploader.css" />
        <link rel="stylesheet" type="text/css" href="css/image-gallery.css" />
        
        <!-- Fluid and jQuery Dependencies -->
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/jquery/core/js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/jquery/ui/js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/jquery.keyboard-a11y.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/jquery/plugins/scrollTo/js/jquery.scrollTo.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/swfobject/js/swfobject.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/swfupload/js/swfupload.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/json/js/json2.js"></script>

        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/Fluid.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/DataBinding.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/FluidIoC.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/FluidDocument.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/FluidView.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/FluidRequests.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/core/js/FluidDOMUtilities.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/lib/fastXmlPull/js/fastXmlPull.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/renderer/js/fluidRenderer.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/renderer/js/fluidParser.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/renderer/js/RendererUtilities.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/framework/enhancement/js/ProgressiveEnhancement.js"></script>
        
        <!-- Uploader dependencies -->
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/Uploader.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/FileQueue.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/progress/js/Progress.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/FileQueueView.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/ErrorsView.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/MimeTypeExtensions.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/FlashUploaderSupport.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/Flash9UploaderSupport.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/HTML5UploaderSupport.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/DemoUploadManager.js"></script>

        <!-- The Uploader demo -->
        <script type="text/javascript" src="js/image-gallery.js"></script>
    </head>
    <body>
        <div class="ig-imageGallery igStyle-imageGallery">
            <h1>Image Gallery Uploader Demo</h1>
            <!-- Basic upload controls, used when JavaScript is unavailable -->
            <div class="igStyle-uploaders">
                <form action="uploader.php" method="post" enctype="multipart/form-data" class="fl-progEnhance-basic">
                    <div>
                        <input type="hidden" name="isSingleUploader" value="1" />
                        <p>Use the Browse button to add a file, and the Save button to upload it.</p>
                        <input name="fileData" type="file" />
                        <input class="fl-uploader-basic-save" type="submit" value="Save"/>
                    </div>
                </form>
            
                <div class="ig-multiFileUploader">
                    <!-- The Uploader's template will be injected via AJAX into this container. -->
                </div>
            </div>
        
            <div>
                <h2>Uploaded images:</h2>
                <div class="igStyle-imageViewer">
                    <!-- The container to display all the uploaded images. -->
                    <div class="ig-imageViewer-images igStyle-imageViewer-images"></div>
                </div>
            </div>
            
            <div class="ig-serverErrors">
                <!-- The returned server error message will be displayed in this container. -->
            </div>
        
            <div class="ig-settings fl-hidden">
                <h2>Demo settings</h2>
                <div class="ig-settings-field">
                    <label for="fileSizeLimit">File size limit:</label>
                    <select id="fileSizeLimit">
                    </select> MB
                </div>
                
                <div class="ig-settings-field">
                    <label for="fileUploadLimit">Upload queue limit:</label>
                    <select id="fileUploadLimit">
                    </select>
                </div>
                    
                <div class="ig-settings-field">
                <span>Allowed image types:</span>
                        <fieldset>
                            <span class="igStyle-fileTypes fileTypes-row">
                                <input class="fileTypes-choice" id="fileTypes-choice" type="checkbox" />
                                <label class="fileTypes-label" for="fileTypes-choice">choice</label>
                            </span>
                        </fieldset>
                </div>
            </div>
            
            <div>
                <h2>Demo notes:</h2>
                <ul>
                    <li>The session will stay active for one hour. After one hour, any uploaded images will be erased from the server.</li>
                    <li>Changing demo settings in IE 7 and IE 8 is known to cause periodic errors.</li>
                    <li>Demo setting "Allowed image types" is only supported by IE, not other browsers.</li>
                </ul>
            </div>
        </div>
        
        <script type="text/javascript">
            demo.imageGallery(".ig-imageGallery", {
                components: {
                    pathResolver: {
                        options: {
                            prefixes: {
                                infusion: "<?php echo FLUID_IG_INFUSION;?>"
                            }
                        }
                    }
                }
            });
        </script>
    </body>
</html>
