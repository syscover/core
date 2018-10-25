{{--

Component: Cookie consent

@component('core::components.cookie-consent')
  $slot('accept)
@endcomponent

--}}

<div class="cookie-consent-block js-cookie-consent-block">
    <p>
        <button type="button" class="no-style js-accept-cookies">
            {{ $accept ?? __('core::cookie-consent.accept_cookies_consent') }}
            <i class="fas fa-times"></i>
        </button>
    </p>
    <p>
        {!! $text ??  __('core::cookie-consent.cookies_consent', ['cookie-consent'=> route('web.cookies-' . user_lang())]) !!}
    </p>
</div>