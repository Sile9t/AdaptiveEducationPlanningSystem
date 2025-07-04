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
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Support\Enums\SortDirection;

/**
 * @extends ModelResource<TrainingProgram>
 */
class TrainingProgramResource extends ModelResource implements HasImportExportContract
{
    protected string $model = TrainingProgram::class;

    protected string $column = 'title';

    protected string $sortColumn = 'id';

    protected SortDirection $sortDirection = SortDirection::ASC;
    
    public function getTitle(): string
    {
        return __('resource.training_program.Programs');
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->sortable()->translatable('resource.training_program'),
            HasMany::make(
                'Aliases',
                'trainingProgramAliases',
                formatted: 'alias',
                resource: TrainingProgramAliasResource::class
            )->translatable('resource.training_program.alias')->relatedLink(),
            HasMany::make(
                'Permits',
                'permits',
                resource: PermitResource::class
            )->relatedLink()->translatable('resource.permit'),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return $this->indexFields();
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    /**
     * @param TrainingProgram $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:3']
        ];
    }
    
    use ImportExportConcern;

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title')->translatable('resource.training_program'),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title')->translatable('resource.training_program'),
        ];
    }
}
