<x-mail::message>
    # Hi {{ $name }},

    Thank you for creating an account with us. Don't forget to complete your registration!
    Please use the code below to confirm your email address:

    ## {{ $verification_code }}

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>