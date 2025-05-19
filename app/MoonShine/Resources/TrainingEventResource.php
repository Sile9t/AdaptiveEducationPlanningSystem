<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingEvent;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;

/**
 * @extends ModelResource<TrainingEvent>
 */
class TrainingEventResource extends ModelResource
{
    protected string $model = TrainingEvent::class;

    protected string $title = 'TrainingEvents';

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
            )->sortable(),
            BelongsTo::make(
                'Employee',
                'employee',
                resource: EmployeeResource::class
            )->sortable(),
            Date::make(
                'Passed at',
                'passed_at'
            )->sortable(),
            Date::make(
                'Expired at',
                'expired_at'
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
                'program',
                resource: TrainingProgramResource::class
            )->nullable(),
            BelongsTo::make(
                'Employee',
                'employee',
                resource: EmployeeResource::class
            )->nullable(),
            DateRange::make(
                'Passed at',
                'passed_at'
            ),
            DateRange::make(
                'Expired at',
                'expired_at'
            ),
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
}
