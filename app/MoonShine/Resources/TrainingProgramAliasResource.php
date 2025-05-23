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
use MoonShine\Support\Enums\SortDirection;

/**
 * @extends ModelResource<TrainingProgramAlias>
 */
class TrainingProgramAliasResource extends ModelResource
{
    protected string $model = TrainingProgramAlias::class;

    protected string $title = 'TrainingProgramAlias';

    protected string $column = 'alias'; 

    protected string $sortColumn = 'id';

    protected SortDirection $sortDirection = SortDirection::ASC;
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class
            )->sortable()->translatable('resource.training_program'),
            Text::make('Alias')->sortable()->translatable('resource.training_program.alias'),
            Text::make('Comment')->sortable()->translatable('resource.training_program_alias'),
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
                BelongsTo::make(
                    'Program',
                    'program',
                    formatted: 'title',
                    resource: TrainingProgramResource::class
                ),
                Text::make('Alias'),
                Text::make('Comment'),
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
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class
            ),
            Text::make('Alias'),
            Text::make('Comment'),
        ];
    }

    /**
     * @param TrainingProgramAlias $item
     *
     * @return array<string, string[]|string>
     */
    protected function search(): array
    {
        return ['id', 'program.title', 'alias'];
    }

    protected bool $saveQueryState = true;

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class
            )->searchable()->translatable('resource.training_program'),
            Text::make('Alias')->translatable('resource.training_program.alias'),
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
        return [
            'alias' => ['required', 'string', 'min:2'],
        ];
    }
}
