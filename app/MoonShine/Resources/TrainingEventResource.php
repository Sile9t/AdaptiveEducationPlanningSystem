<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingEvent;
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
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;

/**
 * @extends ModelResource<TrainingEvent>
 */
class TrainingEventResource extends ModelResource implements HasImportExportContract
{
    protected string $model = TrainingEvent::class;

    protected string $sortColumn = 'id';

    protected SortDirection $sortDirection = SortDirection::ASC;
    
    public function getTitle(): string
    {
        return __('resource.training_program.event.Events');
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make(
                'Program',
                'trainingProgram',
                resource: TrainingProgramResource::class
            )->sortable()->translatable('resource.training_program'),
            BelongsTo::make(
                'Personnel number',
                'employee',
                formatted: 'personnel_number',
                resource: EmployeeResource::class
            )->sortable()->translatable('resource.user'),
            BelongsTo::make(
                'Employee',
                'employee',
                formatted: 'full_name',
                resource: EmployeeResource::class
            )->sortable()->translatable('resource.employee'),
            Date::make(
                'Passed at',
                'passed_at'
            )->sortable()->translatable('resource.training_program.event'),
            Date::make(
                'Expired at',
                'expired_at'
            )->sortable()->translatable('resource.training_program.event'),
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
        return ['id', 'program.title', 'employee.full_name'];
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
                'trainingProgram',
                resource: TrainingProgramResource::class
            )->nullable()->searchable()->translatable('resource.training_program'),
            BelongsTo::make(
                'Personnel number',
                'employee',
                formatted: 'personnel_number',
                resource: EmployeeResource::class
            )->sortable()->translatable('resource.user'),
            BelongsTo::make(
                'Employee',
                'employee',
                formatted: 'full_name',
                resource: EmployeeResource::class
            )->nullable()->searchable()->translatable('resource.employee'),
            DateRange::make(
                'Passed at',
                'passed_at'
            )->translatable('resource.training_program.event'),
            DateRange::make(
                'Expired at',
                'expired_at'
            )->translatable('resource.training_program.event'),
        ];
    }

    /**
     * @param TrainingEvent $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'passed_at' => ['required', 'date'],
            'expired_at' => ['required', 'date'],
        ];
    }

    use ImportExportConcern;

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make(
                'Program',
                'trainingProgram',
                resource: TrainingProgramResource::class
            )->translatable('resource.training_program')
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
                'Employee',
                'employee',
                resource: EmployeeResource::class
            )->translatable('resource.employee'),
            Date::make(
                'Passed at',
                'passed_at'
            )->translatable('resource.training_program.event'),
            Date::make(
                'Expired at',
                'expired_at'
            )->translatable('resource.training_program.event'),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make(
                'Program',
                'trainingProgram',
                resource: TrainingProgramResource::class
            )->translatable('resource.training_program')
            ->modifyRawValue(fn($value, $model) => $model->title),
            BelongsTo::make(
                'Employee',
                'employee',
                resource: EmployeeResource::class
            )->translatable('resource.employee'),
            Date::make(
                'Passed at',
                'passed_at'
            )->translatable('resource.training_program.event'),
            Date::make(
                'Expired at',
                'expired_at'
            )->translatable('resource.training_program.event'),
        ];
    }
}
