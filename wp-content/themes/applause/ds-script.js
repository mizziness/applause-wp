// Custom JS goes here ------------

jQuery(function ($) {

  window._wq = window._wq || [];

  // Press Releases / News Mentions 
  // ==========================================================
  let pressReleases = $("#all-press-releases .type-press_releases");
  if ($(pressReleases).length > 0) {
    let years = [];

    $(pressReleases).each(function() {
      let article = $(this);
      let year = parseInt($(this).find(".entry-meta").text().split(",")[1].trim());
      
      $(article).attr("data-year", year);

      if ( years.indexOf( year ) === -1 ) {
        years.push( year );
      }
    });

    $(years).each(function() {
      let firstArticle = $("#all-press-releases .type-press_releases[data-year='" + this + "']").first();
      let newDivider = "<h2 class='news-divider-title as-h3'>" + this + "</h2>";
      $(firstArticle).before( newDivider );
    });
  }

  if ( $("body").hasClass("state-of-digital-quality-2022") ) {
    $("#sqd-header-hero-huge").append("<div id='sqd-hands'></div>");
  }

  var page = parseInt(load_more_params.current_page) + 1;
  var loading = false;
  var $loadMoreButton = $(".load-more-elements button");
  var $postContainer = $("#news-grid");

  $loadMoreButton.on("click", function (e) {
    e.preventDefault();

    if (!loading) {
      loading = true;

      // Display a loading indicator
      $loadMoreButton.addClass("loading");

      // AJAX request to fetch more posts
      $.ajax({
        url: load_more_params.ajax_url,
        type: "post",
        dataType: "html",
        data: {
          action: "news_mention_load_more_posts",
          page: page,
        },
        success: function (response) {
          if (response) {
            $postContainer.append(response);
            page++;
          }

          // Remove the loading indicator
          $loadMoreButton.removeClass("loading");
          loading = false;
        },
        error: function (xhr, textStatus, errorThrown) {
          console.log(xhr.statusText);
          // Handle the error condition if needed
        },
      });
    }
  });

  // FUNCTION - wait for element to exist
  var waitForEl = function (selector, callback) {
    if ($(selector).length) {
      callback();
    } else {
      setTimeout(function () {
        waitForEl(selector, callback);
      }, 100);
    }
  };

  // GRAVITY FORMS - MATERIAL DSIGN - ADD FOCUS TO LABEL
  // Run on GFORM RENDER
  $(document).on('gform_post_render', function(event, form_id, current_page){
    let gformInputs = $(".ginput_container input, .ginput_container select, .ginput_container textarea");

    $(gformInputs).each(function(){
      if ( $(this).val().length <= 0 ) {
        $(this).removeClass("focused");
        $(this).parents().siblings("label").removeClass("focused");
      } else {
        $(this).addClass("focused");
        $(this).parents().siblings("label").addClass("focused");
      }
    });

    // When focused in the field, always lift the label
    $(gformInputs).on("focus", function(event){
      $(event.currentTarget).addClass("focused");
      $(event.currentTarget).parent().siblings("label").addClass("focused");
    });

    // Nothing is in the field so move the label back
    $(gformInputs).on("blur", function(event){
      if ( $(event.currentTarget).val().length <= 0 ) {
        $(event.currentTarget).removeClass("focused");
        $(event.currentTarget).parent().siblings("label").removeClass("focused");
      }
    });

    $(".gform_validation_errors").addClass("screen-reader-text");
    $(".gfield_error .gfield_validation_message").addClass("screen-reader-text");

  });

  function windowPopup(url, width, height) {
    var left = screen.width / 2 - width / 2,
      top = screen.height / 2 - height / 2;

    window.open(
      url,
      "",
      "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=" +
        width +
        ",height=" +
        height +
        ",top=" +
        top +
        ",left=" +
        left
    );
  }
  var $shareSelector = $(".social-share-section button");
  $shareSelector.on("click", function (e) {
    e.preventDefault();
    if ($(this).hasClass("icon-share-link") == false) {
      windowPopup($(this).attr("data-url"), 500, 300);
    }
  });

  const copyLink = document.querySelectorAll(".icon-share-link");
  copyLink.forEach((link) => {
    link.addEventListener("click", function () {
      navigator.clipboard.writeText(link.getAttribute("data-href")).then(
        function () {
          link.classList.add("copied");
          window.setTimeout(function () {
            link.classList.remove("copied");
          }, 3000);
        },
        function () {
          link.classList.remove("copied");
        }
      );
    });
  });

  // Copy nav into mobile menu
  $("#mega-menu-dropdowns .mega-nav-dropdown").each(function () {
    let id = $(this).attr("id");
    let copyFrom = $(this).find(".et_pb_section .et_pb_row").clone(true);
    $(copyFrom).find(".dicm_custom_image_boxs").remove();
    $(copyFrom).addClass("inner-menu-applause");
    let matchingMenu = $("#mobile_menu").find("." + id);
    $(matchingMenu).append(copyFrom);
  });

  // Disable existing click events because they suck
  $("#mobile_menu li.menu-item a").off("click");
  $(".inner-menu-applause").hide();

  // Add our custom click events
  $("#mobile_menu li.menu-item a").on("click", function (e) {
    let innerMenu = $(this)
      .parents("li.menu-item")
      .find(".inner-menu-applause");
    let menuOpen = $(innerMenu).is(":visible");

    if (menuOpen == false) {
      $(innerMenu).slideDown("fast");
    } else {
      $(innerMenu).slideUp("fast");
    }
  });

// Sliders BEGIN
// ============================================================

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

  // const wlbButton = document.getElementById("life-at-applause-play");
  // if (wlbButton != null) {

  //   // wq is Wistia's window-level global, wistia-queue
  //   window._wq = window._wq || [];
    
  //   if (wlbButton != null ) {
  //     var videoID = wlbButton.querySelector("a").getAttribute("data-href");

  //     if ( videoID == "null" || videoID.trim().length <= 0 ){
  //     } else {
  //       // Create the wrapper for the button
  //       var wistiaContainer = document.createElement('div');
  //       wistiaContainer.innerHTML =
  //         `<div class="wistia_embed wistia_async_${videoID} popover=true window.wistiaDisableMux=true popoverContent=html popoverDisableAutoPlay=true" 
  //           style="display:inline-block; white-space:nowrap;" id="wistia-${videoID}">
  //         </div>`;

  //       document.querySelector("#main-content").appendChild(wistiaContainer);

  //       document.addEventListener("click", function(event){
  //         let selector = `#wistia-${videoID} .wistia_click_to_play`;
  //         let newWistiaButton = document.querySelector(selector);
  //         let checkParent = event.target.closest(".arrow-button-card") != null;

  //         if ( checkParent === true && newWistiaButton != null && videoID.length > 0 ) {
  //           newWistiaButton.click();
  //         }
  //       });
  //     }
  //   }
  // }

  // if ( $(".wistia-trigger").length > 0 ) {
  //   // wq is Wistia's window-level global, wistia-queue
    

  //   $(".wistia-trigger").each(function() {
  //     var wistia_id = $(this).attr("data-href") ?? "";

  //     if ( wistia_id.length <= 0 ) {
  //       console.error("Wistia ID is empty!");
  //     }

  //     $(this).on("click", function(){
  //       window._wq.push({ id: wistia_id, onReady: function(video) {
  //         console.log("I got a handle to the video!", video);
  //       }});
  //     })

  //   });

  // }




  // Top Navigation 
  // ============================================================
  const menuButtons = $("#top-menu li.menu-item").not(".get-started-button");
  
  $(menuButtons).each(function(){
    let currentMenu = $(this).attr("class").split(" ")[0];

    $(this).on("mouseenter", function(e){
      $("#mega-menu-dropdowns .et_pb_row").hide();
      let dropdown = $("#mega-menu-dropdowns").find("#" + currentMenu).find('.et_pb_row');
      $(dropdown).slideDown("fast");
    });

  });

  $("#mega-menu-dropdowns .et_pb_row").on("mouseleave", function(e){
    let currentMenu = $(this).closest('.mega-nav-dropdown').attr("id");

    if ( $(e.toElement).closest('li').hasClass(currentMenu) ) {
      e.preventDefault();
    } else {
      $(this).slideUp("fast");
    }
  })

  // Bottom Navigation 
  // ============================================================
  $("#footer-navigation-top .et_pb_text").each(function(){

    let pair = $(this).add( $(this).next(".et_pb_menu") );
    $(pair).wrapAll("<div class='menu-column-wrapper'></div>");
    
  });

  // Mobile Footer Nav Open/Close
  let footerHeaders = $("#mega-footer-top .menu-column-wrapper .footer-menu-header");

  $(footerHeaders).each(function () {
    let container = $(this).parents(".menu-column-wrapper");
    let menu = $(container).find(".et_pb_menu");

    $(container).attr("aria-expanded", false);
    
    $(this).on("click", function(){
      if ( $('body').outerWidth(true) > 900 ) {
        
        return false;

      } else {

        let expanded = $(container).attr("aria-expanded") == "false" ? false : true;

        if (expanded == false) {
          $(container).addClass("open");
          $(container).attr("aria-expanded", true);
          $(menu).slideDown("fast");
        } else {
          $(container).removeClass("open");
          $(container).attr("aria-expanded", false);
          $(menu).slideUp("fast");
        }

      }
    });
    
  });

  // Replace cookie link code
  let cookieTool = $("li.cookie-settings > a");
  let cookieLink = "<!-- OneTrust Cookies Settings button start --><button id='ot-sdk-btn' class='ot-sdk-show-settings under-link'>Cookie Settings</button><br /><!-- OneTrust Cookies Settings button end -->";

  if ( $(cookieTool).length > 0 ) {
    $(cookieTool).each(function(){
      if ( $(this).attr("href") == "#" ) {
        $(this).replaceWith( cookieLink );
      }
    });
  }

  // Support for Featured Post layout on the Blog index page
  $("#blog-index-featured #featured-posts .dp-dfg-item").each(function(){
    $(this).find('div').wrapAll('<div class="column"></div>');
    $(this).find('figure').wrapAll('<div class="column"></div>');
  });


  // add data attributes for custom click tracking
  // CTA buttons
  $(".et_pb_button").each(function(){
    $(this).addClass("click-track");
    $(this).attr('data-category', 'Page Navigation');
    $(this).attr('data-action', 'Button');
    $(this).attr('data-label', $(this).text());
  });
  // Main nav anchor tags
  $("#mega-menu-dropdowns a").each(function(){
    $(this).addClass("click-track");
    $(this).attr('data-category', 'Header');
    $(this).attr('data-action', $(this).parents('.mega-nav-dropdown').attr('id').replace('_',' '));
    $(this).attr('data-label', $(this).find('.link-title').text());
  });
  // same, but mobile nav
  $("#mobile_menu a.inner-link, #mobile_menu a.mini-title").each(function(){
    $(this).addClass("click-track");
    $(this).attr('data-category', 'Header');
    $(this).attr('data-action', $(this).parents('.menu-item').children('a').text());
    $(this).attr('data-label', $(this).find('.link-title').text());
  });
  // Main nav CTAs, just missing label
  $("#mega-menu-dropdowns .cta-link").each(function(){
    $(this).attr('data-label', 'CTA - ' + $(this).find('.cta-title').text());
  });
  // links in solutions menu, most of them don't use anchor tags
  $("#solutions-menu .solutions-link").each(function(){
    $(this).addClass("click-track");
    $(this).attr('data-category', 'Header');
    $(this).attr('data-action', 'our solutions');
    $(this).attr('data-label', $(this).find('.et_pb_text_inner').text());
  });
    // Main CTA/contact button
    $(".get-started-button a").each(function(){
      $(this).addClass("click-track");
      $(this).attr('data-category', 'Header');
      $(this).attr('data-action', 'Main CTA');
      $(this).attr('data-label', $(this).text());
    });
    // footer navigation
    $("#footer-navigation-top a").each(function(){
      $(this).addClass("click-track");
      $(this).attr('data-category', 'Footer');
      $(this).attr('data-action', $(this).parents('.menu-column-wrapper').find('.footer-menu-header p').text());
      $(this).attr('data-label', $(this).text());
    });
    // forms
    $(".applause-contact-forms .gform_button").each(function(){
      $(this).addClass("submit-track");
      $(this).attr('data-category', 'Form Submit');
      $(this).attr('data-action', 'Button');
      $(this).attr('data-label', window.location.href);
    });
  /* 
  Utility - Click Tracking
  
  Link format should appear as:
  <a class="click-track" href="" data-category="" data-action="" data-label="" name=""></a>
  */

  window.applause = window.applause || {};

  const trackClicksOn = document.querySelectorAll(".click-track");

  trackClicksOn.forEach(link => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      let category = event.currentTarget.getAttribute("data-category");
      let action = event.currentTarget.getAttribute("data-action");
      let label = event.currentTarget.getAttribute("data-label");

      window.applause.handleEventTracking(category, action, label, function(){
        if ( event.currentTarget.href ) {
          window.location.href = event.currentTarget.href;
        }
      });
    });
  });

  const trackSubmits = document.querySelectorAll(".submit-track");
  trackSubmits.forEach(button => {
    button.addEventListener("click", function (event) {
      let category = event.currentTarget.getAttribute("data-category");
      let action = event.currentTarget.getAttribute("data-action");
      let label = event.currentTarget.getAttribute("data-label");

      window.applause.handleEventTracking(category, action, label);
    });
  });

  /* Events & Tracking */
