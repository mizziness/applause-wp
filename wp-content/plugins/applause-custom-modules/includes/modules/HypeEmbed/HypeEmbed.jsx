// External Dependencies
import React, { Component } from 'react';

class HypeEmbed extends Component {

  static slug = 'acm_hype_embed';

  render() {
    const hype_archive = this.props.hype_archive;

    if ( !this.props.hype_archive ) {
      return '';
    }

    const MktoID = hype_archive.split("/").pop().split("-")[0];
    const newUrl = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public/wp-content/uploads/hype4/' + MktoID + '/Default/extracted.html';

    return (
        <div>
          { newUrl }
        </div>
    );
  }
}

export default HypeEmbed;
