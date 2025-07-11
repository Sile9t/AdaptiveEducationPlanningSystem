<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Resources\BranchResource;
use App\MoonShine\Resources\RoleResource;
use App\MoonShine\Resources\TrainingProgramResource;
use App\MoonShine\Resources\TrainingProgramAliasResource;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\EmployeeCategoryResource;
use App\MoonShine\Resources\EmployeeResource;
use App\MoonShine\Resources\PermitResource;
use App\MoonShine\Resources\TrainingEventResource;
use MoonShine\Contracts\AssetManager\AssetManagerContract;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     * @param  AssetManagerContract  $assets
     *
     */
    public function boot(CoreContract $core, ConfiguratorContract $config, AssetManagerContract $assets): void
    {
        // $config->authEnable();
        $config->title('AEPS');
        $config->logo('/gaspromLogo.svg');
        $config->locale('ru');
        $config->locales(['ru', 'en']);

        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                
                BranchResource::class,
                RoleResource::class,
                TrainingProgramResource::class,
                TrainingProgramAliasResource::class,
                UserResource::class,
                EmployeeCategoryResource::class,
                EmployeeResource::class,
                PermitResource::class,
                TrainingEventResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}
