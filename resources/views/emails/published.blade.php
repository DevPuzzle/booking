@component('mail::message')
# Introduction

The body of your message.

@component('mail::panel', ['url' => ''])
    Publish of a new Page
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
