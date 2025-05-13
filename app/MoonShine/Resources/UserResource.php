<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Branch;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Email;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'User';
    
    protected array $with = [
        'branch', 
        'role',
    ];

    protected string $column = 'id';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),
                        Text::make('First name')->sortable(),
                        Text::make('Last name')->sortable(),
                        Text::make('Patronymic')->sortable(),
                        Email::make('Email')->sortable(),
                        BelongsTo::make(
                            'Branch', 
                            'branch', 
                            static fn(Branch $model) => $model->name, 
                            resource: new BranchResource)->sortable(),
                        BelongsTo::make(
                            'Role', 
                            'role', 
                            resource: new RoleResource)->sortable(),
                    ]),

                    Tab::make(__('moonshine::ui.resource.password'), [
                        Heading::make(__('moonshine::ui.resource.change_password')),

                        Password::make('Password')
                            ->hideOnIndex(),
                        PasswordRepeat::make('Password repeat')
                            ->hideOnIndex(),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        //TODO:
        return [];
    }
}
