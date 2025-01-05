<div class="container mt-5">
    <div class="row">
        @if ($user->packages && $user->hasRole('Partner'))
            <div class="col-6 col-lg-6">
            @else
                <div class="col-12 col-lg-12">
        @endif
        <div class="card shadow-sm p-4 mb-4">
            <h3>User Information</h3>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone Number:</strong> {{ $user->phone }}</p>

            <button class="btn btn-primary" wire:click="openEditModal">Edit</button>
        </div>
    </div>
    @if ($user->packages && $user->packages->isNotEmpty())
        <div class="col-6 col-lg-6">
            <div class="card shadow-sm p-4 mb-4">
                <h3 class="mb-3">Assigned Packages</h3>
                @foreach ($user->packages as $package)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="{{ route('packages.show', $package->id) }}" class="text-decoration-none text-primary">
                            {{ $package->name }}
                        </a>
                        <a href="{{ route('packages.show', $package->id) }}" class="btn btn-outline-primary btn-sm">
                            View Details
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif



    @if ($user->hasRole('User'))
        <div class="container-fluid">
            {{-- Booking List --}}
            @if ($bookings && count($bookings))
                <div class="row">
                    @foreach ($bookings as $booking)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div
                                    class="card-header
                                {{ $booking['payment_status'] === 'cancelled'
                                    ? 'bg-danger'
                                    : ($booking['payment_status'] === 'pending'
                                        ? 'bg-warning'
                                        : 'bg-primary') }}
                                text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        Booking #{{ $booking['id'] }} - {{ $booking['package']['name'] }}
                                        @if ($booking['payment_status'] === 'cancelled')
                                            <span class="badge bg-light text-danger ms-2">Cancelled</span>
                                        @endif
                                    </h5>
                                    <div class="btn-group">
                                        @if ($booking['payment_status'] !== 'cancelled')
                                            @if ($booking['payment_summary']['remaining_balance'] > 0)
                                                <button wire:click="generatePaymentLink({{ $booking['id'] }})"
                                                    wire:loading.attr="disabled" class="btn btn-light btn-sm">
                                                    <div wire:loading.remove
                                                        wire:target="generatePaymentLink({{ $booking['id'] }})">
                                                        <i class="fas fa-link me-1"></i> Generate Link
                                                    </div>
                                                    <div wire:loading
                                                        wire:target="generatePaymentLink({{ $booking['id'] }})"
                                                        style="display: none;">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> Generating...
                                                    </div>
                                                </button>
                                            @endif

                                            <button wire:click="downloadInvoice({{ $booking['id'] }})"
                                                wire:loading.attr="disabled" class="btn btn-info btn-sm ms-2">
                                                <div wire:loading.remove
                                                    wire:target="downloadInvoice({{ $booking['id'] }})">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </div>
                                                <div wire:loading wire:target="downloadInvoice({{ $booking['id'] }})"
                                                    style="display: none;">
                                                    <i class="fas fa-spinner fa-spin me-1"></i> Preparing...
                                                </div>
                                            </button>

                                            <button wire:click="emailInvoice({{ $booking['id'] }})"
                                                wire:loading.attr="disabled" class="btn btn-success btn-sm ms-2">
                                                <div wire:loading.remove
                                                    wire:target="emailInvoice({{ $booking['id'] }})">
                                                    <i class="fas fa-envelope me-1"></i> Email
                                                </div>
                                                <div wire:loading wire:target="emailInvoice({{ $booking['id'] }})"
                                                    style="display: none;">
                                                    <i class="fas fa-spinner fa-spin me-1"></i> Sending...
                                                </div>
                                            </button>
                                        @else
                                            <button class="btn btn-light btn-sm" disabled>
                                                <i class="fas fa-ban me-1"></i> Booking Cancelled
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body">
                                    @if ($booking['payment_status'] === 'cancelled')
                                        <div class="alert alert-danger mb-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-exclamation-circle fs-4 me-2"></i>
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
                                                    <strong><i class="far fa-calendar-alt me-2"></i>From:</strong>
                                                    {{ \Carbon\Carbon::parse($booking['from_date'])->format('M d, Y') }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="far fa-calendar-alt me-2"></i>To:</strong>
                                                    {{ \Carbon\Carbon::parse($booking['to_date'])->format('M d, Y') }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="far fa-clock me-2"></i>Duration:</strong>
                                                    {{ $booking['number_of_days'] }} days
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-tag me-2"></i>Price Type:</strong>
                                                    {{ $booking['price_type'] }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-money-bill me-2"></i>Payment
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
                                                            <strong><i class="fas fa-calendar me-1"></i>Initial
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

                                                    <tr class="table-light">
                                                        <td colspan="2">
                                                            <strong><i class="fas fa-clock me-1"></i>Payment Schedule
                                                                ({{ $booking['price_type'] }})
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    @if (isset($booking['bookingPayments']) && count($booking['bookingPayments']) > 0)
                                                        @foreach ($booking['bookingPayments'] as $milestone)
                                                            <tr>
                                                                <td class="ps-3">
                                                                    @if ($milestone['milestone_type'] === 'Month')
                                                                        Month {{ $milestone['milestone_number'] }}
                                                                        Payment
                                                                    @elseif($milestone['milestone_type'] === 'Week')
                                                                        Week {{ $milestone['milestone_number'] }}
                                                                        Payment
                                                                    @elseif($milestone['milestone_type'] === 'Booking Fee')
                                                                        Booking Fee
                                                                        {{ $milestone['milestone_number'] }}
                                                                        Payment
                                                                    @else
                                                                        Day {{ $milestone['milestone_number'] }}
                                                                        Payment
                                                                    @endif
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        Due:
                                                                        {{ \Carbon\Carbon::parse($milestone['due_date'])->format('d M Y') }}
                                                                    </small>
                                                                </td>
                                                                <td class="text-end">
                                                                    £{{ number_format($milestone['amount'], 2) }}
                                                                    <br>

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="2" class="text-center text-muted">
                                                                <i class="fas fa-info-circle me-1"></i>No milestone
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
                                                    <i class="fas fa-info-circle me-2"></i>
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
                                                            <th>Reference Number</th>
                                                            <th>Status</th>
                                                            <th class="text-end">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($booking['payments'] as $index => $payment)
                                                            <tr
                                                                class="{{ $payment['status'] === 'Paid' ? 'bg-success text-white' : '' }}">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>£{{ number_format($payment['amount'], 2) }}</td>
                                                                <td>{{ ucfirst($payment['payment_method']) }}</td>
                                                                <td>{{ ucfirst($payment['transaction_id']) }}</td>
                                                                <td>{{ ucfirst($payment['status']) }}</td>
                                                                <td class="text-end">
                                                                    <!-- Update Payment Status Buttons -->
                                                                    <div class="btn-group">
                                                                        @if ($payment['status'] !== 'Paid')
                                                                            <button class="btn btn-sm btn-success"
                                                                                wire:click="updatePaymentStatusForPayment({{ $payment['id'] }}, 'Paid')">
                                                                                <i class="fas fa-check-circle"></i>
                                                                            </button>
                                                                        @endif
                                                                        @if ($payment['status'] !== 'Pending')
                                                                            <button class="btn btn-sm btn-warning"
                                                                                wire:click="updatePaymentStatusForPayment({{ $payment['id'] }}, 'Pending')">
                                                                                <i class="fas fa-clock"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
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
                    <i class="fas fa-info-circle me-2"></i>
                    No bookings found for this user.
                </div>
            @endif

            <!-- Update the copy script to handle multiple links -->
            <script>
                function copyToClipboard(bookingId) {
                    const paymentLink = document.getElementById(`paymentLink-${bookingId}`).href;
                    navigator.clipboard.writeText(paymentLink).then(() => {
                        const successElement = document.getElementById(`copySuccess-${bookingId}`);
                        successElement.style.display = 'block';
                        setTimeout(() => {
                            successElement.style.display = 'none';
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy:', err);
                    });
                }
            </script>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm p-4">
                @if ($showForm)
                    <h3>Upload Documents</h3>
                    <form wire:submit.prevent="saveDocuments" enctype="multipart/form-data">
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
                                    @if (isset($document['passport']) && !($document['passport'] instanceof \Illuminate\Http\UploadedFile))
                                        <p>Current File: <a href="{{ Storage::url($document['passport']) }}"
                                                target="_blank">View File</a></p>
                                    @endif
                                    @error('documents.' . $index . '.passport')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label>NID/Other (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.nid_or_other">
                                    @if (isset($document['nid_or_other']) && !($document['nid_or_other'] instanceof \Illuminate\Http\UploadedFile))
                                        <p>Current File: <a href="{{ Storage::url($document['nid_or_other']) }}"
                                                target="_blank">View File</a></p>
                                    @endif
                                    @error('documents.' . $index . '.nid_or_other')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- New Payslip Field -->
                                <div class="form-group mt-2">
                                    <label>Payslip (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.payslip">
                                    @if (isset($document['payslip']) && !($document['payslip'] instanceof \Illuminate\Http\UploadedFile))
                                        <p>Current File: <a href="{{ Storage::url($document['payslip']) }}"
                                                target="_blank">View File</a></p>
                                    @endif
                                    @error('documents.' . $index . '.payslip')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- New Student Card Field -->
                                <div class="form-group mt-2">
                                    <label>Student Card (PDF/Image)</label>
                                    <input type="file" class="form-control"
                                        wire:model.defer="documents.{{ $index }}.student_card">
                                    @if (isset($document['student_card']) && !($document['student_card'] instanceof \Illuminate\Http\UploadedFile))
                                        <p>Current File: <a href="{{ Storage::url($document['student_card']) }}"
                                                target="_blank">View File</a></p>
                                    @endif
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

                        <button type="button" class="btn btn-success mt-3" wire:click="addPerson">Add
                            Person</button>
                        <br>
                        <button type="submit" class="btn btn-primary mt-3">Save Documents</button>
                    </form>

                    @if (session()->has('message'))
                        <div class="alert alert-success mt-3">
                            {{ session('message') }}
                        </div>
                    @endif
                @else
                    <!-- Show the list if the form is hidden -->
                    <h4>Uploaded Documents</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Person Name</th>
                                    <th>Passport</th>
                                    <th>NID/Other</th>
                                    <th>Payslip/Others</th>
                                    <th>Student/Employee Card</th>
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
                                                    class="btn btn-sm btn-outline-info" target="_blank">Download
                                                    Passport</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->nid_or_other)
                                                <a href="{{ Storage::url($document->nid_or_other) }}"
                                                    class="btn btn-sm btn-outline-info" target="_blank">Download
                                                    NID/Other</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->payslip)
                                                <a href="{{ Storage::url($document->payslip) }}"
                                                    class="btn btn-sm btn-outline-info" target="_blank">Download
                                                    Payslip/Other</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->student_card)
                                                <a href="{{ Storage::url($document->student_card) }}"
                                                    class="btn btn-sm btn-outline-info" target="_blank">Download
                                                    Student/Employee Card</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="deleteDocument({{ $document->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No documents uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    <!-- Add an Edit button to show the form -->
                    <button type="button" class="btn btn-primary mt-3" wire:click="toggleForm">Edit
                        Documents</button>
                @endif
            </div>
        </div>
    @endif

    @if ($user->hasRole('Partner'))
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm p-4 mb-2">
                <h2 class="h4">Partner Documents</h2>
                <div>
                    <form wire:submit.prevent="updatePartner">
                        <div class="form-group mb-4">
                            <label for="proof_path_1" class="form-label">Gas Certificate</label>
                            <input type="hidden" wire:model="proof_type_1" value="Gas Certificate">
                            <input type="file" class="form-control" id="proof_path_1" wire:model="proof_path_1">
                            @error('proof_path_1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="proof_path_2" class="form-label">Electric Certificate</label>
                            <input type="hidden" wire:model="proof_type_2" value="Electric Certificate">
                            <input type="file" class="form-control" id="proof_path_2" wire:model="proof_path_2">
                            @error('proof_path_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="proof_path_3" class="form-label">Landlord Certificate (HMO/Other)</label>
                            <input type="hidden" wire:model="proof_type_3" value="Landlord Certificate (HMO/Other)">
                            <input type="file" class="form-control" id="proof_path_3" wire:model="proof_path_3">
                            @error('proof_path_3')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="proof_path_4" class="form-label">Building Insurance Certificate</label>
                            <input type="hidden" wire:model="proof_type_4" value="Building Insurance Certificate">
                            <input type="file" class="form-control" id="proof_path_4" wire:model="proof_path_4">
                            @error('proof_path_4')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Document</button>
                        </div>

                        <!-- Success Message -->
                        @if (session()->has('success'))
                            <div class="alert alert-success mt-2">{{ session('success') }}</div>
                        @endif
                    </form>
                </div>

            </div>
        </div>



        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm p-4">
                <h4>Bank Details</h4>
                <form wire:submit.prevent="saveBankDetails">
                    <div class="form-group mt-2">
                        <input type="text" class="form-control" placeholder="name" wire:model="bankDetail.name"
                            value="{{ old('bankDetail.name', $bankDetail['name']) }}">
                    </div>
                    <div class="form-group mt-2">
                        <input type="text" class="form-control" placeholder="Sort Code"
                            wire:model="bankDetail.sort_code"
                            value="{{ old('bankDetail.sort_code', $bankDetail['sort_code']) }}">
                    </div>
                    <div class="form-group mt-2">
                        <input type="text" class="form-control" placeholder="Account"
                            wire:model="bankDetail.account"
                            value="{{ old('bankDetail.account', $bankDetail['account']) }}">
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Save Bank Details</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm p-4">
                <h4>Agreement Details</h4>
                <form wire:submit.prevent="saveAgreement">
                    <div class="form-group mt-2">
                        <input type="text" class="form-control" placeholder="Agreement Type"
                            wire:model="agreementDetail.agreement_type"
                            value="{{ old('agreementDetail.agreement_type', $agreementDetail['agreement_type']) }}">
                    </div>
                    <div class="form-group mt-2">
                        <input type="text" class="form-control" placeholder="Duration"
                            wire:model="agreementDetail.duration"
                            value="{{ old('agreementDetail.duration', $agreementDetail['duration']) }}">
                    </div>
                    <div class="form-group mt-2">
                        <input type="number" step="0.01" class="form-control" placeholder="Amount"
                            wire:model="agreementDetail.amount"
                            value="{{ old('agreementDetail.amount', $agreementDetail['amount']) }}">
                    </div>
                    <div class="form-group mt-2">
                        <input type="number" step="0.01" class="form-control" placeholder="Deposit"
                            wire:model="agreementDetail.deposit"
                            value="{{ old('agreementDetail.deposit', $agreementDetail['deposit']) }}">
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Save Agreement</button>
                </form>
            </div>
        </div>
    @endif
</div>

<div class="mt-5">
    @if ($user->hasRole('Partner'))
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
                            ['type' => $user->proof_type_1, 'path' => $user->proof_path_1],
                            ['type' => $user->proof_type_2, 'path' => $user->proof_path_2],
                            ['type' => $user->proof_type_3, 'path' => $user->proof_path_3],
                            ['type' => $user->proof_type_4, 'path' => $user->proof_path_4],
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
                            <td colspan="3" class="text-center">No documents uploaded yet.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
</div>

@if ($showMilestoneSelectionModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Milestones</h5>
                    <button type="button" class="close" wire:click="$set('showMilestoneSelectionModal', false)">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($milestoneOptions as $milestone)
                                    <tr class="{{ $milestone['status'] === 'paid' ? 'table-success' : '' }}">
                                        <td>
                                            {{ $milestone['description'] }}
                                            @if ($milestone['is_booking_fee'])
                                                <span class="badge bg-primary ms-2">Booking Fee</span>
                                            @endif
                                        </td>
                                        <td>{{ $milestone['due_date'] }}</td>
                                        <td>£{{ number_format($milestone['amount'], 2) }}</td>
                                        <td>
                                            @switch($milestone['status'])
                                                @case('paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @break

                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @break

                                                @default
                                                    <span
                                                        class="badge bg-secondary">{{ ucfirst($milestone['status']) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if ($milestone['status'] === 'pending')
                                                @if ($milestone['payment_link'])
                                                    <div class="btn-group">
                                                        <a href="{{ $milestone['payment_link'] }}"
                                                            class="btn btn-info btn-sm" target="_blank">
                                                            View Link
                                                        </a>
                                                        <button class="btn btn-primary btn-sm"
                                                            wire:click="createPaymentLinkForMilestone({{ $milestone['id'] }})">
                                                            Regenerate Link
                                                        </button>
                                                    </div>
                                                @else
                                                    <button class="btn btn-primary btn-sm"
                                                        wire:click="createPaymentLinkForMilestone({{ $milestone['id'] }})">
                                                        Generate Link
                                                    </button>
                                                @endif
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    No Action
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                No milestones found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showMilestoneSelectionModal', false)">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isEditModalOpen)
        <!-- Modal Overlay -->
        <div class="modal-backdrop fade show"></div>

        <!-- Modal -->
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="display: block;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Information</h5>
                        <button type="button" class="close" wire:click="closeEditModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="updateUser">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" wire:model="userData.name"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" wire:model="userData.email"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" wire:model="userData.phone"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table> :not(caption)>*>* {
            padding: 0.75rem;
        }

        .badge {
            font-weight: 500;
        }

        .progress {
            border-radius: 0.5rem;
        }

        .alert {
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: #198754;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }

        .alert-info {
            border-left-color: #0dcaf0;
        }

        i {
            margin-right: 10px;
        }

        .btn-group .btn {
            min-width: 100px;
            transition: all 0.2s ease;
        }

        .btn-group .btn:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .fa-spin {
            animation-duration: 1s;
        }

        .alert {
            margin-top: 1rem;
            margin-bottom: 0;
        }
    </style>

    </div>
