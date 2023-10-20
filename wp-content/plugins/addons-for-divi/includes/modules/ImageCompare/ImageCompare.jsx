import $ from "jquery";
import React, { Component } from "react";
import "../../../assets/js/twentytwenty.min.js";
import { get_responsive_styles } from "../ModulesCore/ModulesCore";
import "./style.css";

class DTQ_Image_Compare extends Component {
    static slug = "ba_image_compare";

    static css(props) {
        const additionalCss = [];
        let label_height = [];
        let label_width = [];
        let arrow_color = '';
        //handle_color

        if ("handle-1" === props.handle_style) {
            arrow_color = props.handle_color;
        } else if ("handle-2" === props.handle_style) {
            arrow_color = props.arrow_color;
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .dtq-image-compare .twentytwenty-handle",
                    declaration: `background-color: ${props.handle_color};`
                }
            ]);

        }

        additionalCss.push([
            {
                selector: `%%order_class%% .dtq-image-compare .twentytwenty-horizontal .twentytwenty-handle:before,
            %%order_class%% .dtq-image-compare .twentytwenty-horizontal .twentytwenty-handle:after,
            %%order_class%% .dtq-image-compare .twentytwenty-vertical .twentytwenty-handle:before,
            %%order_class%% .dtq-image-compare .twentytwenty-vertical .twentytwenty-handle:after`,
                declaration: `background: ${props.handle_color};`
            }
        ]);

        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-image-compare .twentytwenty-handle",
                declaration: `border: 3px solid ${props.handle_color};`
            }
        ]);


        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-image-compare .twentytwenty-right-arrow",
                declaration: `border-left: 6px solid ${arrow_color};`
            }
        ]);

        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-image-compare .twentytwenty-left-arrow",
                declaration: `border-right: 6px solid ${arrow_color};`
            }
        ]);

        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-image-compare .twentytwenty-down-arrow",
                declaration: `border-top: 6px solid ${arrow_color};`
            }
        ]);

        additionalCss.push([
            {
                selector:
                    "%%order_class%% .dtq-image-compare .twentytwenty-up-arrow",
                declaration: `border-bottom: 6px solid ${arrow_color};`
            }
        ]);

        // handle_color end

        // Label
        if ("initial" !== props.label_height) {
            label_height = get_responsive_styles(
                props,
                "label_height",
                "%%order_class%% .twentytwenty-overlay div:before",
                { primary: "height", important: false },
                { default: "initial" }
            );
        }

        if ("initial" !== props.label_width) {
            label_width = get_responsive_styles(
                props,
                "label_width",
                "%%order_class%% .twentytwenty-overlay div:before",
                { primary: "width", important: false },
                { default: "initial" }
            );
        }

        let label_padding = get_responsive_styles(
            props,
            "label_padding",
            "%%order_class%% .twentytwenty-overlay div:before",
            { primary: "padding", important: false },
            { default: "5px|20px|5px|20px" }
        );

        if (props.show_label === "on_hover") {
            additionalCss.push([
                {
                    selector:
                        "%%order_class%% .twentytwenty-before-label, %%order_class%% .twentytwenty-after-label",
                    declaration: `opacity:0;`
                }
            ]);
            additionalCss.push([
                {
                    selector:
                        "%%order_class%%:hover .twentytwenty-before-label, %%order_class%%:hover .twentytwenty-after-label",
                    declaration: `opacity:1;`
                }
            ]);
        }

        additionalCss.push([
            {
                selector: "%%order_class%% .twentytwenty-before-label:before",
                declaration: `background-color: ${props.before_label_bg}`
            }
        ]);

        additionalCss.push([
            {
                selector: "%%order_class%% .twentytwenty-after-label:before",
                declaration: `background-color: ${props.after_label_bg}`
            }
        ]);

        return additionalCss
            .concat(label_padding)
            .concat(label_width)
            .concat(label_height);
    }

    componentDidMount() {
        this._initImageCompare(this.props, 1000);
    }

    componentDidUpdate(nextProps) {
        if (
            nextProps.before_img !== this.props.before_img ||
            nextProps.before_label !== this.props.before_label ||
            nextProps.after_img !== this.props.after_img ||
            nextProps.after_label !== this.props.after_label ||
            nextProps.orientation !== this.props.orientation ||
            nextProps.offset_pct !== this.props.offset_pct ||
            nextProps.move_on_hover !== this.props.move_on_hover ||
            nextProps.overlay !== this.props.overlay ||
            nextProps.show_label !== this.props.show_label
        ) {
            this._initImageCompare(this.props, 300);
        }
    }

    _initImageCompare(props, time) {
        let move_on_hover = "on" === props.move_on_hover ? true : false,
            order = props.moduleInfo.order,
            address = props.moduleInfo.address.split(".").join(""),
            uid = order + address,
            parent = $(".dtq-image-compare-container-" + uid);
        if (parent.hasClass("twentytwenty-container")) {
            parent.unwrap();
            parent.find(".twentytwenty-overlay").remove();
            parent.find(".twentytwenty-handle").remove();
            parent.removeClass("twentytwenty-container");
        }

        setTimeout(function() {
            if (parent.parent().hasClass("twentytwenty-wrapper")) {
                parent.unwrap();
                parent.find(".twentytwenty-overlay").remove();
                parent.find(".twentytwenty-handle").remove();
                parent.removeClass("twentytwenty-container");
            }
            parent.twentytwenty({
                default_offset_pct: props.offset_pct,
                move_slider_on_hover: move_on_hover,
                orientation: props.orientation,
                before_label: props.before_label,
                after_label: props.after_label,
                no_overlay: props.overlay === "on" ? false : true
            });
        }, time);
    }

    render() {
        let props = this.props,
            order = props.moduleInfo.order,
            address = props.moduleInfo.address.split(".").join(""),
            uid = order + address;

        return (
            <div className={`dtq-image-compare ${props.handle_style}`}>
                <div className={"dtq-image-compare-container-" + uid}>
                    <div
                        dangerouslySetInnerHTML={{ __html: props.__compare }}
                    />
                </div>
            </div>
        );
    }
}

export default DTQ_Image_Compare;
