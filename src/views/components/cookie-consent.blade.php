{{--

Component: Cookie consent

@component('core::components.cookie-consent')
  $slot('button)
@endcomponent

--}}

<div class="cookie-consent-block js-cookie-consent-block">
    <p>
        <button type="button" class="no-style js-accept-cookies">
            {{ $button ?? __('core::cookie-consent.accept_cookies_consent') }}
            <i class="fa fa-times"></i>
        </button>
    </p>
    <p>
        {!! $slot ??  __('core::cookie-consent.cookies_consent', ['cookie-consent'=> route('web.cookies-' . user_lang())]) !!}
    </p>
</div>