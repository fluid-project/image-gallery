/*
Copyright 2011 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://github.com/fluid-project/infusion/raw/master/Infusion-LICENSE.txt
*/

// Declare dependencies
/*global fluid_1_4:true, jQuery*/

// JSLint options 
/*jslint white: true, funcinvoke: true, undef: true, newcap: true, nomen: true, regexp: true, bitwise: true, browser: true, forin: true, maxerr: 100, indent: 4 */

var demo = demo || {};

(function ($, fluid) {

    /**
     * ImageGallery represents the client-side behaviour for the Uploader Image Gallery demo.
     */
    fluid.defaults("demo.imageGallery", {
        gradeNames: ["fluid.viewComponent", "autoInit"],
        finalInitFunction: "demo.imageGallery.init",
                
        components: {
            checker: {
                type: "fluid.progressiveCheckerForComponent",
                options: {
                    componentName: "fluid.uploader"
                }
            },
            
            uploader: {
                type: "fluid.uploader",
                createOnEvent: "onReady",
                container: "{imageGallery}.dom.uploader",
                options: {
                    components: {
                        strategy: {
                            options: {
                                flashMovieSettings: {
                                    flashURL: "../infusion/src/webapp/lib/swfupload/flash/swfupload.swf",
                                    flashButtonImageURL: "../infusion/src/webapp/components/uploader/images/browse.png"
                                }
                            }
                        }
                    },
                    queueSettings: {
                        uploadURL: "{imageGallery}.uploadURL",
                        fileTypes: ["image/gif", "image/jpeg", "image/png", "image/tiff"],
                        fileSizeLimit: "20480",
                        fileUploadLimit: 0
                    },
                    // Boil Uploader's onFileSuccess and onFileError to match our component's semantics.
                    events: {
                        onImageSuccess: {
                            event: "onFileSuccess",
                            args: [
                                {
                                    fileName: "{arguments}.0.name",
                                    srcURL: "{arguments}.1"
                                }
                            ]
                        },
                        onError: {
                            event: "onFileError",
                            args: [
                                {
                                    fileName: "{arguments}.0.name",
                                    statusCode: "{arguments}.2"
                                }
                            ]
                        }
                    },
                    listeners: {
                        onImageSuccess: "{imagesView}.render",
                        onError: "{errorsView}.render"
                    }
                }
            },
            
            imagesView: {
                type: "demo.imageGallery.simpleRenderer",
                container: "{imageGallery}.dom.images",
                options: {
                    template: "<img src='%srcURL' alt='%fileName' class='image-frame' />"
                }
            },
            
            errorsView: {
                type: "demo.imageGallery.simpleRenderer",
                container: "{imageGallery}.dom.errors",
                options: {
                    template: "<div>%fileName failed to upload. HTTP status code: %statusCode</div>"
                }
            },
            
            settings: {
                type: "demo.imageGallery.settings",
                createOnEvent: "onReady"
            }
        },
        
        selectors: {
            uploader: "#multi-file-uploader",
            settings: "#multi-file-uploader-settings",
            images: "#image-space",
            errors: "#server-error"
        },
        
        events: {
            onReady: null
        },
        
        templateURLSelector: "../infusion/src/webapp/components/uploader/html/Uploader.html .fl-uploader",
        serverURLPrefix: "uploader.php?session="
    });
    
    demo.imageGallery.init = function (that) {
        that.sessionID = Math.random().toString(16).substring(2);
        that.uploadURL = that.options.serverURLPrefix + that.sessionID;    
        
        that.loadUploaderTemplate = function () {
            that.locate("uploader").load(that.options.templateURLSelector, null, function () {
                that.events.onReady.fire();
            });
        };
        
        that.destroyUploader = function () {
            if (typeof (that.uploader.strategy.engine) !== "undefined") {
                that.uploader.strategy.engine.swfUpload.destroy();
            }
            that.locate("uploader").empty();
        };
        
        that.resetUploader = function (options) {
            // TODO: use {imageGallery}.options.component.uploader.options.queueSetting as
            // the model for the Settings component.
            var queueSettings = that.options.components.uploader.options.queueSettings;
            fluid.each(options, function (value, key) {
                queueSettings[key] = value;
            });
            that.destroyUploader();
            that.loadUploaderTemplate();
        };
        
        that.loadUploaderTemplate();
    };
    
    
    /**
     * SimpleRenderer injects a single element rendered from a string template into the DOM.
     */
    fluid.defaults("demo.imageGallery.simpleRenderer", {
        gradeNames: ["fluid.viewComponent", "autoInit"],
        finalInitFunction: "demo.imageGallery.simpleRenderer.init",
        template: ""
    });
    
    demo.imageGallery.simpleRenderer.init = function (that) {
        that.render = function (values) {
            var renderedMarkup = fluid.stringTemplate(that.options.template, values);
            that.container.append(renderedMarkup);
        };
    };
    
    
    /**
     * Settings controls the form that allow a user to customize the Uploader's options.
     */
    fluid.defaults("demo.imageGallery.settings", {
        gradeNames: ["fluid.viewComponent", "autoInit"],
        finalInitFunction: "demo.imageGallery.settings.init",
        selectors: {
            fileTypes: "#allowed-file-type",
            fileSizeLimit: "#file-size-limit",
            fileUploadLimit: "#file-queue-limit",
            inputs: "input"
        },
        events: {
            onOptionChanged: null
        },
        listeners: {
            onOptionChanged: "{imageGallery}.resetUploader"
        }
    });
    
    demo.imageGallery.settings.init = function (that) {
        that.container.removeClass("hide-me");
        
        // TODO: Use the renderer and data binding for this.
        that.locate("inputs").change(function () {
            // Trim and parse input from the form fields.
            var options = {
                fileTypes: $.trim(that.locate("fileTypes").val()),
                fileSizeLimit: parseInt($.trim(that.locate("fileSizeLimit").val()), 10),
                fileUploadLimit: parseInt($.trim(that.locate("fileUploadLimit").val()), 10)
            };
            
            // Delete any empty options so we don't override the default.
            fluid.each(options, function (value, key) {
                if (!value) {
                    delete options[key];
                }
            });

        	that.events.onOptionChanged.fire(options);         	
        });
    };
    
    fluid.demands("demo.imageGallery.settings", ["demo.imageGallery", "fluid.uploader.multiFileUploader"], {
        funcName: "demo.imageGallery.settings",
        container: "{imageGallery}.dom.settings"
    });
    
    fluid.demands("demo.imageGallery.settings", ["demo.imageGallery", "fluid.uploader.singleFileUploader"], {
        funcName: "fluid.emptySubcomponent"
    });
    
})(jQuery, fluid);
