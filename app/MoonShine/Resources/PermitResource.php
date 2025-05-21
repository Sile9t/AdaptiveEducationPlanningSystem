<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permit;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Range;

/**
 * @extends ModelResource<Permit>
 */
class PermitResource extends ModelResource implements HasImportExportContract
{
    protected string $model = Permit::class;

    protected string $title = 'Permits';
    
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
                resource: TrainingProgramResource::class
            )->sortable()->creatable(),
            BelongsTo::make(
                'Category',
                'category',
                resource: EmployeeCategoryResource::class
            )->sortable()->creatable(),
            Number::make(
                'Periodicity (years)',
                'periodicity_years'
            )->sortable(),
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
    
    protected function search(): array
    {
        return ['id', 'program.title', 'category.name'];
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
                resource: TrainingProgramResource::class
            )->nullable()->searchable(),
            BelongsTo::make(
                'Category',
                'category',
                resource: EmployeeCategoryResource::class
            )->nullable()->searchable(),
            Range::make(
                'Periodicity (years)',
                'periodicity_years'
            ),
        ];
    }

    /**
     * @param Permit $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'periodicity_years' => ['required', 'Integer']
        ];
    }

    use ImportExportConcern;

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class,
            ),
            BelongsTo::make(
                'Category',
                'category',
                formatted: 'name',
                resource: EmployeeResource::class,
            ),
            Number::make(
                'Periodicity (years)',
                'periodicity_years'
            ),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make(
                'Program',
                'program',
                formatted: 'title',
                resource: TrainingProgramResource::class,
            )->creatable(),
            BelongsTo::make(
                'Category',
                'category',
                formatted: 'name',
                resource: EmployeeResource::class,
            )->creatable(),
            Number::make(
                'Periodicity (years)',
                'periodicity_years'
            ),
        ];
    }
}
