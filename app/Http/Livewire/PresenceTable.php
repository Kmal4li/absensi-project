<?php

namespace App\Http\Livewire;

use App\Models\Presence;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button, Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};

final class PresenceTable extends PowerGridComponent
{
    use ActionButton;

    public $attendanceId;
    public string $sortField = 'presences.created_at';
    public string $sortDirection = 'desc';
    protected $listeners = ['photoSaved' => '$refresh'];

    /*
    |---------------------------------------------------------------------------
    | Features Setup
    |---------------------------------------------------------------------------
    | Setup Table's general features
    |
    */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /*
    |---------------------------------------------------------------------------
    | Datasource
    |---------------------------------------------------------------------------
    | Provides data to your Table using a Model or Collection
    |
    */
    public function datasource(): Builder
    {
        return Presence::query()
            ->where('attendance_id', $this->attendanceId)
            ->join('users', 'presences.user_id', '=', 'users.id')
            ->select('presences.*', 'users.name as user_name');
    }

    /*
    |---------------------------------------------------------------------------
    | Relationship Search
    |---------------------------------------------------------------------------
    | Configure here relationships to be used by the Search and Table Filters.
    |
    */
    public function relationSearch(): array
    {
        return [];
    }

    /*
    |---------------------------------------------------------------------------
    | Add Column
    |---------------------------------------------------------------------------
    | Make Datasource fields available to be used as columns.
    | You can pass a closure to transform/modify the data.
    |
    */
    public function addColumns(): PowerGridEloquent
{
    return PowerGrid::eloquent()
        ->addColumn('id')
        ->addColumn('user_name')
        ->addColumn("presence_date")
        ->addColumn("presence_enter_time")
        ->addColumn("presence_out_time", fn (Presence $model) => $model->presence_out_time ?? '<span class="badge text-bg-danger">Belum Absensi Pulang</span>')
        ->addColumn("is_permission", fn (Presence $model) => $model->is_permission ? 
            '<span class="badge text-bg-warning">Izin</span>' : '<span class="badge text-bg-success">Hadir</span>')
            ->addColumn('photo', function (Presence $model) {
                if ($model->photoData) {
                    $url = asset('storage/photos/' . $photoData->photoData);
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-eye">Lihat Foto</i></a>';
                } else {
                    return '<span class="text-muted">Tidak Ada Foto</span>';
                }
            })
        ->addColumn('created_at')
        ->addColumn('created_at_formatted', fn (Presence $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
}
    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Nama', 'user_name')
                ->searchable()
                ->makeInputText('users.name')
                ->sortable(),

            Column::make('Tanggal Hadir', 'presence_date')
                ->makeInputDatePicker()
                ->searchable()
                ->sortable(),

            Column::make('Jam Absen Masuk', 'presence_enter_time')
                ->searchable()
                ->makeInputText('presence_enter_time')
                ->sortable(),

            Column::make('Jam Absen Pulang', 'presence_out_time')
                ->searchable()
                ->makeInputText('presence_out_time')
                ->sortable(),

            Column::make('Status', 'is_permission')
                ->sortable(),

            Column::make('Foto', 'photo')
                ->searchable()
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->hidden(),

            Column::make('Created at', 'created_at_formatted')
                ->makeInputDatePicker()
                ->searchable()
        ];
    }
}