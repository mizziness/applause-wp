/* 
  Component - Subscriptions Form
*/

window.applause = window.applause || {};
window.applause.isMobile = function () {
  return window.innerWidth <= 900;
}

const subscriptionsForm = document.getElementById("manage-subscriptions-form");
const messages = {
  notFoundError: "Your email address was not found in our records, so you're all set!",
  successMessage: "You've successfully updated your communication preferences. <br><span class='tw-text-sm'>Did you unsubscribe by mistake? Just send an email to <a class='hover:tw-no-underline hover:tw-text-blue-700 tw-text-blue-500 tw-underline' href='mailto:info@applause.com'>info@applause.com</a> to resubscribe.</span>",
  optionsError: "Please choose at least one option.",
  noEmailError: "Please provide your email address.",
  invalidEmailError: "Please provide a valid email address.",
  serverUnreachableError: "There was a problem reaching the server - refresh the page or try again later!",
  invalidError: "It looks like something went wrong. Refresh this page to continue, or try again later!"
};

const setMessage = (css, message) => {
  const messageContainer = document.getElementById("message");
  let classes = ["animate__animated", "animate__fadeOut", "tw-p-2", "tw-my-6", "tw-rounded", "tw-border", css];
  messageContainer.classList = [classes.join(" ")];
  messageContainer.querySelector("p").innerHTML = message;
  messageContainer.classList.add('animate__fadeIn');
  messageContainer.classList.remove('animate__fadeOut');
};

const showError = (element) => {
  let found = document.querySelector(element);
  if (found != null) {
    found.classList.add("has-error");
  }
};

const hideError = (element) => {
  let found = document.querySelector(element);
  if (found != null) {
    found.classList.remove("has-error");
  }
};

const clearErrors = () => {
  let found = subscriptionsForm.querySelectorAll(".has-error");
  if (found.length > 0) {
    found.forEach((item) => item.classList.remove("has-error"));
  }
};

const validateForm = function () {
  // Does the main body of the work for the
  // Subscriptions page form
  let pattern = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/gm;
  let emailAddress = subscriptionsForm.querySelector("#email").value;
  let hasEmail = emailAddress.length > 0;
  let validEmail = emailAddress.search(pattern) >= 0;
  let choices = subscriptionsForm.querySelectorAll("#choices input:checked").length;

  if (!hasEmail) {
    setMessage("tw-bg-red-100 tw-border-red-600", messages.noEmailError);
    showError("#email");
    return false;
  } else {
    hideError("#email");
  };

  if (!validEmail) {
    setMessage("tw-bg-red-100 tw-border-red-600", messages.invalidEmailError);
    showError("#email");
    return false;
  } else {
    hideError("#email");
  };

  if (choices <= 0) {
    setMessage("tw-bg-red-100 tw-border-red-600", messages.optionsError);
    showError("#choices");
    return false;
  } else {
    hideError("#choices");
  };

  return true;
};

// let timeSinceLoad = subscriptionsForm.querySelector(".timeSinceLoad");
// timeSinceLoad.setAttribute('value', Date.now());

const triggerFormClick = (event) => {
  // let timeSinceFirstInteraction = subscriptionsForm.querySelector('.timeSinceFirstInteraction');
  // if (timeSinceFirstInteraction.value.length <= 0) {
  //   timeSinceFirstInteraction.setAttribute('value', Date.now());
  // }
}

subscriptionsForm.addEventListener("change", triggerFormClick);
subscriptionsForm.addEventListener("focus", triggerFormClick);
subscriptionsForm.addEventListener("click", triggerFormClick);

const optOutAll = document.getElementById("optout-all");
optOutAll.addEventListener("change", function () {
  if (optOutAll.checked == true) {
    subscriptionsForm.querySelectorAll(".form-inner input[type=checkbox]").forEach((input) => { input.checked = true; });
  } else {
    subscriptionsForm.querySelectorAll(".form-inner input[type=checkbox]").forEach((input) => { input.checked = false; });
  }
});

const optOutSales = document.getElementById("optout-sales");
optOutSales.addEventListener("change", function () {
  if (optOutSales.checked == true) {
    subscriptionsForm.querySelectorAll(["#optout-email", "#optout-phone"]).forEach((input) => { input.checked = true; });
  } else {
    subscriptionsForm.querySelectorAll(["#optout-email", "#optout-phone"]).forEach((input) => { input.checked = false; });
  }
});

const salesOptions = document.querySelectorAll(["#optout-email", "#optout-phone"]);
salesOptions.forEach(item => {
  item.addEventListener("change", function () {
    let total = document.querySelector("#options div input[type=checkbox]").length;
    let checked = document.querySelector("#options div input[type=checkbox]:checked").length;
    if (checked >= total) {
      document.getElementById("optout-sales").checked = true;
    } else {
      document.getElementById("optout-sales").checked = false;
    }
  });
});

subscriptionsForm.addEventListener("reset", function () {
  subscriptionsForm.querySelectorAll("input[type=checkbox]").forEach((input) => { input.checked = false; });
  subscriptionsForm.querySelectorAll("input[type=email]").forEach((input) => { input.value = ''; });
});

subscriptionsForm.addEventListener("submit", function (e) {
  e.preventDefault();

  var valid = validateForm();
  if (!valid) { return false; }

  // let timeToSubmit = subscriptionsForm.querySelector(".timeToSubmit");
  // timeToSubmit.setAttribute("value", Date.now());

  // Get the response as JSON
  let fetchUrl = subscriptionsForm.getAttribute("action");
  let formData = new FormData(subscriptionsForm);

  fetch(fetchUrl, { method: "POST", body: formData }).then(response => {
    if (response.ok) {
      const contentType = response.headers.get('Content-Type') || '';
      if (contentType.includes('application/json')) {
        return response.json();
      }
      return Promise.reject(Error('Invalid content type: ' + contentType));
    }
    return Promise.reject(Error(response.status));
  }).then(responseData => {

    let data = responseData[0];

    if (data.status == 200 && data.message == "done") {
      // Request completed successfully, and updates were made.
      setMessage("tw-bg-green-100 tw-border-green-500", messages.successMessage);

    } else if (data.status == 200 && data.message == "none") {
      // Request completed successfully, email not found.
      setMessage("tw-bg-green-100 tw-border-green-500", messages.notFoundError);

    } else if (data.status == 500) {
      // Custom error message, likely from Salesforce because they hate me
      setMessage("tw-bg-red-100 tw-border-red-600", messages.serverUnreachableError);

    } else {
      // IDFK what went wrong but something exploded. Act like everything's cool.
      setMessage("tw-bg-red-100 tw-border-red-600", messages.invalidError);
    }

  });
});
