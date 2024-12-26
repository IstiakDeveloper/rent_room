<div class="container mt-5">


    <!-- User Role Content -->
    @role('User')
        @if ($bookings && count($bookings))
            <div class="row">
                @foreach ($bookings as $booking)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div
                                class="card-header {{ $booking['payment_status'] === 'cancelled' ? 'bg-danger' : ($booking['payment_status'] === 'pending' ? 'bg-warning' : 'bg-primary') }} text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    Booking #{{ $booking['id'] }} - {{ $booking['package']['name'] }}
                                    @if ($booking['payment_status'] === 'cancelled')
                                        <span class="badge bg-light text-danger ms-2">Cancelled</span>
                                    @endif
                                </h5>
                                <div class="btn-group">
                                    @if ($booking['payment_status'] !== 'cancelled')
                                    @else
                                        <button class="btn btn-light btn-sm" disabled>
                                            <i class="fas fa-ban mr-1"></i> Booking Cancelled
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                @if ($booking['payment_status'] === 'cancelled')
                                    <div class="alert alert-danger mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle fs-4 mr-2"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">Booking Cancelled</h6>
                                                <p class="mb-0 small">This booking has been cancelled and is no
                                                    longer active.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                {{-- Booking Details Section --}}
                                <div class="mb-4">
                                    <h6 class="text-muted border-bottom pb-2">Booking Details</h6>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong><i class="far fa-calendar-alt mr-2"></i>From:</strong>
                                                {{ \Carbon\Carbon::parse($booking['from_date'])->format('M d, Y') }}
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="far fa-calendar-alt mr-2"></i>To:</strong>
                                                {{ \Carbon\Carbon::parse($booking['to_date'])->format('M d, Y') }}
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="far fa-clock mr-2"></i>Duration:</strong>
                                                {{ $booking['number_of_days'] }} days
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong><i class="fas fa-tag mr-2"></i>Price Type:</strong>
                                                {{ $booking['price_type'] }}
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="fas fa-money-bill mr-2"></i>Payment
                                                    Option:</strong>
                                                {{ str_replace('_', ' ', ucfirst($booking['payment_option'])) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Progress Section --}}
                                <div class="mb-4">
                                    <h6 class="text-muted border-bottom pb-2">Payment Summary</h6>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr class="table-light">
                                                    <td colspan="2">
                                                        <strong><i class="fas fa-calendar mr-1"></i>Initial
                                                            Charges</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">Base Price:</td>
                                                    <td class="text-end">£{{ number_format($booking['price'], 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">Booking Fee:</td>
                                                    <td class="text-end">
                                                        £{{ number_format($booking['booking_price'], 2) }}</td>
                                                </tr>

                                                @if (isset($booking['bookingPayments']) && count($booking['bookingPayments']) > 0)
                                                @else
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">
                                                            <i class="fas fa-info-circle mr-1"></i>No milestone
                                                            payments set
                                                        </td>
                                                    </tr>
                                                @endif

                                                <tr class="border-top table-light">
                                                    <td><strong>Payment Overview</strong></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">Total Amount:</td>
                                                    <td class="text-end">
                                                        £{{ number_format($booking['payment_summary']['total_price'], 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3">Amount Paid:</td>
                                                    <td class="text-end text-success">
                                                        £{{ number_format($booking['payment_summary']['total_paid'], 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td class="ps-3"><strong>Remaining Balance:</strong></td>
                                                    <td class="text-end">
                                                        <strong
                                                            class="text-{{ $booking['payment_summary']['remaining_balance'] > 0 ? 'danger' : 'success' }}">
                                                            £{{ number_format($booking['payment_summary']['remaining_balance'], 2) }}
                                                        </strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-3"><strong>Status:</strong></td>
                                                    <td class="text-end">
                                                        <span
                                                            class="badge bg-{{ $booking['payment_summary']['remaining_balance'] == 0 ? 'success' : 'warning' }} px-3">
                                                            {{ $booking['payment_summary']['remaining_balance'] == 0 ? 'Fully Paid' : 'Payment Pending' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        @if ($booking['payment_summary']['remaining_balance'] > 0)
                                            <div class="alert alert-info mt-3 mb-0">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                @if ($booking['price_type'] === 'Month')
                                                    Monthly payments are due at the start of each month
                                                @elseif($booking['price_type'] === 'Week')
                                                    Weekly payments are due at the start of each week
                                                @else
                                                    Daily payments are due at the start of each day
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Payment Summary Section --}}
                                @if ($booking['payment_status'] !== 'cancelled')
                                    <div class="mb-4">
                                        <h6 class="text-muted border-bottom pb-2">Payment History</h6>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Amount</th>
                                                        <th>Payment Method</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($booking['payments'] as $index => $payment)
                                                        <tr
                                                            class="{{ $payment['status'] === 'Paid' ? 'bg-success text-white' : '' }}">
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>£{{ number_format($payment['amount'], 2) }}</td>
                                                            <td>{{ ucfirst($payment['payment_method']) }}</td>
                                                            <td>{{ ucfirst($payment['status']) }}</td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                No bookings found for this user.
            </div>
        @endif

        {{-- <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm p-4">
                    <div class="d-flex justify-content-between">
                        <h4>Payment Details</h4>
                    </div>

                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Details</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr
                                    class="{{ $payment->payment_status === 'Paid' ? 'table-success' : ($payment->payment_status === 'Pending' ? 'table-warning' : '') }}">
                                    <td>{{ $payment->payment_date }}</td>
                                    <td>{{ $payment->details }}</td>
                                    <td>{{ $payment->amount }}</td>
                                    <td>{{ $payment->payment_status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div> --}}
        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm p-4">
                    <h3>Upload Documents</h3>
                    <form wire:submit.prevent="saveDocuments">
                        @foreach ($documents as $index => $document)
                            <div class="card mt-2 p-3">
                                <div class="form-group">
                                    <label>Person Name</label>
                                    <input type="text" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.person_name">
                                    @error('documents.' . $index . '.person_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label>Passport (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.passport">
                                    @error('documents.' . $index . '.passport')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label>NID/Other (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.nid_or_other">
                                    @error('documents.' . $index . '.nid_or_other')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label>Payslip/Other (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.payslip">
                                    @error('documents.' . $index . '.payslip')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label>Student/Employee Card (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.student_card">
                                    @error('documents.' . $index . '.student_card')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($index > 0)
                                    <button type="button" class="btn btn-danger mt-2"
                                        wire:click="removePerson({{ $index }})">Remove Person</button>
                                @endif
                            </div>
                        @endforeach

                        <button type="button" class="btn btn-success mt-3" wire:click="addPerson">Add Person</button>
                        <br>
                        <button type="submit" class="btn btn-primary mt-3">Save Documents</button>
                    </form>

                    @if (session()->has('message'))
                        <div class="alert alert-success mt-3">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
        @endrole


        <!-- Partner Role Content -->
        @role('Partner')

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm p-4 mb-2">
                    <h2 class="h4">Partner Documents</h2>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="proof_path_gas_certificate" class="form-label">Gas Certificate</label>
                            <input type="hidden" name="proof_type_1" value="Gas Certificate">
                            <input type="file" class="form-control" id="proof_path_1" name="proof_path_1">
                            @error('proof_path_1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="proof_path_electric_certificate" class="form-label">Electric Certificate</label>
                            <input type="hidden" name="proof_type_2" value="Electric Certificate">
                            <input type="file" class="form-control" id="proof_path_2" name="proof_path_2">
                            @error('proof_path_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="proof_path_3" class="form-label">Landlord Certificate (HMO/Other)</label>
                            <input type="hidden" name="proof_type_3" value="Landlord Certificate (HMO/Other)">
                            <input type="file" class="form-control" id="proof_path_3" name="proof_path_3">
                            @error('proof_path_3')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="proof_path_4" class="form-label">Building Insurance Certificate</label>
                            <input type="hidden" name="proof_type_4" value="Building Insurance Certificate">
                            <input type="file" class="form-control" id="proof_path_4" name="proof_path_4">
                            @error('proof_path_4')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm p-4">
                    <h4>Bank Details</h4>
                    <form wire:submit.prevent="saveBankDetails">
                        <div class="form-group mt-2">
                            <input type="text" class="form-control" placeholder="name" wire:model="bankDetail.name">
                        </div>
                        <div class="form-group mt-2">
                            <input type="text" class="form-control" placeholder="Sort Code"
                                wire:model="bankDetail.sort_code">
                        </div>
                        <div class="form-group mt-2">
                            <input type="text" class="form-control" placeholder="Account"
                                wire:model="bankDetail.account">
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Save Bank Details</button>

                        @if (session()->has('message'))
                            <div class="alert alert-success mt-2">
                                {{ session('message') }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm p-4">
                    <h4>Agreement Details</h4>
                    <form wire:submit.prevent="saveAgreement">
                        <div class="form-group mt-2">
                            <input type="text" class="form-control" placeholder="Agreement Type"
                                wire:model="agreementDetail.agreement_type">
                        </div>
                        <div class="form-group mt-2">
                            <input type="text" class="form-control" placeholder="Duration"
                                wire:model="agreementDetail.duration">
                        </div>
                        <div class="form-group mt-2">
                            <input type="number" step="0.01" class="form-control" placeholder="Amount"
                                wire:model="agreementDetail.amount">
                        </div>
                        <div class="form-group mt-2">
                            <input type="number" step="0.01" class="form-control" placeholder="Deposit"
                                wire:model="agreementDetail.deposit">
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Save Agreement</button>

                        @if (session()->has('message'))
                            <div class="alert alert-success mt-2">
                                {{ session('message') }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @endrole

        <!-- Shared Content -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm p-4">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm p-4">
                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>

    <div class="mt-5">

        @role('User')
            <div class="mt-5">
                <h4>Uploaded Documents</h4>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Person Name</th>
                            <th>Passport</th>
                            <th>NID/Other</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->documents as $index => $document)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $document->person_name }}</td>
                                <td>
                                    @if ($document->passport)
                                        <a href="{{ Storage::url($document->passport) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank">Download Passport</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($document->nid_or_other)
                                        <a href="{{ Storage::url($document->nid_or_other) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank">Download NID/Other</a>
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>
                                    @if ($document->payslip)
                                        <a href="{{ Storage::url($document->payslip) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank">Download Payslip/Other</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($document->student_card)
                                        <a href="{{ Storage::url($document->student_card) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank">Download Student/Employee
                                            Card</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        wire:click="editDocument({{ $document->id }})">Edit</button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="deleteDocument({{ $document->id }})">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No documents uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endrole

        @role('Partner')
            <div class="mt-5 shadow-sm p-2">
                <h4>Uploaded Documents</h4>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Document Type</th>
                            <th>File</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $documents = [
                                ['type' => Auth::user()->proof_type_1, 'path' => Auth::user()->proof_path_1],
                                ['type' => Auth::user()->proof_type_2, 'path' => Auth::user()->proof_path_2],
                                ['type' => Auth::user()->proof_type_3, 'path' => Auth::user()->proof_path_3],
                                ['type' => Auth::user()->proof_type_4, 'path' => Auth::user()->proof_path_4],
                            ];
                        @endphp

                        @foreach ($documents as $index => $document)
                            @if ($document['path'])
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $document['type'] }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $document['path']) }}"
                                            class="btn btn-primary btn-sm" download>Download</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if (!array_filter($documents, fn($doc) => $doc['path']))
                            <tr>
                                <td colspan="4" class="text-center">No documents uploaded yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>


            @if (Auth::user()->partner_bank_details)
                <div class="card mt-4">
                    <div class="card-body">
                        <strong>Bank Details:</strong>
                        <p>{{ Auth::user()->partner_bank_details }}</p>
                        <button class="btn btn-secondary btn-sm" wire:click="editBankDetails">Edit</button>
                    </div>
                </div>
            @endif
        @endrole
    </div>



    <!-- Edit Document Modal -->
    @if ($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" aria-labelledby="editDocumentModalLabel"
            style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDocumentModalLabel">Edit Document</h5>
                        <button type="button" class="btn-close" wire:click="closeEditModal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="updateDocument">
                            <div class="mb-3">
                                <label for="editPersonName" class="form-label">Person Name</label>
                                <input type="text" class="form-control" id="editPersonName"
                                    wire:model="editPersonName" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPassport" class="form-label">Passport</label>
                                <input type="file" class="form-control" id="editPassport"
                                    wire:model="editPassport">
                                @if ($editPassport)
                                    <small class="text-muted">New file selected:
                                        {{ $editPassport->getClientOriginalName() }}</small>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="editNidOrOther" class="form-label">NID/Other</label>
                                <input type="file" class="form-control" id="editNidOrOther"
                                    wire:model="editNidOrOther">
                                @if ($editNidOrOther)
                                    <small class="text-muted">New file selected:
                                        {{ $editNidOrOther->getClientOriginalName() }}</small>
                                @endif
                            </div>

                            <!-- New Payslip Field -->
                            <div class="mb-3">
                                <label for="editPayslip" class="form-label">Payslip</label>
                                <input type="file" class="form-control" id="editPayslip"
                                    wire:model="editPayslip">
                                @if ($editPayslip)
                                    <small class="text-muted">New file selected:
                                        {{ $editPayslip->getClientOriginalName() }}</small>
                                @endif
                            </div>

                            <!-- New Student Card Field -->
                            <div class="mb-3">
                                <label for="editStudentCard" class="form-label">Student Card</label>
                                <input type="file" class="form-control" id="editStudentCard"
                                    wire:model="editStudentCard">
                                @if ($editStudentCard)
                                    <small class="text-muted">New file selected:
                                        {{ $editStudentCard->getClientOriginalName() }}</small>
                                @endif
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    wire:click="closeEditModal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
