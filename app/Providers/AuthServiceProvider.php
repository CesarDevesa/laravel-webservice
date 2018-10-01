<?php

namespace App\Providers;

use App\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        foreach ($this->allPermissions() as $perm) {
            // dd($this->allPermissions());
            // dd($perm->name);
            // Gate::define(PermissionName, TRUE || FALSE);
            Gate::define($perm->name, function ($user) use ($perm) {
                return $user->hasRole($perm->role) || $user->isSuperUser();
            });
        }
    }

    public function allPermissions()
    {
        // Usado o try para não conflitar na criação das migrações (php artisan migrate)
        try {

            return Permission::with('role')->get();

        } catch (\Exception $e) {

            return [];

        }
    }

}
/*
 * ACL Map - Anarquico
 *
 * User1 (Support)
 * - Tudo para testes e manutenção
 *
 * PublicGuest
 * -ver publicados
 *
 * User2 (Registered)
 * -Ver tudo
 * -Cria
 * -Edita os seus
 * -Deleta os seus
 * -Publica os seus
 * -Sugere edição os de outros
 * -Sugere deletar os de outros
 * -Sugere publicar os de outros
 *
 * */

/*
 * ACL Map - Padrão
 *
 * SuperUser
 * -Tudo
 *
 * PublicGuest
 * -ver publicados
 *
 * Registered(Group)
 *
 * Author
 * -ver os publicados
 * -cria
 *
 * Editor
 * -ver os publicados
 * -cria
 * -edita os seus
 * -edita os de outros
 * -publica os seus
 *
 * Publisher
 * -ver os publicados
 * -cria
 * -edita os seus
 * -edita os de outros
 * -publica os seus
 * -publica os de outros
 *
 * Manager
 * -ver os publicados
 * -cria
 * -edita os seus
 * -edita os de outros
 * -publica os seus
 * -publica os de outros
 * -deleta os seus
 * -deleta os de outros
 * -gerencia usuários(cria, deleta, edita)
 *
 * Administrator
  * -ver os publicados
 * -cria
 * -edita os seus
 * -edita os de outros
 * -publica os seus
 * -publica os de outros
 * -deleta os seus
 * -deleta os de outros
 * -gerencia usuários(cria, deleta, edita)
 * -gerencia ACL (papeis e permissões)
 * -gerencia Configurações do Sistema
 *
 * */


/*
 * ACL Map - Neste projeto
 *
 * SuperUser
 * -Tudo
 *
 * Registered
 * -ver publicados
 *
 * Author
 * -ver os publicados
 * -cria
 * -edita os seus
 *
 * Publisher
 * -ver os publicados
 * -cria
 * -edita os seus
 * -edita os de outros
 * -publica os seus
 * -publica os de outros
 * -deleta os seus
 * -deleta os de outros
 * -ver os usuários
 *
 * Manager
 * -ver os publicados
 * -cria
 * -edita os seus
 * -edita os de outros
 * -publica os seus
 * -publica os de outros
 * -deleta os seus
 * -deleta os de outros
 * -gerencia usuários(lista, cria, atualiza, deleta)
 * -gerencia ACL - Papeis (lista, cria, atualiza, deleta)
 * -gerencia ACL - Permissões (lista)
 *
 * */
