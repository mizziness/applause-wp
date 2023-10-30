class MarkerClusterer{constructor(t,e,s){this.extend(MarkerClusterer,google.maps.OverlayView),this.map_=t,this.MARKER_CLUSTER_IMAGE_PATH_="../images/m",this.MARKER_CLUSTER_IMAGE_EXTENSION_="png",this.markers_=[],this.clusters_=[],this.sizes=[53,56,66,78,90],this.styles_=[],this.ready_=!1;var i=s||{};this.zIndex_=i.zIndex||google.maps.Marker.MAX_ZINDEX+1,this.gridSize_=i.gridSize||60,this.minClusterSize_=i.minimumClusterSize||2,this.maxZoom_=i.maxZoom||null,this.styles_=i.styles||[],this.imagePath_=i.imagePath||this.MARKER_CLUSTER_IMAGE_PATH_,this.imageExtension_=i.imageExtension||this.MARKER_CLUSTER_IMAGE_EXTENSION_,this.zoomOnClick_=!0,null!=i.zoomOnClick&&(this.zoomOnClick_=i.zoomOnClick),this.averageCenter_=!1,null!=i.averageCenter&&(this.averageCenter_=i.averageCenter),this.setupStyles_(),this.setMap(t),this.prevZoom_=this.map_.getZoom();var r=this;google.maps.event.addListener(this.map_,"zoom_changed",function(){var t=r.map_.getZoom(),e=r.map_.minZoom||0,s=Math.min(r.map_.maxZoom||100,r.map_.mapTypes[r.map_.getMapTypeId()].maxZoom);t=Math.min(Math.max(t,e),s),r.prevZoom_!=t&&(r.prevZoom_=t,r.resetViewport())}),google.maps.event.addListener(this.map_,"idle",function(){r.redraw()}),e&&(e.length||Object.keys(e).length)&&this.addMarkers(e,!1)}extend(t,e){return function(t){for(var e in t.prototype)this.prototype[e]=t.prototype[e];return this}.apply(t,[e])}onAdd(){this.setReady_(!0)}draw(){}setupStyles_(){if(!this.styles_.length)for(var t,e=0;t=this.sizes[e];e++)this.styles_.push({url:this.imagePath_+(e+1)+"."+this.imageExtension_,height:t,width:t})}fitMapToMarkers(){for(var t,e=this.getMarkers(),s=new google.maps.LatLngBounds,i=0;t=e[i];i++)s.extend(t.getPosition());this.map_.fitBounds(s)}setZIndex(t){this.zIndex_=t}getZIndex(){return this.zIndex_}setStyles(t){this.styles_=t}getStyles(){return this.styles_}isZoomOnClick(){return this.zoomOnClick_}isAverageCenter(){return this.averageCenter_}getMarkers(){return this.markers_}getTotalMarkers(){return this.markers_.length}setMaxZoom(t){this.maxZoom_=t}getMaxZoom(){return this.maxZoom_}calculator_(t,e){for(var s=0,i=t.length,r=i;0!==r;)r=parseInt(r/10,10),s++;return{text:i,index:s=Math.min(s,e)}}setCalculator(t){this.calculator_=t}getCalculator(){return this.calculator_}addMarkers(t,e){if(t.length)for(let e,s=0;e=t[s];s++)this.pushMarkerTo_(e);else if(Object.keys(t).length)for(let e in t)this.pushMarkerTo_(t[e]);e||this.redraw()}pushMarkerTo_(t){if(t.isAdded=!1,t.draggable){var e=this;google.maps.event.addListener(t,"dragend",function(){t.isAdded=!1,e.repaint()})}this.markers_.push(t)}addMarker(t,e){this.pushMarkerTo_(t),e||this.redraw()}removeMarker_(t){var e=-1;if(this.markers_.indexOf)e=this.markers_.indexOf(t);else for(var s,i=0;s=this.markers_[i];i++)if(s==t){e=i;break}return-1!=e&&(t.setMap(null),this.markers_.splice(e,1),!0)}removeMarker(t,e){var s=this.removeMarker_(t);return!(e||!s)&&(this.resetViewport(),this.redraw(),!0)}removeMarkers(t,e){for(var s,i=t===this.getMarkers()?t.slice():t,r=!1,h=0;s=i[h];h++){var a=this.removeMarker_(s);r=r||a}if(!e&&r)return this.resetViewport(),this.redraw(),!0}setReady_(t){this.ready_||(this.ready_=t,this.createClusters_())}getTotalClusters(){return this.clusters_.length}getMap(){return this.map_}setMap(t){this.map_=t}getGridSize(){return this.gridSize_}setGridSize(t){this.gridSize_=t}getMinClusterSize(){return this.minClusterSize_}setMinClusterSize(t){this.minClusterSize_=t}getExtendedBounds(t){var e=this.getProjection(),s=new google.maps.LatLng(t.getNorthEast().lat(),t.getNorthEast().lng()),i=new google.maps.LatLng(t.getSouthWest().lat(),t.getSouthWest().lng()),r=e.fromLatLngToDivPixel(s);r.x+=this.gridSize_,r.y-=this.gridSize_;var h=e.fromLatLngToDivPixel(i);h.x-=this.gridSize_,h.y+=this.gridSize_;var a=e.fromDivPixelToLatLng(r),n=e.fromDivPixelToLatLng(h);return t.extend(a),t.extend(n),t}isMarkerInBounds_(t,e){return e.contains(t.getPosition())}clearMarkers(){this.resetViewport(!0),this.markers_=[]}resetViewport(t){for(let t,e=0;t=this.clusters_[e];e++)t.remove();for(let e,s=0;e=this.markers_[s];s++)e.isAdded=!1,t&&e.setMap(null);this.clusters_=[]}repaint(){var t=this.clusters_.slice();this.clusters_.length=0,this.resetViewport(),this.redraw(),setTimeout(function(){for(var e,s=0;e=t[s];s++)e.remove()},0)}redraw(){this.createClusters_()}distanceBetweenPoints_(t,e){if(!t||!e)return 0;var s=(e.lat()-t.lat())*Math.PI/180,i=(e.lng()-t.lng())*Math.PI/180,r=Math.sin(s/2)*Math.sin(s/2)+Math.cos(t.lat()*Math.PI/180)*Math.cos(e.lat()*Math.PI/180)*Math.sin(i/2)*Math.sin(i/2);return 6371*(2*Math.atan2(Math.sqrt(r),Math.sqrt(1-r)))}addToClosestCluster_(t){for(var e,s=4e4,i=null,r=0;e=this.clusters_[r];r++){var h=e.getCenter();if(h){var a=this.distanceBetweenPoints_(h,t.getPosition());a<s&&(s=a,i=e)}}if(i&&i.isMarkerInClusterBounds(t))i.addMarker(t);else{var n=new Cluster(this);n.addMarker(t),this.clusters_.push(n)}}createClusters_(){if(this.ready_)for(var t,e=new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(),this.map_.getBounds().getNorthEast()),s=this.getExtendedBounds(e),i=0;t=this.markers_[i];i++)!t.isAdded&&this.isMarkerInBounds_(t,s)&&this.addToClosestCluster_(t)}}class Cluster{constructor(t){this.markerClusterer_=t,this.map_=t.getMap(),this.gridSize_=t.getGridSize(),this.minClusterSize_=t.getMinClusterSize(),this.averageCenter_=t.isAverageCenter(),this.center_=null,this.markers_=[],this.bounds_=null,this.clusterIcon_=new ClusterIcon(this,t.getStyles(),t.getGridSize())}isMarkerAlreadyAdded(t){if(this.markers_.indexOf)return-1!=this.markers_.indexOf(t);for(var e,s=0;e=this.markers_[s];s++)if(e==t)return!0;return!1}addMarker(t){if(this.isMarkerAlreadyAdded(t))return!1;if(this.center_){if(this.averageCenter_){var e=this.markers_.length+1,s=(this.center_.lat()*(e-1)+t.getPosition().lat())/e,i=(this.center_.lng()*(e-1)+t.getPosition().lng())/e;this.center_=new google.maps.LatLng(s,i),this.calculateBounds_()}}else this.center_=t.getPosition(),this.calculateBounds_();t.isAdded=!0,this.markers_.push(t);var r=this.markers_.length;if(r<this.minClusterSize_&&t.getMap()!=this.map_&&t.setMap(this.map_),r==this.minClusterSize_)for(var h=0;h<r;h++)this.markers_[h].setMap(null);return r>=this.minClusterSize_&&t.setMap(null),this.updateIcon(),!0}getMarkerClusterer(){return this.markerClusterer_}getBounds(){for(var t,e=new google.maps.LatLngBounds(this.center_,this.center_),s=this.getMarkers(),i=0;t=s[i];i++)e.extend(t.getPosition());return e}remove(){this.clusterIcon_.remove(),this.markers_.length=0,delete this.markers_}getSize(){return this.markers_.length}getMarkers(){return this.markers_}getCenter(){return this.center_}calculateBounds_(){var t=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(t)}isMarkerInClusterBounds(t){return this.bounds_.contains(t.getPosition())}getMap(){return this.map_}updateIcon(){var t=this.map_.getZoom(),e=this.markerClusterer_.getMaxZoom();if(e&&t>e)for(var s,i=0;s=this.markers_[i];i++)s.setMap(this.map_);else if(this.markers_.length<this.minClusterSize_)this.clusterIcon_.hide();else{var r=this.markerClusterer_.getStyles().length,h=this.markerClusterer_.getCalculator()(this.markers_,r);this.clusterIcon_.setCenter(this.center_),this.clusterIcon_.setSums(h),this.clusterIcon_.show()}}}class ClusterIcon{constructor(t,e,s){t.getMarkerClusterer().extend(ClusterIcon,google.maps.OverlayView),this.styles_=e,this.padding_=s||0,this.cluster_=t,this.center_=null,this.map_=t.getMap(),this.div_=null,this.sums_=null,this.visible_=!1,this.setMap(this.map_)}triggerClusterClick(){var t=this.cluster_.getBounds(),e=this.cluster_.getMarkerClusterer();google.maps.event.trigger(e.map_,"clusterclick",this.cluster_),e.isZoomOnClick()&&(this.map_.fitBounds(t),this.map_.setCenter(t.getCenter()))}onAdd(){if(this.div_=document.createElement("DIV"),this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.innerHTML=this.sums_.text}this.getPanes().overlayMouseTarget.appendChild(this.div_);var e=this;google.maps.event.addDomListener(this.div_,"click",function(){e.triggerClusterClick()})}getPosFromLatLng_(t){var e=this.getProjection().fromLatLngToDivPixel(t);return e.x-=parseInt(this.width_/2,10),e.y-=parseInt(this.height_/2,10),e}draw(){if(this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.top=t.y+"px",this.div_.style.left=t.x+"px"}}hide(){this.div_&&(this.div_.style.display="none"),this.visible_=!1}show(){if(this.div_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.style.display=""}this.visible_=!0}remove(){this.setMap(null)}onRemove(){this.div_&&this.div_.parentNode&&(this.hide(),this.div_.parentNode.removeChild(this.div_),this.div_=null)}setSums(t){this.sums_=t,this.text_=t.text,this.index_=t.index,this.div_&&(this.div_.innerHTML=t.text),this.useStyle()}useStyle(){var t=Math.max(0,this.sums_.index-1);t=Math.min(this.styles_.length-1,t);var e=this.styles_[t];this.url_=e.url,this.height_=e.height,this.width_=e.width,this.textColor_=e.textColor,this.anchor_=e.anchor,this.textSize_=e.textSize,this.backgroundPosition_=e.backgroundPosition}setCenter(t){this.center_=t}createCss(t){var e=[];e.push("z-index:"+this.cluster_.markerClusterer_.getZIndex()+";"),e.push("background-image:url("+this.url_+");");var s=this.backgroundPosition_?this.backgroundPosition_:"0 0";e.push("background-position:"+s+";"),"object"==typeof this.anchor_?("number"==typeof this.anchor_[0]&&this.anchor_[0]>0&&this.anchor_[0]<this.height_?e.push("height:"+(this.height_-this.anchor_[0])+"px; padding-top:"+this.anchor_[0]+"px;"):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px;"),"number"==typeof this.anchor_[1]&&this.anchor_[1]>0&&this.anchor_[1]<this.width_?e.push("width:"+(this.width_-this.anchor_[1])+"px; padding-left:"+this.anchor_[1]+"px;"):e.push("width:"+this.width_+"px; text-align:center;")):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px; width:"+this.width_+"px; text-align:center;");var i=this.textColor_?this.textColor_:"black",r=this.textSize_?this.textSize_:11;return e.push("cursor:pointer; top:"+t.y+"px; left:"+t.x+"px; color:"+i+"; position:absolute; font-size:"+r+"px; font-family:Arial,sans-serif; font-weight:bold"),e.join("")}}