// External Dependencies
import React, { Component } from 'react';

// Internal Dependencies
import './style.css';

class HypeEmbed extends Component {

  static slug = '__acm_hype_embed';

  render() {
    const HypeArchive = this.props.hype_archive;

    return (
        {HypeArchive}
    );
  }
}

export default HypeEmbed;