/*
  - handleEventTracking() should be applied to links and buttons for Google Analytics/Tracking
  - A list of Event Types and their corresponding category, action and label can be found here:
  - https://docs.google.com/spreadsheets/d/1rIyy57AgBs8pHaYoSfQDM1X7XlTOuTUD8-uaRbukqcw/edit#gid=1510182044
*/
window.applause.handleEventTracking = function(category, action, label, callback) {
  if (window.devMode == true) {
    console.log(`handleEventTracking detected: ${category}, ${action}, ${label}`);
  }
  window.dataLayer.push({ event: 'event_activity', action, category, label });

  if ( typeof callback == "function" ) {
    callback();
  }
}

});



// END Sliders 
// ============================================================

// var cdnUrl = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public'; // Replace with your CDN URL
// var images = document.querySelectorAll('.de-mega-menu img');
// for (var i = 0; i < images.length; i++) {
//     var image = images[i];
//     var imageUrl = image.getAttribute('src');
//     // Check if the image URL is from your WordPress site
//     if (imageUrl.indexOf('https://wordpress-12factor.herokuapp.com') !== -1) {
//         imageUrl = imageUrl.replace('https://wordpress-12factor.herokuapp.com', cdnUrl);
//         image.setAttribute('src', imageUrl);
//     }
// }

// Replace Image URL
// window.addEventListener('load', function() {
//     var cdnUrl = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public'; // Replace with your CDN URL

//     var images = document.querySelectorAll('.de-mega-menu img');

//     for (var i = 0; i < images.length; i++) {
//         var image = images[i];

//         var imageUrl = image.getAttribute('src');

//         // Check if the image URL is from your WordPress site
//         if (imageUrl.indexOf('https://wordpress-12factor.herokuapp.com') !== -1) {
//             imageUrl = imageUrl.replace('https://wordpress-12factor.herokuapp.com', cdnUrl);
//             image.setAttribute('src', imageUrl);
//         }
//     }

//     console.clear();
//     console.log("Divi Site is ready");
// } );

// console.clear();
// console.log("Divi Site is ready");
