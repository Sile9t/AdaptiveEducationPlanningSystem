<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\EmployeeCategory;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Employee>
 */
class EmployeeResource extends ModelResource implements HasImportExportContract
{
    protected string $model = Employee::class;

    protected string $title = 'Employees';
    
    protected string $column = 'full_name';

    protected string $sortColumn = 'id';

    protected SortDirection $sortDirection = SortDirection::ASC;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Personnel number')->translatable('resource.user'),
            Text::make('Full name')->sortable()->translatable('resource.employee'),
            Text::make('Position')->sortable()->translatable('resource.employee'),
            BelongsTo::make(
                'Category',
                'employeeCategory',
                resource: EmployeeCategoryResource::class
            )->sortable()->creatable()->translatable('resource.employee_category'),
            BelongsTo::make(
                'Branch',
                'branch',
                resource: BranchResource::class
            )->sortable()->creatable()->translatable('resource.branch'),
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
        return $this->formFields();
    }

    protected function search(): array
    {
        return ['id', 'personnel_number', 'full_name', 'position', 'category.title', 'branch.name' ];
    }
    
    protected bool $saveQueryState = true;

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            Text::make('Personnel number')->translatable('resource.user'),
            Text::make('Full name')->translatable('resource.employee'),
            Text::make('Position')->translatable('resource.employee'),
            BelongsTo::make(
                'Category',
                'employeeCategory',
                resource: EmployeeCategoryResource::class
            )->nullable()->searchable()->translatable('resource.employee_category'),
            BelongsTo::make(
                'Branch',
                'branch',
                resource: BranchResource::class
            )->nullable()->searchable()->translatable('resource.branch'),
        ];
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

    use ImportExportConcern;

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Personnel number'),
            Text::make('Full name'),
            Text::make('Position'),
            BelongsTo::make(
                'Category',
                'employeeCategory',
                resource: EmployeeCategoryResource::class
            )->creatable()
            ->fromRaw(function($raw, $ctx) {
                $category = EmployeeCategory::where('name', $raw)->first();

                if (!isset($category)) {
                    $category = new EmployeeCategory();
                    $category->name = $raw;
                    $category->save();
                }

                return $category->id;
            }),
            BelongsTo::make(
                'Branch',
                'branch',
                resource: BranchResource::class
            )->creatable()
            ->fromRaw(function($raw, $ctx) {
                $branch = Branch::where('', $raw)->first();

                if (!isset($branch)) {
                    $branch = new Branch();
                    $branch->name = $raw;
                    $branch->save();
                }

                return $branch->id;
            }),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Personnel number')->translatable('resource.user'),
            Text::make('Full name')->translatable('resource.employee'),
            Text::make('Position')->translatable('resource.employee'),
            BelongsTo::make(
                'Category',
                'employeeCategory',
                resource: EmployeeCategoryResource::class
            )->creatable()->translatable('resource.employee_category')
            ->modifyRawValue(fn($value, $model) => $model->employeeCategory->name),
            BelongsTo::make(
                'Branch',
                'branch',
                resource: BranchResource::class
            )->creatable()->translatable('resource.branch')
            ->modifyRawValue(fn($value, $model) => $model->branch->name),
        ];
    }
}
