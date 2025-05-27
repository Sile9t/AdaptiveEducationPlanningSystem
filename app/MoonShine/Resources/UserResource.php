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
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Fields\Switcher;

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'User';

    protected string $sortColumn = 'id';

    protected SortDirection $sortDirection = SortDirection::ASC;
    
    protected bool $columnSelection = true;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Personnel number')->translatable('resource.user'),
            BelongsTo::make(
                'Role', 
                'role', 
                static fn(Role $model) => $model->name,
                resource: RoleResource::class
            )->sortable()->badge(Color::BLUE)->translatable('resource.role'),
            BelongsTo::make(
                'Branch', 
                'branch', 
                static fn(Branch $model) => $model->name, 
                resource: BranchResource::class
            )->sortable()->translatable('resource.branch'),
            Text::make('First name')->sortable()->translatable('resource.user'),
            Text::make('Last name')->sortable()->translatable('resource.user'),
            Text::make('Patronymic')->sortable()->translatable('resource.user'),
            Email::make('Email')->sortable()->translatable('resource.user'),
            Switcher::make('Must change password')->translatable('resource.user'),
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
                    Text::make('Personnel number')->translatable('resource.user'),
                    Text::make('First name')->translatable('resource.user'),
                    Text::make('Last name')->translatable('resource.user'),
                    Text::make('Patronymic')->translatable('resource.user'),
                    Email::make('Email')->translatable('resource.user'),
                    BelongsTo::make(
                        'Branch', 
                        'branch', 
                        static fn(Branch $model) => $model->name, 
                        resource: BranchResource::class
                    )->translatable('resource.branch'),
                    BelongsTo::make(
                        'Role', 
                        'role', 
                        static fn(Role $model) => $model->name,
                        resource: RoleResource::class
                    )->translatable('resource.role'),
                    Switcher::make('Must change password')->translatable('resource.user'),
                ]),
    
                Tab::make(__('moonshine::ui.resource.password'), [
                    Heading::make(__('moonshine::ui.resource.change_password')),
    
                    Password::make('Password')->translatable('resource.user')
                        ->eye(),
                    PasswordRepeat::make('Password repeat')->translatable('resource.user')
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
        return $this->indexFields();
    }

    /**
     * @return list<FieldContract>
     */
    protected function search(): array
    {
        return [
            'personnel_number', 'first_name', 'last_name', 'patronymic', 'email'
        ];
    }

    protected bool $saveQueryState = true;
    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            Text::make('Personnel number')->translatable('resource.user'),
            Text::make('First name')->translatable('resource.user'),
            Text::make('Last name')->translatable('resource.user'),
            Text::make('Patronymic')->translatable('resource.user'),
            Email::make('Email'),
            BelongsTo::make(
                'Branch', 
                'branch', 
                static fn(Branch $model) => $model->name, 
                resource: BranchResource::class
            )->nullable()->searchable()->translatable('resource.branch'),
            BelongsTo::make(
                'Role', 
                'role', 
                static fn(Role $model) => $model->name,
                resource: RoleResource::class
            )->nullable()->searchable()->translatable('resource.role'),
            Switcher::make('Must change password')->translatable('resource.user'),
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
            'password' => ['required', 'min:8'],
            'password_repeat' => ['required', 'same:password','min:8'],
        ];
    }
}
