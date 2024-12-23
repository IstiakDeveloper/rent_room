<div class="container-fluid">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Package Management</h4>
        <a class="btn btn-primary" href="{{ route('admin.packages.create') }}">
            <i class="fas fa-plus mr-2"></i>Create Package
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Package Name</th>
                            <th>Address</th>
                            <th>Created By</th>
                            <th>Current Bookings</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            @php
                                $today = \Carbon\Carbon::now();
                                $expirationDate = $package->expiration_date
                                    ? \Carbon\Carbon::parse($package->expiration_date)
                                    : null;
                                $isExpired =
                                    $package->status === 'expired' ||
                                    ($expirationDate && $today->greaterThanOrEqualTo($expirationDate));
                            @endphp
                            <tr class="{{ $isExpired ? 'table-danger' : '' }}">
                                <td>{{ $package->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-primary mr-2"></i>
                                        <strong>{{ $package->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-secondary mr-2"></i>
                                        {{ $package->address }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light p-2 mr-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        {{ $package->user->name }}
                                    </div>
                                </td>
                                <td>
                                    @if ($package->bookings->isEmpty())
                                        <span class="text-muted">No current bookings</span>
                                    @else
                                        <div class="d-flex flex-column">
                                            @foreach ($package->bookings->take(2) as $booking)
                                                <div class="mb-2 border-bottom pb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-user-circle text-primary me-1"></i>
                                                        <strong>{{ $booking->user->name }}</strong>
                                                    </div>
                                                    <div class="small text-muted ms-3">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($booking->from_date)->format('d M') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($booking->to_date)->format('d M') }}
                                                    </div>
                                                    <div class="ms-3 mt-1">
                                                        @php
                                                            $roomIds = json_decode($booking->room_ids, true) ?? [];
                                                            $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                                        @endphp
                                                        @foreach ($rooms as $room)
                                                            <span class="badge bg-info me-1">
                                                                <i class="fas fa-bed me-1"></i>
                                                                {{ $room->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($package->bookings->count() > 2)
                                                <small class="text-primary">
                                                    <i class="fas fa-plus-circle me-1"></i>
                                                    {{ $package->bookings->count() - 2 }} more bookings
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if (!$isExpired)
                                        <div class="btn-group">
                                            <a href="{{ route('packages.show', ['packageId' => $package->id]) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.package.edit', ['packageId' => $package->id]) }}"
                                                class="btn btn-sm btn-outline-info" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="delete({{ $package->id }})"
                                                class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @role('Super Admin')
                                                <button wire:click="openAssignModal({{ $package->id }})"
                                                    class="btn btn-sm btn-outline-warning" title="Assign User">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endrole
                                        </div>
                                    @else
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($showAssignModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign User to Package</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="assignUser">
                            <div class="mb-3">
                                <label class="form-label">Select User</label>
                                <select wire:model="selectedUserId" class="form-select">
                                    <option value="">Choose a user...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedUserId')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Assign User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .table th {
            font-weight: 600;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }

        .modal {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
@endpush
