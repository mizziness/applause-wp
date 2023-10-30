import '../css/sections/widgets.scss';

let timeline = document.getElementById("applause-swiper-timeline");
if (timeline != null) {
  new Swiper(timeline.querySelector(".swiper"), {
    // Optional parameters
    createElements: true,
    spaceBetween: 16,
    slidesPerView: 2,
    slidesPerGroup: 2,
    centeredSlides: false,
    breakpoints: {
      585: {
        slidesPerView: 2,
        slidesPerGroup: 2,
        navigation: {
          enabled: true,
        },
      },
      900: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        navigation: {
          enabled: true,
        },
      },
      1165: {
        slidesPerView: 4,
        slidesPerGroup: 4,
        navigation: {
          enabled: true,
        },
      },
    },
    setWrapperSize: true,
    breakpointsBase: "window",
    watchSlidesProgress: true,
    loop: false,
    a11y: true,
    pagination: {
      enabled: true,
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true,
    },
    navigation: {
      enabled: true,
      hiddenClass: "tw-hidden",
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });
}
const wlbButton = document.querySelector("#content #life-at-applause .arrow-button-card");
if (wlbButton != null) {

  // wq is Wistia's window-level global, wistia-queue
  window._wq = window._wq || [];
  
  if (wlbButton != null ) {
    var videoID = wlbButton.getAttribute("data-href");

    if ( videoID == "null" || videoID.trim().length <= 0 ){
    } else {
      // Create the wrapper for the button
      var wistiaContainer = document.createElement('div');
      wistiaContainer.innerHTML =
        `<div class="wistia_embed wistia_async_${videoID} popover=true window.wistiaDisableMux=true popoverContent=html popoverDisableAutoPlay=true" 
          style="display:inline-block; white-space:nowrap;" id="wistia-${videoID}">
        </div>`;

      document.querySelector("body #app #content").appendChild(wistiaContainer);

      document.addEventListener("click", function(event){
        let selector = `#wistia-${videoID} .wistia_click_to_play`;
        let newWistiaButton = document.querySelector(selector);
        let checkParent = event.target.closest(".arrow-button-card") != null;

        if ( checkParent === true && newWistiaButton != null && videoID.length > 0 ) {
          newWistiaButton.click();
        }
      });
    }
  }
}
