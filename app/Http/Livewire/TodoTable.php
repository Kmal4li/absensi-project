<?php

namespace App\Http\Livewire;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\{Button, Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;

final class TodoTable extends PowerGridComponent
{
    use ActionButton;

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'bulkCheckedDelete',
                'bulkCheckedEdit'
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
            Button::add('bulk-edit-checked')
                ->caption(__('Edit'))
                ->class('btn btn-success border-0')
                ->emit('bulkCheckedEdit', []),
        ];
    }

    public function bulkCheckedDelete()
    {
        if (auth()->check()) {
            $ids = $this->checkedValues();

            if (!$ids)
                return $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => 'Pilih data yang ingin dihapus terlebih dahulu.']);

            try {
                Todo::whereIn('id', $ids)->delete();
                $this->dispatchBrowserEvent('showToast', ['success' => true, 'message' => 'Data kegiatan berhasil dihapus.']);
            } catch (\Illuminate\Database\QueryException $ex) {
                $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => 'Data gagal dihapus, kemungkinan ada data lain yang menggunakan data tersebut.']);
            }
        }
    }

    public function bulkCheckedEdit()
    {
        if (auth()->check()) {
            $ids = $this->checkedValues();

            if (!$ids)
                return $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => 'Pilih data yang ingin diedit terlebih dahulu.']);

            $ids = join('-', $ids);
            return $this->dispatchBrowserEvent('redirect', ['url' => route('Todos.edit', ['ids' => $ids])]);
        }
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
{
    return Todo::query()->latest();
}
    

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('nama_kegiatan')
            ->addColumn('deskripsi_kegiatan')
            ->addColumn('tanggal_kegiatan')
            ->addColumn('status_kegiatan', fn (Todo $model) => $model->status_kegiatan === 'Selesai' ? 'Selesai' : 'Belum Diselesaikan' ?? 'Tidak Diketahui')
            ->addColumn('created_at_formatted', fn (Todo $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Nama Kegiatan', 'nama_kegiatan')
                ->searchable()
                ->makeInputText()
                ->sortable(),

            Column::make('Deskripsi Kegiatan', 'deskripsi_kegiatan')
                ->searchable()
                ->makeInputText()
                ->sortable(),

            Column::make('Tanggal Kegiatan', 'tanggal_kegiatan')
                ->makeInputDatePicker()
                ->sortable(),

            Column::make('Status Kegiatan', 'status_kegiatan')
                ->makeInputText()
                ->sortable(),
        ];
    }
}
