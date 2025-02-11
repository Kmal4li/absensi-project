<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="mb-3">
            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
            <input type="text" id="nama_kegiatan" class="form-control" wire:model="nama_kegiatan">
            @error('nama_kegiatan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi_kegiatan" class="form-label">Deskripsi</label>
            <textarea id="deskripsi_kegiatan" class="form-control" wire:model="deskripsi_kegiatan"></textarea>
            @error('deskripsi_kegiatan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-3">
            <x-form-label id="users" label="Pilih Karyawan" />
            <div class="row ms-1">
                @foreach ($users as $user)
                    <div class="form-check col-sm-4">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            value="{{ $user->id }}"
                            wire:model.defer="user_id"  
                            id="flexCheckuser{{ $loop->index }}">

                        <label class="form-check-label" for="flexCheckuser{{ $loop->index }}">
                            {{ $user->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <small class="text-muted d-block mt-1">Pilih karyawan yang akan mengikuti perjalanan dinas ini.</small>
            <x-form-error key="user_id" />
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                Simpan
            </button>
            <button class="btn btn-light" type="button" wire:click="addPositionInput" wire:loading.attr="disabled">
                Tambah Input
            </button>
        </div>
    </form>
</div>
