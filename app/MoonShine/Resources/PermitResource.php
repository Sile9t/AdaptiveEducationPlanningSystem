<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\EmployeeCategory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permit;
use App\Models\TrainingProgram;
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
            )->sortable()->creatable()->translatable('resource.training_program'),
            BelongsTo::make(
                'Category',
                'category',
                resource: EmployeeCategoryResource::class
            )->sortable()->creatable()->translatable('resource.employee_category'),
            Number::make(
                'Periodicity (years)',
                'periodicity_years'
            )->sortable()->translatable('resource.permit'),
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
            )->nullable()->searchable()->translatable('resource.training_program'),
            BelongsTo::make(
                'Category',
                'category',
                resource: EmployeeCategoryResource::class
            )->nullable()->searchable()->translatable('resource.employee_category'),
            Range::make(
                'Periodicity (years)',
                'periodicity_years'
            )->translatable('resource.permit'),
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
            )
            ->fromRaw(function($raw, $ctx) {
                $program = TrainingProgram::where('title', $raw)->first();
                
                if (!isset($program)) {
                    $program = new TrainingProgram();
                    $program->title = $raw;
                    $program->save();
                }
                
                return $program->id;
            }),
            BelongsTo::make(
                'Category',
                'category',
                formatted: 'name',
                resource: EmployeeResource::class,
            )
            ->fromRaw(function($raw, $ctx) {
                $category = EmployeeCategory::where('name', $raw)->first();

                if (!isset($category)) {
                    $category = new EmployeeCategory();
                    $category->name = $raw;
                    $category->save();
                }

                return $category->id;
            }),
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
            )->creatable()->translatable('resource.training_program')
            ->modifyRawValue(fn ($value, $model) => $model->program->title),
            BelongsTo::make(
                'Category',
                'category',
                formatted: 'name',
                resource: EmployeeResource::class,
            )->creatable()->translatable('resource.employee_category')
            ->modifyRawValue(fn ($value, $model) => $model->category->name),
            Number::make(
                'Periodicity (years)',
                'periodicity_years'
            ),
        ];
    }
}
