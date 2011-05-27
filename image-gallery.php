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
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/MimeTypeExtensions.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/FlashUploaderSupport.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/Flash9UploaderSupport.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/HTML5UploaderSupport.js"></script>
        <script type="text/javascript" src="<?php echo FLUID_IG_INFUSION;?>/src/webapp/components/uploader/js/DemoUploadManager.js"></script>

        <!-- The Uploader demo -->
        <script type="text/javascript" src="js/image-gallery.js"></script>
    </head>
    <body>
        <div class="ig-imageGallery">
            <!-- Basic upload controls, used when JavaScript is unavailable -->
            <div class="ig-uploaders">
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
        
            <div class="ig-imageViewer">
                <!-- The container to display all the uploaded images. -->
                <h2>Uploaded images:</h2>
                <div class="ig-imageViewer-images"></div>
            </div>
        
            <div class="ig-serverErrors">
                <!-- The returned server error message will be displayed in this container. -->
            </div>
        
            <div class="ig-notes">
                <p>
                    Notes:
                    <ul>
                        <li>Your session will stay active for an hour. After one hour, all the uploaded images will be erased from the server.</li>
                        <li>To test with specific file types, set the fileTypes option to a comma-delimited list of MIME types.</li>
                        <li>Reconfiguring the Uploader in IE 7-8 is known to cause periodic explosions.</li>
                    </ul>
            </div>
        
            <div class="ig-settings fl-hidden">
                <fieldset>
                    <legend>Uploader Settings</legend>
                    <table class="settingsTable noBorder">
                        <tr>
                            <td class="noBorder"><label for="fileSizeLimit">File Size Limit:</label></td>
                            <td class="noBorder"><input id="fileSizeLimit" type="text" /> in KB</td>
                        </tr>
                        <tr>
                            <td class="noBorder"><label for="fileUploadLimit">File Queue Limit:</label></td>
                            <td class="noBorder"><input id="fileUploadLimit" type="text" /></td>
                        </tr>
                        <tr>
                            <td class="noBorder"><label for="fileTypes">Allowed File MIME Types:</label></td>
                            <td class="noBorder"><input id="fileTypes" name="allowedFileType" type="text" /></td>
                        </tr>
                    </table>
                </fieldset>
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
