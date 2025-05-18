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
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Number;

/**
 * @extends ModelResource<Permit>
 */
class PermitResource extends ModelResource
{
    protected string $model = Permit::class;

    protected string $title = 'Permits';
    
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
                'Category',
                'category',
                resource: EmployeeCategoryResource::class
            )->sortable(),
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
}
