<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingProgramAlias;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

/**
 * @extends ModelResource<TrainingProgramAlias>
 */
class TrainingProgramAliasResource extends ModelResource
{
    protected string $model = TrainingProgramAlias::class;

    protected string $title = 'TrainingProgramAlias';

    protected string $column = "alias"; 
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Alias')->sortable(),
            Text::make('Comment')->sortable(),
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class
            )->sortable(),
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
                Text::make('Alias'),
                Text::make('Comment'),
                BelongsTo::make(
                    'Program',
                    'program',
                    formatted: 'title',
                    resource: TrainingProgramResource::class
                ),
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
            Text::make('Alias'),
            Text::make('Comment'),
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class
            ),
        ];
    }

    /**
     * @param TrainingProgramAlias $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
