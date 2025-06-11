<?php

namespace TypiCMS\Modules\Core\Exports;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\User;

/**
 * @implements WithMapping<mixed>
 */
class UsersExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
{
    /** @return Collection<int, Model> */
    public function collection(): Collection
    {
        return QueryBuilder::for(User::class)
            ->allowedSorts(['first_name', 'last_name', 'email', 'subscription_plan', 'subscription_ends_at', 'last_payment_at', 'superuser'])
            ->allowedFilters([
                AllowedFilter::custom('first_name,last_name,email', new FilterOr()),
            ])
            ->get();
    }

    /** @return array<int, mixed> */
    public function map(mixed $model): array
    {
        return [
            Date::dateTimeToExcel($model->created_at),
            Date::dateTimeToExcel($model->updated_at),
            $model->last_name,
            $model->first_name,
            $model->email,
            $model->phone,
            $model->street,
            $model->number,
            $model->box,
            $model->postal_code,
            $model->city,
            $model->country,
            $model->locale,
            $model->privacy_policy_accepted,
        ];
    }

    /** @return string[] */
    public function headings(): array
    {
        return [
            __('Created at'),
            __('Updated at'),
            __('Last name'),
            __('First name'),
            __('Email'),
            __('Phone'),
            __('Street'),
            __('Number'),
            __('Box'),
            __('Postal code'),
            __('City'),
            __('Country'),
            __('Locale'),
            __('Privacy policy accepted'),
        ];
    }

    /** @return array<string, string> */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DATETIME,
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
