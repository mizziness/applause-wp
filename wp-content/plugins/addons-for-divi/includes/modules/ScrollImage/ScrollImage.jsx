// External Dependencies
import $ from 'jquery';
import React, { Component } from "react";
import { findDOMNode } from 'react-dom';
import { renderFontStyle, _getCustomBgCss } from "../ModulesCore/ModulesCore";
// Internal Dependencies
import "./style.css";

class DTQ_Scroll_Image extends Component {

    static slug = "ba_scroll_image";

    static css( props ) {

        let additionalCss                = [],
            show_icon                    = props.show_icon,
            use_image                    = props.use_image,
            use_icon_anim                = props.use_icon_anim,
            icon_size                    = props.icon_size,
            icon_color                   = props.icon_color,
            scroll_speed                 = props.scroll_speed,
            scroll_type                  = props.scroll_type,
            scroll_dir_scroll            = props.scroll_dir_scroll,
            scroll_dir_hover             = props.scroll_dir_hover,
            img_height                   = props.img_height,
            img_height_tablet            = props.img_height_tablet,
            img_height_phone             = props.img_height_phone,
            img_height_last_edited       = props.img_height_last_edited,
            img_height_responsive_status = img_height_last_edited && img_height_last_edited.startsWith("on");

        if( use_icon_anim === 'on' ) {
            let anim_dir = '';
            if( scroll_type === 'on_scroll' ) {
                if( scroll_dir_scroll === 'vertical' ) {
                    anim_dir = "Y";
                } else {
                    anim_dir = "X";
                }
            } else {
                anim_dir = scroll_dir_hover[0];
            }

            additionalCss.push([{
                selector: "%%order_class%% .dtq-scroll-image-icon-el",
                declaration: `
                    animation-name: dtq-scroll-${anim_dir};
                    animation-duration: .5s;
                    animation-iteration-count: infinite;
                    animation-direction: alternate;
                    animation-timing-function: ease-in-out;
                    `
            }] );
        }

        // Icon
        if( show_icon === 'on' ) {
            if( use_image === 'off' ) {
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image-icon-el",
                    declaration: `font-size: ${icon_size};color:${icon_color};`
                }] );
            } else {
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image-icon img",
                    declaration: `width: ${icon_size};`
                }] );
            }
        }

        //Scroll
        if( scroll_type === 'on_scroll' ) {

            if( scroll_dir_scroll === 'vertical' ) {
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image",
                    declaration: `overflow-y: auto;overflow-x:hidden;`
                }] );
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                    declaration: `max-width: 100%;width: 100%;`
                }] );
            } else {
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image",
                    declaration: `overflow-y:hidden;overflow-x: auto;`
                }] );
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                    declaration: `height: 100%; max-width: none;width: auto;`
                }] );
                additionalCss.push([{
                    selector: "%%order_class%% .scroll-figure-wrap",
                    declaration: `height: 100%;width: 100%;`
                }] );
            }
        }
        else if( scroll_type === 'on_hover' ) {

            additionalCss.push([{
                selector: "%%order_class%% .scroll-figure-wrap",
                declaration: `height:100%;width:100%;`
            }] );

            additionalCss.push([{
                selector: "%%order_class%% .dtq-scroll-image",
                declaration: `overflow: hidden;`
            }] );

            additionalCss.push([{
                selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                declaration: `
                    position: absolute;
                    transition: ${scroll_speed};`
            }] );


            if( scroll_dir_hover === 'X_ltr' || scroll_dir_hover === 'X_rtl' ) {
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                    declaration: `height: 100%; max-width: none;width: auto;top:0;`
                }] );


                if( scroll_dir_hover === 'X_ltr' ) {
                    additionalCss.push([{
                        selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                        declaration: `right:0;`
                    }] );
                } else if( scroll_dir_hover === 'X_rtl' ) {
                    additionalCss.push([{
                        selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                        declaration: `left:0;`
                    }] );
                }

            } else {
                additionalCss.push([{
                    selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                    declaration: `max-width: 100%;width: 100%; left:0;`
                }] );

                if( scroll_dir_hover === 'Y_ttb' ) {
                    additionalCss.push([{
                        selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                        declaration: `bottom:0;`
                    }] );
                } else if( scroll_dir_hover === 'Y_btt' ) {
                    additionalCss.push([{
                        selector: "%%order_class%% .dtq-scroll-image .dtq-scroll-image-el",
                        declaration: `top:0;`
                    }] );
                }
            }
        }

        // image height
        additionalCss.push([{
            selector: "%%order_class%% .dtq-scroll-image",
            declaration: `height: ${img_height};`
        }] );

        if( img_height_tablet && img_height_responsive_status ) {
            additionalCss.push( [{
                selector: "%%order_class%% .dtq-scroll-image",
                device: "tablet",
                declaration: `height: ${img_height_tablet};`
            }] );
        }

        if( img_height_phone && img_height_responsive_status ) {
            additionalCss.push( [{
                selector: "%%order_class%% .dtq-scroll-image",
                device: "phone",
                declaration: `wiheightdth: ${img_height_phone};`
            }] );
        }

        // overlay bg
        let overlay_bg = _getCustomBgCss( props, 'overlay', "%%order_class%% .dtq-scroll-image-overlay", '' );

        let iconStyle = renderFontStyle(
            props,
            "icon",
            "%%order_class%% .dtq-scroll-image-icon-el"
        );

        return additionalCss.concat( overlay_bg ).concat( iconStyle );
    }


    render_overlay = () => {
        if( this.props.use_overlay === 'on' ) {
            return(
                <div className="dtq-scroll-image-overlay"></div>
            )
        }
    }

    render_icon = () => {
        // return if icon visibility is off
        if( this.props.show_icon === 'off' ) return;

        if( this.props.use_image === 'on' ) {
            return(
                <div className="dtq-scroll-image-icon">
                    <div className="dtq-scroll-image-icon-el">
                        <img src={this.props.icon_image} alt=""/>
                    </div>
                </div>
            )
        } else {
            const utils = window.ET_Builder.API.Utils;
            const _icon = this.props.icon ? utils.processFontIcon(this.props.icon) : "";
            return(
                <div className="dtq-scroll-image-icon dtq-et-font-icon">
                    <div className="dtq-scroll-image-icon-el">
                        {_icon}
                    </div>
                </div>
            )
        }
    }

    componentDidUpdate () {

        const _scroll = findDOMNode(this.refs.scroll);
        if (!_scroll) return;

        let scroll         = $(_scroll),
            direction      = this.props.scroll_dir_hover,
            type           = this.props.scroll_type,
            scroll_image   = scroll.find('img'),
            scroll_overlay = scroll.find(".dtq-scroll-image-overlay"),
            operator       = '',
            translate      = '',
            offset         = 0;

        if(type === "on_hover") {
            if( direction[0] === 'X' ) {
                translate = "translateX";
                offset = scroll_image.width()  - scroll.width();
            }
            else if( direction[0] === 'Y' ) {
                translate = "translateY";
                offset =  scroll_image.height() - scroll.height();
            }

            if( parseInt( offset ) < 1 ) {
                offset = 0;
            }

            if( direction === 'X_rtl' ||  direction === 'Y_btt' ) {
                operator = '-';
            }

            scroll.on( 'mouseenter', function() {
            	scroll_image.css("transform", translate + "(" + operator + offset + "px)");
            });

            scroll.on( 'mouseleave', function() {
            	scroll_image.css("transform", translate + "(0px)");
            });

        } else {
            scroll.off("mouseenter");
            if( this.props.scroll_dir_scroll === "horizontal" ) {
                scroll_overlay.css({
                    "width": scroll_image.width(),
                    "height": scroll_image.height()
                });
            } else {
                scroll_overlay.css({
                    "width": '100%',
                    "height": '100%'
                });
            }
        }
    }

    render() {
        let image            = this.props.image,
            image_alt        = this.props.image_alt;

        if( this.props.image ) {
            return (
            <div className="dtq-module dtq-scroll-image" ref="scroll">
                    { this.render_icon() }
                    <div className="scroll-figure-wrap">
                        { this.render_overlay() }
                        <img
                            ref={el => (this.figure = el)}
                            className="dtq-scroll-image-el"
                            src={image}
                            alt={image_alt}
                        />
                    </div>
            </div>
            );
        }
        else {
            return (
                <div className="dtq-module dtq-scroll-image"></div>
            );
        }
    }
}

export default DTQ_Scroll_Image;
