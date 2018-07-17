$(function(){
    $(".js-accept-cookies").on('click', function () {
        $('.js-cookie-consent-block').fadeOut();
        Cookies.set('user_cookie_consent', 1, {expires: 365, path: "/"});
    });

    if(Cookies.get("user_cookie_consent") ? !1 : !0)
    {
        $('.js-cookie-consent-block').fadeIn();
    }
});
