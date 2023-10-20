// This script is loaded both on the frontend page and in the Visual Builder.

jQuery(function($) {
    /**
     * News Ticker Slide width Calculation
     */
    var newsTicker = $(".dtq-news-tricker");
    if (newsTicker && newsTicker.length > 0) {
        newsTicker.each(function() {
            var _width = $(this)
                .find("#parent")
                .width();
            $(this)
                .get(0)
                .style.setProperty(`--width`, `${_width}px`);
        });
    }

    /**
     * Scroll Image Height/Width Calculation
     * Set scroll height/width in css variable
     */
    var scrollImage = $(".dtq-scroll-image");
    if (scrollImage && scrollImage.length > 0) {
        scrollImage.each(function() {
            var _img = $(this).find(".dtq-scroll-image-el");
            var _dir_hover = $(this).data("dir-hover");
            var _dir_scroll = $(this).data("dir-scroll");
            if (_dir_hover !== "none") {
                var operator = "";
                var offset =
                    _dir_hover[0] === "X"
                        ? parseInt(_img.width()) - parseInt($(this).width())
                        : parseInt(_img.height()) - parseInt($(this).height());
                if (parseInt(offset) < 1) {
                    offset = 0;
                }
                if (_dir_hover === "X_rtl" || _dir_hover === "Y_btt") {
                    operator = "-";
                }

                $(this).on("mouseenter", function() {
                    _img.css(
                        "transform",
                        "translate" +
                            _dir_hover[0] +
                            "(" +
                            operator +
                            offset +
                            "px)"
                    );
                });

                $(this).on("mouseleave", function() {
                    _img.css(
                        "transform",
                        "translate" + _dir_hover[0] + "(0px)"
                    );
                });
            }

            if (_dir_scroll !== "none") {
                var _overlay = $(this).find(".dtq-scroll-image-overlay");
                if (_dir_scroll === "horizontal") {
                    _overlay.css({
                        width: _img.width(),
                        height: _img.height()
                    });
                } else {
                    _overlay.css({
                        width: "100%",
                        height: "100%"
                    });
                }
            }
        });
    }

    /**
     * Youtube && Vimeo Video lightbox
     */
    let popupVideo = $(".dtq-popup-yt, .dtq-popup-vm");
    if (popupVideo && popupVideo.length > 0) {
        popupVideo.each(function() {
            let href = $(this).attr("href");
            let type = $(this).data("type");
            let source = $(this).data("source");
            let videoSource = "";

            if (type === "vm") {
                let suffix = "";
                if (-1 === href.indexOf("?")) {
                    suffix = "?autoplay=1";
                } else {
                    suffix = "&autoplay=1";
                }

                if (-1 === href.indexOf("player")) {
                    videoSource = "//player.vimeo.com/video/%id%" + suffix;
                } else {
                    videoSource = href + suffix;
                }
            }

            $(this).magnificPopup({
                type: "iframe",
                mainClass: "mfp-fade",
                removalDelay: 160,
                preloader: false,
                fixedContentPos: false,
                iframe: {
                    markup:
                        '<div class="mfp-iframe-scaler">' +
                        '<div class="mfp-close"></div>' +
                        '<iframe class="mfp-iframe" frameborder="0" allow="autoplay" allowfullscreen></iframe>' +
                        "</div>",
                    patterns: {
                        youtube: {
                            index: "youtube.com/",
                            src: "//www.youtube.com/embed/%id%?autoplay=1"
                        },
                        vimeo: {
                            index: "vimeo.com/",
                            id: "/",
                            src: videoSource
                        }
                    }
                },
                callbacks: {
                    beforeOpen: function() {
                        var triggerEl = $(this.st.el),
                            order_class = triggerEl.data("order");
                        $("body").addClass(
                            `dtq-video-open dtq-video-popup-${order_class}`
                        );
                    },

                    close: function() {
                        var triggerEl = $(this.st.el),
                            order_class = triggerEl.data("order");
                        $("body").removeClass(
                            `dtq-video-open dtq-video-popup-${order_class}`
                        );
                    }
                }
            });
        });
    }

    $(".dtq-popup-video").magnificPopup({
        type: "inline",
        mainClass: "mfp-fade",
        removalDelay: 100,
        closeOnContentClick: false,
        midClick: false,
        callbacks: {
            beforeOpen: function() {
                $("body").addClass("dtq-modal-open dtq-video-popup");
            },
            open: function() {
                var targetEl = $(this.st.el).data("mfp-src");
                $(".dtq-modal").addClass("open");
                $(`${targetEl} video`).trigger("play");
            },
            close: function() {
                var targetEl = $(this.st.el).data("mfp-src");
                $(`${targetEl} video`).trigger("pause");
                $("body").removeClass("dtq-modal-open dtq-video-popup");
                $(".dtq-modal").removeClass("open");
            }
        }
    });

    // Image Compare
    $(".dtq-image-compare").each(function() {
        var offsetpct = $(this).data("offsetpct"),
            moveonhover = $(this).data("moveonhover"),
            orientation = $(this).data("orientation"),
            beforelabel = $(this).data("beforelabel"),
            afterlabel = $(this).data("afterlabel"),
            overlay = $(this).data("overlay");

        $(this)
            .find(".dtq-image-compare-container")
            .twentytwenty({
                default_offset_pct: offsetpct,
                move_slider_on_hover: moveonhover === "on" ? true : false,
                orientation: orientation,
                before_label: beforelabel,
                after_label: afterlabel,
                no_overlay: overlay === "on" ? false : true,
                move_with_handle_only: false,
                click_to_move: true
            });
    });

    // Tooltip Activation
    var tooltip = $(".dtq-tooltip");
    if (tooltip && tooltip.length > 0) {
        tippy(".dtq-tooltip", {
            trigger: "mouseenter"
        });
    }

    // Module Link Click Reactivation
    // This will work if click event don't work or disabled by other action
    if (typeof et_link_options_data !== "undefined") {
        et_link_options_data.forEach(function(el, i) {
            $(document).on(
                "click",
                `.${et_link_options_data[i].class}`,
                function() {
                    window.open(
                        et_link_options_data[i].url,
                        et_link_options_data[i].target
                    );
                }
            );
        });
    }

    // Animated Text
    // Text animation typed
    $(".dtq-animated-text").each(function() {
        let settings = $(this).data("settings"),
            target = $(this).attr("id"),
            type = $(this).data("type");

        // typing text type
        if ("typed" === type) {
            new Typed(`#${target} .dtq-typed-text`, settings);
        }
        // tilt type
        else if ("tilt" === type) {
            $(this)
                .find(".dtq-animated-text-tilt")
                .textillate(settings);

            console.log(settings);
        }
        // Slide type
        else if ("slide" === type) {
            $(this)
                .find(".dtq-animated-text-slide")
                .addClass("slide-initialized");
            $(this)
                .find(".dtq-animated-text-slide li")
                .removeClass("text-in");
            $(this)
                .find(".dtq-animated-text-slide li")
                .first()
                .addClass("text-in");

            setInterval(() => {
                let activeText = $(this).find(
                    ".dtq-animated-text-slide .text-in"
                );
                activeText.addClass("text-out");
                activeText.removeClass("text-in");
                if (activeText.next().is("li")) {
                    activeText.next().removeClass("text-out");
                    activeText.next().addClass("text-in");
                } else {
                    $(this)
                        .find(".dtq-animated-text-slide li")
                        .first()
                        .addClass("text-in");
                    $(this)
                        .find(".dtq-animated-text-slide li")
                        .first()
                        .removeClass("text-out");
                }
            }, parseInt(settings.slide_gap));
        }
    });

    // Popup
    $(".dtq-popup-front-mode").each(function() {
        let $target = $(this);
        let instance = null;
        let settings = $target.data("settings");
        instance = new window.baPopup($target, settings);
        instance.init();
    });

    // Alert Dismiss
    $(".dtq-alert-dismiss").each(function() {
        $(this).on("click", function() {
            $(this)
                .parents(".ba_alert")
                .fadeOut(400);
        });
    });

    // Counter.
    $(".dtq-counter .dtq-number-text").each(function(ignore, counter) {
        var counterUp = window.counterUp["default"];
        var waypoint = new Waypoint({
            element: $(this),
            handler: function() {
                counterUp(counter, {
                    duration: 1000,
                    delay: 16
                });
                this.destroy();
            },
            offset: "bottom-in-view"
        });
    });
});
