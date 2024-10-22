<div>
    <form wire:submit.prevent="save">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" id="title" wire:model.defer="title" class="form-control" required>
            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="date_start" class="form-label">Start Date</label>
            <input type="date" id="date_start" wire:model.defer="date_start" class="form-control" required>
            @error('date_start') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" id="start_time" wire:model.defer="start_time" class="form-control" required>
            @error('start_time') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="date_end" class="form-label">End Date</label>
            <input type="date" id="date_end" wire:model.defer="date_end" class="form-control" required>
            @error('date_end') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" id="end_time" wire:model.defer="end_time" class="form-control" required>
            @error('end_time') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="file_perjalanan" class="form-label">File Perjalanan (PDF)</label>
            <input type="file" id="file_perjalanan" wire:model="file_perjalanan" class="form-control" accept="application/pdf">
            @error('file_perjalanan') <span class="text-danger">{{ $message }}</span> @enderror
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
