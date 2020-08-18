@component('email.indicator', ['status' => 'success'])
	Your application is installed.
@endcomponent

@component('mail::message')
{{-- Body --}}
# Howdy!

{{ $appname }} has been successfully installed on {{ $uri }}!

## Finish setup
Before you can begin using Discourse you'll need to confirm your email. We used your account email {{ $email }}.
Follow the activation link below to finish customizing or just visit [{{$proto}}{{$uri}}]({{$proto}}{{$uri}})

@component('mail::button', ['url' => "${proto}${uri}"])
	Customize Discourse
@endcomponent

---

@include('email.webapps.common-footer')
@endcomponent