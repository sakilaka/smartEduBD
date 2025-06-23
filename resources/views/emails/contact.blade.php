@component("mail::message")

{{ $data['message']??'' }}


<br /><br />
Thanks <br />
{{ $data['name']??'' }} <br />
{{ $data['mobile']??'' }} <br />
{{ $data['email']??'' }}
@endcomponent