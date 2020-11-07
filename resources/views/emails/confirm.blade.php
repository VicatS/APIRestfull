@component('mail::message')
    # Hola {{ $user->name }}

    Has cambiado tu correo electronico. Por favor verifica la nueva direccion usando el siguiente enlace:

    @component('mail::button', ['url' => route('verify', $user->verification_token)])
    Confirmar mi cuenta
    @endcomponent

    Gracias,
    {{ config('app.name') }}
@endcomponent

