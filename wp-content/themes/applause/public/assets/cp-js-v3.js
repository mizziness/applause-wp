$(function () {
  if ( $('form#main-form .action-input').val() == 'elements/save' ) {

    var maxUrlLength = 73;
    var domainLength = window.location.hostname.length;
    var $slugInput = $('#slug');
    var showAlert = false;
    // TODO: Get the full path length of the live url to count, remembering that different sites will be different (eg /fr/le-blog or /blog). This will likely need to be via Twig
    var pathLength = 0;

    function checkSlugLength(el) {
      var slugLength = $(el).val().length;
      var remainingChars = maxUrlLength - domainLength - pathLength - slugLength;
      var textColor = '';
      switch (true) {
        case (remainingChars < 0):
          textColor = 'red';
          showAlert = true;
        break;
        case (remainingChars < 10):
          textColor = 'darkorange';
          showAlert = false;
          break;
        default:
          showAlert = false;
        break;
      }
      $('#remaining-slug-chars').remove();
      $('#slug-label').append(`<span style='margin-left:.5em; color:` + textColor + `' id='remaining-slug-chars'>(` + remainingChars + `)</span>`);
    }

    $($slugInput).on("change", function() {
      checkSlugLength($(this));
    });
    checkSlugLength($slugInput);

    $('#main-form').on('submit', function(e){
      if (showAlert) {
        if (!confirm('The full URL for this entry will be over the recommended 75 characters; you should consider shortening the title/slug. Continue saving this entry?')) {
          e.preventDefault();
        }
      }
    });

    if ( $("#fields-blogReadTime").length > 0 ) {
      // On a blog page that needs an updated reading time.

      // Insert a button
      let button = '<button id="calulateReadTime" type="button" class="btn caution">Calculate</button>';
      $("#fields-blogReadTime").parents(".flex").append(button);

      $("#calulateReadTime").on("click", function(){
        $("#fields-blogReadTime").val();
        let content = "";
        let getElements = $("textarea[id*=blogArticleText]");
        
        Array.from(getElements).forEach(function(element){
          content += element.value;
        });

        let wordCount = content.replace( /[^\w ]/g, "" ).split( /\s+/ ).length;
        var readingTime = Math.floor(wordCount / 200) + 1;
        $("#fields-blogReadTime").val(readingTime);
      });
    };
  } // endif entry

  var isHebrew = false;

  if (window.location.href.indexOf("hebrewMicrosite") >= 0) {
      isHebrew = true;
  };
      
  if ($("#entryType option:selected").text().toLowerCase().includes("hebrew")) {
      isHebrew = true;
  };

  if (isHebrew) {
    $("body").attr("dir", "rtl");
    $(".ltr").removeClass("ltr").addClass("rtl").css("direction","rtl");
    
	  // timeout is required to avoid js error in cpanel
    setTimeout(function(){
  
        $('[data-type="craft\\\\redactor\\\\Field"] textarea').each(function() {
            $R($(this).get(0), 'destroy');
            $R($(this).get(0), { direction: "rtl", buttons: ["html","bold", "italic", "link"], linkNewTab: true });
        });

  		$('.redactor-box').parent('.input').addClass("rtl");
    }, 500); 
  }; // endif Hebrew

});

let layoutField = document.getElementById("fields-pageHeroStyle-field");
if ( layoutField != null ) {
  const responsiveHero = document.getElementById("fields-responsiveHero-field");
  const heroLeftText = document.getElementById("fields-heroLeftSideText-field");
  const heroVideoCentered = document.getElementById("fields-heroVideoCentered-field");
  const heroImageOnly = document.getElementById("fields-heroImageOnly-field");
  const heroImageGrid = document.getElementById("fields-heroImageGrid-field");

  responsiveHero.classList.add("tw-hidden", "is-disabled");
  heroLeftText.classList.add("tw-hidden", "is-disabled");
  heroVideoCentered.classList.add("tw-hidden", "is-disabled");
  heroImageOnly.classList.add("tw-hidden", "is-disabled");
  heroImageGrid.classList.add("tw-hidden", "is-disabled");

  let inputs = layoutField.querySelectorAll(".radio-group input");
  inputs.forEach(element => {
    
    if ( element.nextElementSibling != null && element.nextElementSibling.tagName == "LABEL" ) {
      let uid = element.nextElementSibling
        .innerText
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');

      element.setAttribute('data-target', uid);
      element.parentElement.classList.add(uid);

      let image = document.createElement("img");
      image.setAttribute('id', uid);
      image.setAttribute('src', "/static/icons/layout-" + uid + ".png");
      image.classList.add('layout-icon');
      element.nextElementSibling.append(image);

      element.addEventListener("click", function(event){
        let target = event.currentTarget.getAttribute('data-target');
        
        if ( target && target == "offset-text" ){
          responsiveHero.classList.remove("is-disabled");
          responsiveHero.classList.replace("tw-hidden", "tw-not-hidden");
        } else {
          responsiveHero.classList.add("is-disabled");
          responsiveHero.classList.replace("tw-not-hidden", "tw-hidden");
        }

        if ( target && target == "left-side-text" ){
          heroLeftText.classList.remove("is-disabled");
          heroLeftText.classList.replace("tw-hidden", "tw-not-hidden");
        } else {
          heroLeftText.classList.add("is-disabled");
          heroLeftText.classList.replace("tw-not-hidden", "tw-hidden");
        }
        
        if ( target && target == "video-centered" ){
          heroVideoCentered.classList.remove("is-disabled");
          heroVideoCentered.classList.replace("tw-hidden", "tw-not-hidden");
        } else {
          heroVideoCentered.classList.add("is-disabled");
          heroVideoCentered.classList.replace("tw-not-hidden", "tw-hidden");
        }
        
        if ( target && target == "image-only" ){
          heroImageOnly.classList.remove("is-disabled");
          heroImageOnly.classList.replace("tw-hidden", "tw-not-hidden");
        } else {
          heroImageOnly.classList.add("is-disabled");
          heroImageOnly.classList.replace("tw-not-hidden", "tw-hidden");
        }

        if ( target && target == "image-grid" ){
          heroImageGrid.classList.remove("is-disabled");
          heroImageGrid.classList.replace("tw-hidden", "tw-not-hidden");
        } else {
          heroImageGrid.classList.add("is-disabled");
          heroImageGrid.classList.replace("tw-not-hidden", "tw-hidden");
        }

      });
    }

  });

  let preselected = layoutField.querySelector(".radio-group input:checked");
  if ( preselected != null ){
    preselected.click();
  }

}

let checkType = $('#entryType option:selected').text();

if (checkType == "Menu Tab" || checkType == "Mega Menu Tab") {
  $('#main-form').on('submit', function(event){
    let length = $("#title").val().trim().length;
    if ( length > 15 ) {
      if (confirm('Navigation Tabs should not be longer than 15 characters in length due to the very limited space available in the header. If you continue, the navigation may break. Continue saving this entry?') == false ) {
        event.preventDefault();
        event.stopPropagation();
      }
    }
  });
}
