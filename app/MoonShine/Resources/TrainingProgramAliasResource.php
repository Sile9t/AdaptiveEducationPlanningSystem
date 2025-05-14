<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\TrainingProgram;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingProgramAlias;

use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;

/**
 * @extends ModelResource<TrainingProgramAlias>
 */
class TrainingProgramAliasResource extends ModelResource
{
    protected string $model = TrainingProgramAlias::class;

    protected string $title = 'TrainingProgramAlias';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make("Alias")->sortable(),
                Text::make("Comment"),
                BelongsTo::make(
                    'Program', 
                    'program',
                    static fn(TrainingProgram $model) => $model->title, 
                    resource: new TrainingProgramResource()
                ),
            ]),
        ];
    }

    /**
     * @param TrainingProgramAlias $item
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
