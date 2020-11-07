<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Cada que se crea un usuario se dispara este evento
        User::created(function ($user) {
            retry(5,function() use ($user) {
                Mail::to($user)->send(new UserCreated($user));
            },100);
        });

       // Se dispara este evento cuando un usuario cambia su correo
        User::updated(function ($user) {
            if ($user->isDirty('email')) {
                retry(5,function() use ($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                },100);
            }
        });

        // Evento para cambiar el estado de un producto
        Product::updated(function ($product) {
            if ($product->quantity == 0 && $product->estaDisponible()) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;

                $product->save();
            }
        });
    }
}
