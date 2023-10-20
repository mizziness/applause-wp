import $ from "jquery";
import React, { Component } from "react";
import { findDOMNode } from "react-dom";
import Typed from "typed.js";
import "../../../assets/js/text-animation.min.js";
import { get_responsive_styles } from "../ModulesCore/ModulesCore";
import "./style.css";

class DTQ_Animated_Text extends Component {
    static slug = "ba_animated_text";

    static css(props) {
        let additionalCss = [];

        let text_alignment = get_responsive_styles(
            props,
            "text_alignment",
            "%%order_class%%",
            { primary: "text-align", important: false },
            { default: "left" }
        );

        let text_alignment_alt = [];
        if (props.layout === "inline") {
            text_alignment_alt = get_responsive_styles(
                props,
                "text_alignment",
                "%%order_class%% .dtq-animated-text-head",
                { primary: "justify-content", important: false },
                { default: "left" }
            );
        }

        // Prefix
        if (props.prefix_stroke) {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
                    declaration: `
				-webkit-text-stroke-width: ${props.prefix_stroke};
				-webkit-text-stroke-color: ${props.prefix_stroke_color};`,
                },
            ]);
        }

        if (props.prefix_text_color) {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
                    declaration: `-webkit-text-fill-color: ${props.prefix_text_color};`,
                },
            ]);
        }

        let prefix_padding = get_responsive_styles(
            props,
            "prefix_padding",
            "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
            { primary: "padding", important: false },
            { default: "0|0|0|0" }
        );

        let prefix_margin = get_responsive_styles(
            props,
            "prefix_margin",
            "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
            { primary: "margin", important: false },
            { default: "0|0|0|0" }
        );

        let prefix_bg = get_responsive_styles(
            props,
            "prefix_bg",
            "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
            { primary: "background", important: false },
            { default: "transparent" }
        );

        let prefix_radius = props.prefix_radius.split("|");
        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
                declaration: `border-radius: ${prefix_radius[1]} ${prefix_radius[2]}  ${prefix_radius[3]}  ${prefix_radius[4]};`,
            },
        ]);

        // Suffix
        if (props.suffix_stroke) {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-module .dtq-animated-text-suffix span",
                    declaration: `
				-webkit-text-stroke-width: ${props.suffix_stroke};
				-webkit-text-stroke-color: ${props.suffix_stroke_color};`,
                },
            ]);
        }

        if (props.suffix_text_color) {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-module .dtq-animated-text-suffix span",
                    declaration: `-webkit-text-fill-color: ${props.suffix_text_color};`,
                },
            ]);
        }

        let suffix_padding = get_responsive_styles(
            props,
            "suffix_padding",
            "%%order_class%% .dtq-module .dtq-animated-text-suffix span",
            { primary: "padding", important: false },
            { default: "0|0|0|0" }
        );

        let suffix_margin = get_responsive_styles(
            props,
            "suffix_margin",
            "%%order_class%% .dtq-module .dtq-animated-text-suffix span",
            { primary: "margin", important: false },
            { default: "0|0|0|0" }
        );

        let suffix_bg = get_responsive_styles(
            props,
            "suffix_bg",
            "%%order_class%% .dtq-module .dtq-animated-text-suffix span",
            { primary: "background", important: false },
            { default: "transparent" }
        );

        let suffix_radius = props.suffix_radius.split("|");
        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-module .dtq-animated-text-prefix span",
                declaration: `border-radius: ${suffix_radius[1]} ${suffix_radius[2]}  ${suffix_radius[3]}  ${suffix_radius[4]};`,
            },
        ]);

        // Animated Text
        if (props.animated_stroke) {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-module .dtq-animated-text-main",
                    declaration: `
				-webkit-text-stroke-width: ${props.animated_stroke};
				-webkit-text-stroke-color: ${props.animated_stroke_color};`,
                },
            ]);
        }

        if (props.main_text_color) {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-module .dtq-animated-text-main",
                    declaration: `-webkit-text-fill-color: ${props.main_text_color};`,
                },
            ]);
        }

        let animated_padding = get_responsive_styles(
            props,
            "animated_padding",
            "%%order_class%% .dtq-module .dtq-animated-text-main",
            { primary: "padding", important: false },
            { default: "0|0|0|0" }
        );

        let animated_margin = get_responsive_styles(
            props,
            "animated_margin",
            "%%order_class%% .dtq-module .dtq-animated-text-main",
            { primary: "margin", important: false },
            { default: "0|0|0|0" }
        );

        let animated_bg = get_responsive_styles(
            props,
            "animated_bg",
            "%%order_class%% .dtq-module .dtq-animated-text-main",
            { primary: "background", important: false },
            { default: "transparent" }
        );

        let animated_radius = props.animated_radius.split("|");
        additionalCss.push([
            {
                selector: "%%order_class%% .dtq-module .dtq-animated-text-main",
                declaration: `border-radius: ${animated_radius[1]} ${animated_radius[2]}  ${animated_radius[3]}  ${animated_radius[4]};`,
            },
        ]);

        // Cursor
        if (props.show_cursor === "on") {
            additionalCss.push([
                {
                    selector: "%%order_class%% .dtq-text-animation:after",
                    declaration: `
			  		display: block;
				  right: -${props.cursor_gap};
				  width: ${props.cursor_width};
				  background: ${props.cursor_color};
				  height: ${props.cursor_height};
				  `,
                },
            ]);
        }

        // Others
        additionalCss.push([
            {
                selector: "%%order_class%% .dtq-animated-text-slide li.text-in",
                declaration: `animation: ${props.slide_animation} 700ms;`,
            },
        ]);

        if (props.layout === "inline") {
            additionalCss.push([
                {
                    selector: "%%order_class%% .dtq-animated-text-head",
                    declaration: `display: flex; align-items: center;`,
                },
            ]);
        }

        return additionalCss
            .concat(text_alignment)
            .concat(prefix_padding)
            .concat(prefix_bg)
            .concat(prefix_margin)
            .concat(animated_padding)
            .concat(animated_bg)
            .concat(text_alignment_alt)
            .concat(animated_margin)
            .concat(suffix_padding)
            .concat(suffix_bg)
            .concat(suffix_margin);
    }

    componentDidMount() {
        if (this.props.animation_type === "typed") {
            const el = findDOMNode(this.refs.animatedText);

            if (!this.props.animated_text) {
                return;
            }

            let dataStrings = [],
                animation_speed =
                    this.props.animation_speed !== ""
                        ? this.props.animation_speed
                        : 0,
                typeStartDelay = this.props.start_delay,
                typeBackSpeed = this.props.back_speed,
                typeBackDelay = this.props.back_delay,
                loop = "on" === this.props.use_loop ? true : false;
            try {
                JSON.parse(this.props.animated_text).map((el, i) => {
                    dataStrings.push(el.value);
                    return false;
                });
            } catch (er) {
                dataStrings = this.props.animated_text.split("|");
            }

            if (el) {
                this.typed = new Typed(el, {
                    strings: dataStrings,
                    typeSpeed: parseInt(animation_speed),
                    loop: loop,
                    showCursor: false,
                    stringsElement: null,
                    startDelay: parseInt(typeStartDelay),
                    backSpeed: parseInt(typeBackSpeed),
                    backDelay: parseInt(typeBackDelay),
                });
            }
        }
    }

    componentDidUpdate(prevProps) {
        if (this.props.animation_type === "typed") {
            const el = findDOMNode(this.refs.animatedText);

            if (el && this.typed) {
                this.typed.destroy();
            }

            if (!this.props.animated_text) {
                return;
            }

            let dataStrings = [],
                animation_speed =
                    this.props.animation_speed !== ""
                        ? this.props.animation_speed
                        : 0,
                typeStartDelay = this.props.start_delay,
                typeBackSpeed = this.props.back_speed,
                typeBackDelay = this.props.back_delay,
                loop = "on" === this.props.use_loop ? true : false;

            try {
                JSON.parse(this.props.animated_text).map((el, i) => {
                    dataStrings.push(el.value);
                    return false;
                });
            } catch (er) {
                dataStrings = this.props.animated_text.split("|");
            }

            if (el) {
                this.typed = new Typed(el, {
                    strings: dataStrings,
                    typeSpeed: parseInt(animation_speed),
                    loop: loop,
                    showCursor: true,
                    stringsElement: null,
                    startDelay: parseInt(typeStartDelay),
                    backSpeed: parseInt(typeBackSpeed),
                    backDelay: parseInt(typeBackDelay),
                });
            }
        } else {
            if (this.typed) {
                this.typed.destroy();
            }
        }
    }

    render_prefix = () => {
        if (this.props.prefix) {
            return (
                <div className='dtq-animated-text-prefix'>
                    <span>{this.props.prefix}</span>
                    {this.props.layout === "inline" ? "\u00A0" : ""}
                </div>
            );
        }
    };

    render_suffix = () => {
        if (this.props.suffix) {
            return (
                <div className='dtq-animated-text-suffix'>
                    {this.props.layout === "inline" ? "\u00A0" : ""}
                    <span>{this.props.suffix}</span>
                </div>
            );
        }
    };

    animation_typed = () => {
        return (
            <div
                className='dtq-text-animation dtq-animated-text-main'
                ref='animatedText'
            />
        );
    };

    animation_tilt = () => {
        let animated_text = JSON.parse(this.props.animated_text);
        return (
            <div className='dtq-animated-text-tilt'>
                <ul className='texts dtq-animated-text-main'>
                    {animated_text &&
                        animated_text.map((el, i) => {
                            return <li key={i}>{el.value}</li>;
                        })}
                </ul>
            </div>
        );
    };

    animation_slide = () => {
        let animated_text = JSON.parse(this.props.animated_text);
        return (
            <ul className='dtq-animated-text-slide dtq-animated-text-main'>
                {animated_text &&
                    animated_text.map((el, i) => {
                        return <li key={i}>{el.value}</li>;
                    })}
            </ul>
        );
    };

    render() {
        let order = this.props.moduleInfo.order,
            address = this.props.moduleInfo.address.split(".").join(""),
            uid = order + address,
            Title = this.props.animated_level
                ? this.props.animated_level
                : "h3",
            animation_type = this.props.animation_type,
            slide_gap = parseInt(this.props.slide_gap),
            inAnimation = this.props.tilt_in,
            outAnimation = this.props.tilt_out,
            tilt_delay = this.props.tilt_delay,
            tilt_shuffle = this.props.tilt_shuffle.split("|"),
            tilt_reverse = this.props.tilt_reverse.split("|"),
            tilt_sync = this.props.tilt_sync.split("|"),
            slide = animation_type.split("-")[0],
            that = this;

        if ("tilt" === animation_type) {
            setTimeout(function() {
                $(`#dtq-animated-text-${uid} .dtq-animated-text-tilt`).textillate(
                    {
                        in: {
                            effect: inAnimation,
                            delayScale: 1.5,
                            delay: parseInt(tilt_delay),
                            sync: "on" === tilt_sync[0] ? true : false,
                            reverse: "on" === tilt_reverse[0] ? true : false,
                            shuffle: "on" === tilt_shuffle[0] ? true : false,
                        },
                        out: {
                            effect: outAnimation,
                            delayScale: 1.5,
                            delay: parseInt(tilt_delay),
                            sync: "on" === tilt_sync[1] ? true : false,
                            reverse: "on" === tilt_reverse[1] ? true : false,
                            shuffle: "on" === tilt_shuffle[1] ? true : false,
                        },
                        loop: true,
                    }
                );
            }, 400);
        } else {
            $(`#dtq-animated-text-${uid} .dtq-animated-text-tilt`).textillate(
                "stop"
            );
        }

        if ("slide" === slide) {
            setTimeout(function() {
                $(`#dtq-animated-text-${uid} .dtq-animated-text-slide`).addClass(
                    "slide-initialized"
                );
                $(
                    `#dtq-animated-text-${uid} .dtq-animated-text-slide li`
                ).removeClass("text-in");
                $(`#dtq-animated-text-${uid} .dtq-animated-text-slide li`)
                    .first()
                    .addClass("text-in");
                if (that._interval) {
                    clearInterval(that._interval);
                }
                that._interval = setInterval(() => {
                    let activeText = $(
                        `#dtq-animated-text-${uid} .dtq-animated-text-slide .text-in`
                    );
                    activeText.addClass("text-out");
                    activeText.removeClass("text-in");
                    if (activeText.next().is("li")) {
                        activeText.next().removeClass("text-out");
                        activeText.next().addClass("text-in");
                    } else {
                        $(`#dtq-animated-text-${uid} .dtq-animated-text-slide li`)
                            .first()
                            .addClass("text-in");
                        $(`#dtq-animated-text-${uid} .dtq-animated-text-slide li`)
                            .first()
                            .removeClass("text-out");
                    }
                }, slide_gap);
            }, 400);
        }

        return (
            <div
                className='dtq-module dtq-animated-text'
                id={`dtq-animated-text-${uid}`}
            >
                <Title className='dtq-animated-text-head'>
                    {this.render_prefix()}
                    {this.props.animation_type === "typed" &&
                        this.animation_typed()}
                    {this.props.animation_type === "tilt" &&
                        this.animation_tilt()}
                    {"slide" === slide && this.animation_slide()}
                    {this.render_suffix()}
                </Title>
            </div>
        );
    }
}

export default DTQ_Animated_Text;
