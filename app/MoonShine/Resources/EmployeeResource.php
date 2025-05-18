<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Employee>
 */
class EmployeeResource extends ModelResource
{
    protected string $model = Employee::class;

    protected string $title = 'Employees';
    
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Full name')->sortable(),
            Text::make('Position')->sortable(),
            BelongsTo::make(
                'Category',
                'category',
                resource: EmployeeCategoryResource::class
            )->sortable(),
            BelongsTo::make(
                'Branch',
                'branch',
                resource: BranchResource::class
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
                Text::make('Full name'),
                Text::make('Position')->sortable(),
                BelongsTo::make(
                    'Category',
                    'category',
                    resource: EmployeeCategoryResource::class
                )->sortable(),
                BelongsTo::make(
                    'Branch',
                    'branch',
                    resource: BranchResource::class
                )->sortable(),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return $this->formFields();
    }

    /**
     * @param Employee $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'full_name' => ['required', 'string', 'min:3'],
            'position' => ['required', 'string', 'min:5']
        ];
    }
}
