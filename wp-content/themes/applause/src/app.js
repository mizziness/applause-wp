import 'core-js/stable';
import 'regenerator-runtime/runtime';
import './app.scss';

let timelineSlider      = document.getElementById("applause-swiper-timeline");

if ( timelineSlider != null || locationSlider != null ) {
    import('./js/sliders');
}

// var bodyTag = document.getElementsByTagName("body")[0];
// if ( isset(bodyTag) && bodyTag.classList.contains("home") ) {
//     console.log("here");
//     import('./css/pages/home.scss');
// }

if (module.hot) {
    module.hot.accept();
};
