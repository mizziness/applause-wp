// Custom JS goes here ------------

jQuery(function ($) {
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
  $("form input, form textarea, form select").focusin(function () {
    $(this).parent().siblings("label").addClass("focused");
  });

  $("form input, form textarea, form select").focusout(function () {
    var input = $(this);
    if (input.val().length === 0) {
      input.parent().siblings("label").removeClass("focused");
    }
  });

  // on ajax submission
  $("#gform_ajax_frame_1, #gform_ajax_frame_2, #gform_ajax_frame_3").on(
    "load",
    function () {
      $("form input, form textarea, form select").focusin(function () {
        $(this).parent().siblings("label").addClass("focused");
      });
      $("form input, form textarea, form select").focusout(function () {
        var input = $(this);
        if (input.val().length === 0) {
          input.parent().siblings("label").removeClass("focused");
        }
      });
      $("form input, form textarea, form select").each(function () {
        if ($(this).val().length != 0) {
          $(this).parent().siblings("label").addClass("focused");
        }
      });
    }
  );

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

  // Mobile Footer Nav Open/Close
  let footerHeaders = $(".footer-widget-custom-grid .title");
  $(footerHeaders).each(function () {
    let container = $(this).parents(".fwidget.et_pb_widget.widget_nav_menu");
    $(container).attr("aria-expanded", false);
    $(container).on("click", function () {
      let expanded =
        $(container).attr("aria-expanded") == "false" ? false : true;
      if (expanded == false) {
        $(container).find(".menu").stop().slideDown("fast");
        $(container).attr("aria-expanded", true);
        $(container).addClass("open");
      } else {
        $(container).find(".menu").stop().slideUp("fast");
        $(container).attr("aria-expanded", false);
        $(container).removeClass("open");
      }
    });
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
  $("#et-main-area .de-mega-menu-container .de-mega-menu").each(function () {
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

  const wlbButton = document.getElementById("life-at-applause-play");
  if (wlbButton != null) {

    // wq is Wistia's window-level global, wistia-queue
    window._wq = window._wq || [];
    
    if (wlbButton != null ) {
      var videoID = wlbButton.querySelector("a").getAttribute("data-href");

      if ( videoID == "null" || videoID.trim().length <= 0 ){
      } else {
        // Create the wrapper for the button
        var wistiaContainer = document.createElement('div');
        wistiaContainer.innerHTML =
          `<div class="wistia_embed wistia_async_${videoID} popover=true window.wistiaDisableMux=true popoverContent=html popoverDisableAutoPlay=true" 
            style="display:inline-block; white-space:nowrap;" id="wistia-${videoID}">
          </div>`;

        document.querySelector("#main-content").appendChild(wistiaContainer);

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
