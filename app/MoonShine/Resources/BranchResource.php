<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;

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
 * @extends ModelResource<Branch>
 */
class BranchResource extends ModelResource implements HasImportExportContract
{
    protected string $model = Branch::class;

    protected string $column = 'name';

    protected string $sortColumn = 'id';

    protected SortDirection $sortDirection = SortDirection::ASC;
    
    public function getTitle(): string
    {
        return __('resource.branch.Branches');
    }

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
     * @param Branch $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
        ];
    }

    use ImportExportConcern;

    protected function importFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->translatable('resource'),
            Text::make('Description')->translatable('resource'),
        ];
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->translatable('resource'),
            Text::make('Description')->translatable('resource'),
        ];
    }
}
