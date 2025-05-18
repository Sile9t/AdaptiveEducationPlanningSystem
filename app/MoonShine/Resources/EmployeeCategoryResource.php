<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeCategory;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<EmployeeCategory>
 */
class EmployeeCategoryResource extends ModelResource
{
    protected string $model = EmployeeCategory::class;

    protected string $title = 'EmployeeCategories';

    protected string $column = 'name';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable(),
            Text::make('Description'),
            HasMany::make(
                'Employees',
                'employees',
                resource: EmployeeResource::class
            )->relatedLink(),
            HasMany::make(
                'Permits',
                'permits',
                resource: PermitResource::class
            )->relatedLink(),
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
     * @param EmployeeCategory $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => ['required', 'unique:employee_categories,name', 'string', 'min:3'],
            'description' => ['string', 'min:3'],
        ];
    }
}
