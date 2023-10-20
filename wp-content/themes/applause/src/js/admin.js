jQuery(function ($) {

    // Support for Featured Post layout on the Blog index page
    $("#blog-index-grid #featured-posts .dp-dfg-item").each(function(){
        $(this).find('div').wrapAll('<div class="column"></div>');
        $(this).find('figure').wrapAll('<div class="column"></div>');
    });

});