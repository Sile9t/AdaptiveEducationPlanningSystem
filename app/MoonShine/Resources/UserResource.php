<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Branch;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\Tabs;

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'User';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('First name')->sortable(),
            Text::make('Last name')->sortable(),
            Text::make('Patronymic')->sortable(),
            Email::make('Email')->sortable(),
            BelongsTo::make(
                'Branch', 
                'branch', 
                static fn(Branch $model) => $model->name, 
                resource: BranchResource::class
            )->sortable(),
            BelongsTo::make(
                'Role', 
                'role', 
                static fn(Role $model) => $model->name,
                resource: RoleResource::class
            )->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Tabs::make([
                Tab::make(__('moonshine::ui.resource.main_information'), [
                    ID::make(),
                    Text::make('First name'),
                    Text::make('Last name'),
                    Text::make('Patronymic'),
                    Email::make('Email'),
                    BelongsTo::make(
                        'Branch', 
                        'branch', 
                        static fn(Branch $model) => $model->name, 
                        resource: BranchResource::class
                    ),
                    BelongsTo::make(
                        'Role', 
                        'role', 
                        static fn(Role $model) => $model->name,
                        resource: RoleResource::class
                    ),
                ]),
    
                Tab::make(__('moonshine::ui.resource.password'), [
                    Heading::make(__('moonshine::ui.resource.change_password')),
    
                    Password::make('Password')
                        ->eye(),
                    PasswordRepeat::make('Password repeat')
                        ->eye(),
                ]),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('First name'),
            Text::make('Last name'),
            Text::make('Patronymic'),
            Email::make('Email'),
            BelongsTo::make(
                'Branch', 
                'branch', 
                static fn(Branch $model) => $model->name, 
                resource: BranchResource::class
            ),
            BelongsTo::make(
                'Role', 
                'role', 
                static fn(Role $model) => $model->name,
                resource: RoleResource::class
            ),
        ];
    }

    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'patronymic' => ['string', 'nullable', 'min:3'],
            'email' => ['required', 'unique:users,email','email'],
            'branch' => ['required'],
            'role' => ['required'],
            'password' => ['required', 'min:8'],
            'password_repeat' => ['required', 'same:password','min:8'],
        ];
    }
}
