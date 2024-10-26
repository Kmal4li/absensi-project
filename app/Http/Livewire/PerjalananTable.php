<?php

namespace App\Http\Livewire;

use App\Models\Perjalanan;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button, Column, Detail, Exportable, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};

final class PerjalananTable extends PowerGridComponent
{
    use ActionButton;

    // Table sort field
    public string $sortField = 'perjalanans.created_at';
    public string $sortDirection = 'desc';

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'bulkCheckedDelete',
            ]
        );
    }

    public function header(): array
    {
        return [
            Button::add('bulk-checked')
                ->caption(__('Hapus'))
                ->class('btn btn-danger border-0')
                ->emit('bulkCheckedDelete', []),
        ];
    }

    public function bulkCheckedDelete()
    {
        if (auth()->check()) {
            $ids = $this->checkedValues();

            if (!$ids) {
                return $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => 'Pilih data yang ingin dihapus terlebih dahulu.']);
            }

            try {
                Perjalanan::whereIn('id', $ids)->delete();
                $this->dispatchBrowserEvent('showToast', ['success' => true, 'message' => 'Data perjalanan berhasil dihapus.']);
            } catch (QueryException $ex) {
                $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => 'Data gagal dihapus, kemungkinan ada data lain yang menggunakan data tersebut.']);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Features Setup
    |--------------------------------------------------------------------------
    */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Datasource
    |--------------------------------------------------------------------------
    */
    public function datasource(): Builder
    {
        return Perjalanan::query();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship Search
    |--------------------------------------------------------------------------
    */
    public function relationSearch(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Add Columns
    |--------------------------------------------------------------------------
    */
    public function addColumns(): PowerGridEloquent
{
    return PowerGrid::eloquent()
        ->addColumn('id')
        ->addColumn('title')
        ->addColumn('date_start', fn (Perjalanan $model) => Carbon::parse($model->date_start)->format('d/m/Y'))
        ->addColumn('start_time', fn (Perjalanan $model) => substr($model->start_time, 0, -3))
        ->addColumn('date_end', fn (Perjalanan $model) => Carbon::parse($model->date_end)->format('d/m/Y'))
        ->addColumn('end_time', fn (Perjalanan $model) => substr($model->end_time, 0, -3))
        ->addColumn('created_at')
        ->addColumn('created_at_formatted', fn (Perjalanan $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'))
        ->addColumn('laporan_keuangan', function (Perjalanan $model) {
            return $model->getMedia('files')->isNotEmpty() ? 'Laporan Tersedia' : 'Tidak ada laporan';
        });
}

    /*
    |--------------------------------------------------------------------------
    | PowerGrid Columns
    |--------------------------------------------------------------------------
    */
    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Nama Perjalanan', 'title')
                ->searchable()
                ->makeInputText('title')
                ->sortable(),

            Column::make('Tanggal Mulai', 'date_start')
                ->searchable()
                ->makeInputText('date_start')
                ->sortable(),

            Column::make('Waktu Mulai', 'start_time')
                ->searchable()
                ->makeInputText('start_time')
                ->sortable(),

            Column::make('Tanggal Selesai', 'date_end')
                ->searchable()
                ->makeInputText('date_end')
                ->sortable(),

            Column::make('Waktu Selesai', 'end_time')
                ->searchable()
                ->makeInputText('end_time')
                ->sortable(),

            Column::make('Laporan Keuangan', 'laporan_keuangan')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->makeInputDatePicker()
                ->searchable(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Action Buttons
    |--------------------------------------------------------------------------
    */
    public function actions(): array
{
    return [
        Button::make('edit', 'Edit')
            ->class('badge text-bg-success')
            ->target('')
            ->route('perjalanan.edit', ['id' => 'id']),

        Button::make('download', 'Download Laporan')
            ->class('btn btn-primary')
            ->target('_blank')  
            ->route('perjalanan.downloadLaporan', ['id' => 'id'])
            
    ];
}

    /*
    |--------------------------------------------------------------------------
    | Action Rules
    |--------------------------------------------------------------------------
    */

    // Add action rules if necessary
}