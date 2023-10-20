// External Dependencies
import React, { Component } from 'react';

class WistiaEmbed extends Component {

  static slug = 'acm_wistia_embed';

  render() {
    const Image = this.props.module_image;
    const WistiaID = this.props.wistia_id;

    return (
    
        <div className={`wistia_embed wistia_async_-${WistiaID} popover=true window.wistiaDisableMux=true popoverContent=html wistia_embed_initialized`} itemID={`wistia-${WistiaID}`}>
            <a class='real-video-button video-play-button-careers' data-video={`${WistiaID}`} href='#/'>
                <svg class='play-button' width='100%' height='100%' viewBox='0 0 80 80' fill='none' xmlns='http://www.w3.org/2000/svg'><circle class='play-bg' cx='40' cy='40' r='40' fill='white' fill-opacity='0.8'/><path class='play-icon' d='M56 40L31.8712 53.8564L31.8712 26.1436L56 40Z' fill='#0272B4'/></svg>
                <div class='video-image tw-overflow-hidden tw-rounded-lg'>
                    <img src={`${Image}`} alt="Video Preview" />
                </div>
            </a>
        </div>

    );
  }
}

export default WistiaEmbed;
