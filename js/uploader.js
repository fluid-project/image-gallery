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
                    listeners: {
                        onFileSuccess: "{imagesView}.showFile",
                        onFileError: "{errorsView}.showError"
                    }
                }
            },
            
            imagesView: {
                type: "demo.imageGallery.imagesView",
                container: "{imageGallery}.dom.images"
            },
            
            errorsView: {
                type: "demo.imageGallery.errorsView",
                container: "{imageGallery}.dom.errors"
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
    
    demo.imageGallery.renderAndAppend = function(container, template, values) {
        var renderedMarkup = fluid.stringTemplate(template, values);
        container.append(renderedMarkup);
    };
    
    /**
     * ImagesView manages the displayed images area of the page.
     */
    fluid.defaults("demo.imageGallery.imagesView", {
        gradeNames: ["fluid.viewComponent", "autoInit"],
        invokers: {
            showFile: {
                funcName: "demo.imageGallery.renderAndAppend",
                args: [
                    "{imagesView}.container",
                    "{imagesView}.options.imageMarkup",
                    {
                        fileName: "{arguments}.0.name",
                        srcURL: "{arguments}.1"
                    }
                ]
            }
        },
        imageMarkup: "<img src='%srcURL' alt='%fileName' class='image-frame' />"
    });
    
    /**
     * ErrorsView displays any server errors to the user.
     */
    fluid.defaults("demo.imageGallery.errorsView", {
        gradeNames: ["fluid.viewComponent", "autoInit"],
        invokers: {
            showError: {
                funcName: "demo.imageGallery.renderAndAppend",
                args: [
                    "{errorsView}.container",
                    "{errorsView}.options.errorMarkup",
                    {
                        fileName: "{arguments}.0.name",
                        statusCode: "{arguments}.2"
                    }
                ]
            }
        },
        errorMarkup: "<div>%fileName failed to upload. HTTP status code: %statusCode</div>"
    });
    
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
