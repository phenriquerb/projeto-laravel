<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tecnico.{funcionarioId}', function ($user, $funcionarioId) {
    // TODO: Ajustar auth conforme sistema de autenticação implementado
    // Por enquanto, permitir acesso para desenvolvimento
    return true;
});
