/*

Copyright 2011 OCAD University
Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://github.com/fluid-project/infusion/raw/master/Infusion-LICENSE.txt
*/

// Declare dependencies
/*global fluid, jQuery*/

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
            
            pathResolver: {
                type: "demo.imageGallery.pathResolver",
                options: {
                    prefixes: {
                        infusion: "../infusion"
                    },
                    paths: {
                        flashURL: "%infusion/src/webapp/lib/swfupload/flash/swfupload.swf",
                        flashButtonImageURL: "%infusion/src/webapp/components/uploader/images/browse.png",
                        templateURL: "%infusion/src/webapp/components/uploader/html/Uploader.html"
                    }
                }
            },
            
            imageUploader: {
                type: "fluid.uploader",
                createOnEvent: "onReady",
                container: "{imageGallery}.dom.uploader",
                options: {
                    components: {
                        strategy: {
                            options: {
                                flashMovieSettings: {
                                    flashURL: "{pathResolver}.paths.flashURL",
                                    flashButtonImageURL: "{pathResolver}.paths.flashButtonImageURL"
                                }
                            }
                        }
                    },
                    queueSettings: {
                        uploadURL: "{imageGallery}.uploadURL",
                        fileTypes: ["image/bmp", "image/gif", "image/jpeg", "image/png", "image/tiff"],
                        fileSizeLimit: "20480",
                        fileUploadLimit: 0
                    }
                }
            },
            
            imagesView: {
                type: "demo.imageGallery.simpleRenderer",
                container: "{imageGallery}.dom.images",
                options: {
                    template: "<img src='%srcURL' alt='%fileName' class='igStyle-imageFrame' />"
                }
            },
            
            errorsView: {
                type: "demo.imageGallery.simpleRenderer",
                container: "{imageGallery}.dom.errors",
                options: {
                    template: "<div class='igStyle-serverErrors'><span class='igStyle-errorTitle'>HTTP status code: %statusCode</span><span>%fileName failed to upload.</span> </div>"
                }
            },
            
            settings: {
                type: "demo.imageGallery.settings",
                createOnEvent: "onReady",
                options: {
                    model: "{imageGallery}.options.components.imageUploader.options.queueSettings",
                    listeners: {
                        modelChanged: "{imageGallery}.resetUploader"
                    }
                }
            }
        },
        
        selectors: {
            uploader: ".ig-multiFileUploader",
            settings: ".ig-settings",
            images: ".ig-imageViewer-images",
            errors: ".ig-serverErrors"
        },
        
        events: {
            onReady: null
        },
        
        // A selector pointing to the portion of the Uploader's template that we're interested in.
        templateSelector: ".fl-uploader",
        
        serverURLPrefix: "uploader-server.php?session="
    });
    
    demo.imageGallery.init = function (that) {
        that.sessionID = Math.random().toString(16).substring(2);
        that.uploadURL = that.options.serverURLPrefix + that.sessionID;    
        
        that.loadUploaderTemplate = function () {
            var urlSelector = that.pathResolver.paths.templateURL + " " + that.options.templateSelector;
            that.locate("uploader").load(urlSelector, function () {
                that.events.onReady.fire();
            });
        };
        
        that.destroyUploader = function () {
            that.locate("uploader").empty();
            if (fluid.get(that, "uploader.strategy.engine")) {
                var su = that.imageUploader.strategy.engine.swfUpload;
                su.destroy();
            }
        };
        
        that.resetUploader = function (options) {
            that.destroyUploader();
            that.loadUploaderTemplate();
        };
        
        that.loadUploaderTemplate();
    };
    
    fluid.defaults("demo.imageGallery.pathResolver", {
        gradeNames: ["fluid.littleComponent", "autoInit"],
        finalInitFunction: "demo.imageGallery.pathResolver.init"
    });
    
    demo.imageGallery.pathResolver.init = function (that) {
        that.paths = {};
        fluid.each(that.options.paths, function (path, name) {
            that.paths[name] = fluid.stringTemplate(path, that.options.prefixes);
        });
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

        that.clear = function () {
            that.container.html("");
        };
    };
    
    
    /**
     * Settings controls the form that allow a user to customize the Uploader's options.
     */
    fluid.defaults("demo.imageGallery.settings", {
        gradeNames: ["fluid.rendererComponent", "autoInit"],
        finalInitFunction: "demo.imageGallery.settings.init",
        selectors: {
            fileSizeLimit: "#fileSizeLimit",
            fileUploadLimit: "#fileUploadLimit",
            "fileTypesRowID:": ".fileTypes-row",
            fileTypesInputID: ".fileTypes-choice",
            fileTypesLabelID: ".fileTypes-label"
        },
        model: {
            labelMap: {
                fileSizeLimit: {
                    labels: ["1", "5", "10", "15", "20"],
                    values: ["1024", "5120", "10240", "15360", "20480"]
                },
                fileUploadLimit: {
                    labels: ["No Limit", "5", "10", "15", "20"],
                    values: ["0", "5", "10", "15", "20"]
                },
                fileTypes: {
                    labels: ["BMP", "GIF", "JPG, JPEG, JPE, JFIF", "PNG", "TIF, TIFF"],
                    values: ["image/bmp", "image/gif", "image/jpeg", "image/png", "image/tiff"]
                }
            }
        },
        protoTree: {
            fileSizeLimit: {
                optionnames: "${labelMap.fileSizeLimit.labels}",
                optionlist: "${labelMap.fileSizeLimit.values}",
                selection: "${fileSizeLimit}"
            },
            fileUploadLimit: {
                optionnames: "${labelMap.fileUploadLimit.labels}",
                optionlist: "${labelMap.fileUploadLimit.values}",
                selection: "${fileUploadLimit}"
            },
            expander: {
                type: "fluid.renderer.selection.inputs",
                inputID: "fileTypesInputID",
                tree: {
                    optionnames: "${labelMap.fileTypes.labels}",
                    optionlist: "${labelMap.fileTypes.values}",
                    selection: "${fileTypes}"
                },
                rowID: "fileTypesRowID",
                selectID: "fileTypes",
                labelID: "fileTypesLabelID"
            }
        },
        events: {
            modelChanged: null
        },
        styles: {
            hidden: "fl-hidden"
        }
    });
    
    demo.imageGallery.settings.init = function (that) {
        // TODO: Replace this with a declarative listener when the framework supports it.
        that.applier.modelChanged.addListener("*", function (model) {
            that.events.modelChanged.fire(model);
        });
        
        that.refreshView();
        that.container.removeClass(that.options.styles.hidden);
    };
    
    fluid.demands("demo.imageGallery.settings", ["demo.imageGallery", "fluid.uploader.multiFileUploader"], {
        funcName: "demo.imageGallery.settings",
        container: "{imageGallery}.dom.settings"
    });
    
    fluid.demands("demo.imageGallery.settings", ["demo.imageGallery", "fluid.uploader.singleFileUploader"], {
        funcName: "fluid.emptySubcomponent"
    });
    
    // Boil Uploader's onFileSuccess and onFileError to match our component's semantics.
    var EventOpts = {
        events: {
            onSuccess: {
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
            },
            onUploadStart: {
                event: "onUploadStart"
            }
        },
        listeners: {
            onSuccess: "{imagesView}.render",
            onError: "{errorsView}.render",
            onUploadStart: "{errorsView}.clear"
        }
    };
    
    // Bind event options only with multiFileUplaoder, not singleFileUploader, since the boiled
    // events are only available at multiFileUploader.

    // Note that the demands context uses underlying components rather than the direct use of
    // fluid.uploader.multiFileUploader because "imageUploader" is an instance of "fluid.uploader"
    // that the matching on multiFileUploader is too late to pick up the desired options.
    fluid.demands("imageUploader", ["demo.imageGallery", "fluid.uploader.html5"], {
        options: EventOpts
    });
    
    fluid.demands("imageUploader", ["demo.imageGallery", "fluid.uploader.swfUpload"], {
        options: EventOpts
    });
    
    fluid.demands("imageUploader", ["demo.imageGallery", "fluid.uploader.singleFile"], {
        options: "{options}"
    });
    
})(jQuery, fluid);
