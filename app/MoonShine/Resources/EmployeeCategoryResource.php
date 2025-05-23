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
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<EmployeeCategory>
 */
class EmployeeCategoryResource extends ModelResource implements HasImportExportContract
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
            Text::make('Name')->sortable()->translatable('resource'),
            Text::make('Description')->translatable('resource'),
            HasMany::make(
                'Employees',
                'employees',
                resource: EmployeeResource::class
            )->relatedLink()->translatable('resource.employee'),
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

    use ImportExportConcern;

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name')->translatable('resource'),
            Text::make('Description')->translatable('resource'),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name')->translatable('resource'),
            Text::make('Description')->translatable('resource'),
        ];
    }
}
