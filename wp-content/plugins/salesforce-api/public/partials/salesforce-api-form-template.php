<div class="tw-container">
    <div id="message" class="" role="alert" aria-live="polite">
        <p class="tw-p-2 tw-mb-0"></p>
    </div>
    <form id="manage-subscriptions-form" action="/wp-json/salesforce-api/v1/subscriptions/submit" method="POST" autocomplete="off">
        <input type="hidden" name="accessToken" value="00D80000000K3IP!ARwAQI5Lbe3MaQf3E3z9y17icJ1YdhyWDy6zb1HQGqEq1AAkiKdHwBIopVI.Netf09xrEmIqQYZQDHbd5Z02HKN2B__LvgOE">
        <input type="hidden" name="ipAddress" value="173.48.186.147">
        <input name="timeSinceLoad" class="timeSinceLoad" value="1686764818075" readonly="" type="hidden">
        <input name="timeSinceFirstInteraction" class="timeSinceFirstInteraction" value="" readonly="" type="hidden">
        <input name="timeToSubmit" class="timeToSubmit" value="" readonly="" type="hidden">
        <div id="manage-subscriptions" class="tw-text-left">
            <div class="form-inner tw-p-6 tw-text-left tw-bg-gray-100 tw-border tw-border-gray-300 tw-rounded">
                <label for="email">Enter your email address:</label><br>
                <input id="email" name="email" class="tw-px-2 tw-py-1 tw-border tw-rounded-sm" type="email" value="" placeholder="your@email.here">
                <br><br>

                <p class="tw-mb-2">Please select the type of communications you would like to stop receiving from us:
                </p>
                <div id="choices" class="tw-p-2">
                    <div class="tw-ml-4">
                        <label for="optout-marketing" class="tw-mb-2">
                            <input class="tw-border" type="checkbox" id="optout-marketing" name="optout-marketing">
                            Marketing updates including invitations to webinars, resource offers, research reports, and
                            newsletters
                        </label>

                        <div id="options">
                            <label for="optout-sales">
                                <input class="tw-border" type="checkbox" id="optout-sales" name="optout-sales">
                                All one-to-one communications &amp; promotions
                            </label>
                            <br>
                            <div class="tw-ml-4">
                                <label for="optout-email">
                                    <input class="tw-border" type="checkbox" id="optout-email" name="optout-email">
                                    One-to-one emails
                                </label>
                                <br><label for="optout-phone">
                                    <input class="tw-border" type="checkbox" id="optout-phone" name="optout-phone">
                                    One-to-one phone calls
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <p class="tw-mb-2">Or select here to opt out of all further communications from Applause:</p>
                    <div class="tw-ml-4">
                        <input class="tw-border" type="checkbox" id="optout-all" name="optout-all">
                        <label for="optout-all">Select all</label><br>
                    </div>
                </div>
                <div data-lastpass-icon-root="true" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;">
                </div>
            </div>
        </div><input type="checkbox" id="contact_me_by_phone_only" name="contact_me_by_phone_only" value="1" style="display:none !important" tabindex="-1" autocomplete="off">
        <div class="tw-grid tw-items-center">
            <div class="tw-text-center">
                <button id="submit" type="submit" name="submit" data-category="Form Submit" data-action="Button" data-label="https://www.applause.com/subscriptions" class="button is-secondary submit-track tw-inline-block tw-px-4 tw-mt-4 tw-mr-4">Unsubscribe</button>
                <button id="reset" type="reset" name="reset" class="button is-secondary is-outlined tw-inline-block tw-px-4 tw-mt-4 tw-text-gray-700 tw-border-gray-500">Clear</button>
            </div>
            <div class="tw-mt-4 tw-text-xs tw-text-center">
                <a class="hover:tw-no-underline tw-text-blue-500 tw-underline" href="/terms-of-use">Terms &amp;
                    Conditions</a> |
                <a class="hover:tw-no-underline tw-text-blue-500 tw-underline" href="/privacy-policy">Privacy Policy</a>
            </div>
        </div>
    </form>
    <div id="overlay" class="tw-absolute tw-w-full tw-h-full" role="alert" aria-busy="true" aria-label="Loading, please wait...">
        <div class="lds-circle">
            <div></div>
        </div>
    </div>
</div>