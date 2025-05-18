<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Profile, Search};
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Flash,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Footer,
    Layout\Head,
    Layout\Favicon,
    Layout\Assets,
    Layout\Meta,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\TopBar,
    Layout\Wrapper,
    When};
use App\MoonShine\Resources\BranchResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\RoleResource;
use App\MoonShine\Resources\TrainingProgramResource;
use App\MoonShine\Resources\TrainingProgramAliasResource;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\EmployeeCategoryResource;
use App\MoonShine\Resources\EmployeeResource;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            
            MenuGroup::make('Main', [
                MenuItem::make('Users', UserResource::class)->icon('users'),
                MenuItem::make('Roles', RoleResource::class)->icon('bookmark'),
                MenuItem::make('Branch', BranchResource::class)->icon('user-group'),
                MenuGroup::make('Training', [
                    MenuItem::make('Programs', TrainingProgramResource::class),
                    MenuItem::make('Program Aliases', TrainingProgramAliasResource::class),
                ]),
                MenuGroup::make('Staff', [
                    MenuItem::make('Employees', EmployeeResource::class),
                    MenuItem::make('Employee Categories', EmployeeCategoryResource::class),
                ]),
            ]),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
