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
                                                        <i class="fas fa-user-circle text-primary mr-1"></i>
                                                        <strong>{{ $booking->user->name }}</strong>
                                                    </div>
                                                    <div class="small text-muted ms-3">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
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
                                                            <span class="badge bg-info mr-1 text-white">
                                                                <i class="fas fa-bed mr-1"></i>
                                                                {{ $room->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($package->bookings->count() > 2)
                                                <small class="text-primary">
                                                    <i class="fas fa-plus-circle mr-1"></i>
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
        <!-- Assignment Modal -->
        <div class="modal fade {{ $showAssignModal ? 'show' : '' }}" tabindex="-1" role="dialog"
            style="display: {{ $showAssignModal ? 'block' : 'none' }}; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus text-primary mr-2"></i>
                            Assign Partner to Package
                        </h5>
                        <button type="button" class="close" wire:click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="assignUser">
                        <div class="modal-body">
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="font-weight-bold mb-2">
                                    <i class="fas fa-user text-muted mr-2"></i>
                                    Select Partner
                                </label>
                                <select wire:model="selectedUserId"
                                    class="form-control @error('selectedUserId') is-invalid @enderror">
                                    <option value="">Choose a partner...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedUserId')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary text-white" wire:click="closeModal">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary text-white">
                                <i class="fas fa-check mr-2"></i>
                                Confirm Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('livewire:load', function() {
                    Livewire.on('closeModal', () => {
                        document.body.classList.remove('modal-open');
                    });

                    Livewire.on('modalOpened', () => {
                        document.body.classList.add('modal-open');
                    });
                });
            </script>
        @endpush

        <style>
            .modal {
                padding-right: 17px;
            }

            .modal-open {
                overflow: hidden;
            }

            .modal-backdrop {
                opacity: 0.5;
            }

            .modal-content {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                border: none;
                border-radius: 0.5rem;
            }

            .modal-header {
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
            }

            .modal-footer {
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
            }

            .form-control {
                border-radius: 0.25rem;
                padding: 0.5rem 0.75rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            .form-control:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
        </style>
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
