<%--
compare https://developer.matomo.org/guides/tracking-javascript-guide#optional-creating-a-custom-opt-out-form
--%>
<div class="matomo-optout-form">
    <%t Mhe\Matomo\Extensions\MatomoConfig.OPTOUT_FORM_TEXT '<p>To opt out from analysis by Matomo, please deactivate the checkbox.</p>' %>
    <div class="field" >
        <input type="checkbox" id="optout" />
        <label for="optout" class="right"><% if not $SiteConfig.UseMatomo %><%t Mhe\Matomo\Extensions\MatomoConfig.TRACKING_DEACTIVATED 'Tracking is currently not active'%><% end_if %></label>
    </div>
</div>
<% if $SiteConfig.UseMatomo %>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
    function setOptOutText(element) {
        _paq.push([function() {
            element.checked = !this.isUserOptedOut();
            document.querySelector('label[for=optout]').innerText = this.isUserOptedOut()
                    ? '<%t Mhe\Matomo\Extensions\MatomoConfig.YOU_ARE_OPTED_OUT 'You are currently opted out.'%>'
                    : '<%t Mhe\Matomo\Extensions\MatomoConfig.YOU_ARE_OPTED_IN 'You are currently opted in.'%>';
        }]);
    }
    var optOut = document.getElementById("optout");
    optOut.addEventListener("click", function() {
        if (this.checked) {
            _paq.push(['forgetUserOptOut']);
        } else {
            _paq.push(['optUserOut']);
        }
        setOptOutText(optOut);
    });
    setOptOutText(optOut);
});
</script>
<% end_if %>