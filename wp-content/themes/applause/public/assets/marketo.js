$(document).on("ready", function(){
  let span = $("<span></span>").addClass("txt-small").html("Fields marked with a (<span class='red'>*</span>) are required.");
  $("form.mktoForm").before(span);

  $(".mktoFieldWrap.mktoRequiredField").each(function(){
    // Add aria labels
    $(this).find("input.mktoRequired").attr("aria-required", true);
  });

  $("input.mktoRequired").on("blur", function(){
    // If a required input is empty on blur, change the value so it fires an error.
    if ( $(this).val().trim().length <= 0 ) {
      $(this).val(" ");
    };
  });
});
