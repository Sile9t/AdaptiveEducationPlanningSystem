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
use App\MoonShine\Resources\PermitResource;
use App\MoonShine\Resources\TrainingEventResource;

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
                    MenuItem::make('Events', TrainingEventResource::class),
                ]),
                MenuGroup::make('Staff', [
                    MenuItem::make('Employees', EmployeeResource::class),
                    MenuItem::make('Employee Categories', EmployeeCategoryResource::class),
                ]),
                MenuItem::make('Permits', PermitResource::class),
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

        // $colorManager->bulkAssign([
        //     'primary' => '#1A4FDA',
        //     'secondary' => '#0D80EC',
        //     'body' => '#1A4FDA',

        //     'dark' => [
        //         50 => '#1A5DDA',
        //         100 => '#FFFFFF',  //dividers
        //         200 => '#FFFFFF',  //dividers
        //         300 => '#0D80EC',  //borders
                
        //         400 => '#FFFFFF',  //dropdowns, btns, paginations
                
        //         500 => '#0D80EC',  //btn bg
        //         600 => '#1A5DDA',  //table rows

        //         700 => '#0D80EC',  //content bg
        //         800 => '#0D80EC',  //sidebar bg
        //         900 => '#0D80EC',  //bg
        //     ],
        // ]);

        // $colorManager->primary('#2B61EC');
        // $colorManager->secondary('#0D80EC');
        // $colorManager->background('#1A4FDA');
        // $colorManager->tableRow('#0D80EC');
        // $colorManager->borders('#006CD8');
        // $colorManager->dropdowns('#006CD8');
        // $colorManager->buttons('#2B61EC');
        // $colorManager->dividers('#2B61EC');
        // $colorManager->content('#2B61EC');
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
