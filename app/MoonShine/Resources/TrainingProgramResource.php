<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingProgram;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;

/**
 * @extends ModelResource<TrainingProgram>
 */
class TrainingProgramResource extends ModelResource
{
    protected string $model = TrainingProgram::class;

    protected string $title = 'TrainingProgram';

    protected string $column = 'title';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->sortable(),
            HasMany::make(
                'Aliases',
                'aliases',
                formatted: 'alias',
                resource: TrainingProgramAliasResource::class
            )->relatedLink(),
            HasMany::make(
                'Permits',
                'permits',
                resource: PermitResource::class
            )->relatedLink(),
            //TODO: add 'events' columns
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Title'),
                HasMany::make(
                    'Aliases',
                    'aliases',
                    formatted: 'alias',
                    resource: TrainingProgramAliasResource::class
                ),
                //TODO: add 'permits' and 'events' columns
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            HasMany::make(
                'Aliases',
                'aliases',
                formatted: 'alias',
                resource: TrainingProgramAliasResource::class
            ),
            //TODO: add 'permits' and 'events' columns
        ];
    }

    /**
     * @param TrainingProgram $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
