<div>
    <div class="container-fluid py-4">
        <!-- Common Profile Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle mr-2"></i>Profile Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-user-edit mr-2"></i>Personal Information
                                        </h6>
                                        <livewire:profile.update-profile-information-form />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-lock mr-2"></i>Security Settings
                                        </h6>
                                        <livewire:profile.update-password-form />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Role Content -->
        @role('User')
            <div class="row">
                <div class="col-12">
                    <!-- Document Upload Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-upload mr-2"></i>Document Upload Center
                            </h5>
                        </div>
                        <div class="card-body">
                            @if (session()->has('message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form wire:submit.prevent="saveDocuments">
                                @foreach ($documents as $index => $document)
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-user mr-2"></i>Person {{ $index + 1 }}</span>
                                                @if ($index > 0)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removePerson({{ $index }})">
                                                        <i class="fas fa-trash mr-1"></i>Remove
                                                    </button>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">
                                                            <i class="fas fa-user-circle mr-1"></i>Person Name
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter full name"
                                                            wire:model.defer="documents.{{ $index }}.person_name">
                                                        @error('documents.' . $index . '.person_name')
                                                            <small class="text-danger">
                                                                <i
                                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                            </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">
                                                            <i class="fas fa-passport mr-1"></i>Passport
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                wire:model.defer="documents.{{ $index }}.passport"
                                                                id="passport{{ $index }}">
                                                            <label class="custom-file-label"
                                                                for="passport{{ $index }}">
                                                                Choose file
                                                            </label>
                                                        </div>
                                                        @error('documents.' . $index . '.passport')
                                                            <small class="text-danger">
                                                                <i
                                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                            </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">
                                                            <i class="fas fa-id-card mr-1"></i>NID/Other
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                wire:model.defer="documents.{{ $index }}.nid_or_other"
                                                                id="nid{{ $index }}">
                                                            <label class="custom-file-label" for="nid{{ $index }}">
                                                                Choose file
                                                            </label>
                                                        </div>
                                                        @error('documents.' . $index . '.nid_or_other')
                                                            <small class="text-danger">
                                                                <i
                                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                            </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">
                                                            <i class="fas fa-file-invoice-dollar mr-1"></i>Payslip
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                wire:model.defer="documents.{{ $index }}.payslip"
                                                                id="payslip{{ $index }}">
                                                            <label class="custom-file-label"
                                                                for="payslip{{ $index }}">
                                                                Choose file
                                                            </label>
                                                        </div>
                                                        @error('documents.' . $index . '.payslip')
                                                            <small class="text-danger">
                                                                <i
                                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                            </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">
                                                            <i class="fas fa-id-badge mr-1"></i>Student/Employee Card
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                wire:model.defer="documents.{{ $index }}.student_card"
                                                                id="student_card{{ $index }}">
                                                            <label class="custom-file-label"
                                                                for="student_card{{ $index }}">
                                                                Choose file
                                                            </label>
                                                        </div>
                                                        @error('documents.' . $index . '.student_card')
                                                            <small class="text-danger">
                                                                <i
                                                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                            </small>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="text-center mb-4">
                                    <button type="button" class="btn btn-success mr-2" wire:click="addPerson">
                                        <i class="fas fa-plus mr-1"></i>Add Another Person
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Save All Documents
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Uploaded Documents Table -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt mr-2"></i>Uploaded Documents
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="15%">Person Name</th>
                                            <th width="15%">Passport</th>
                                            <th width="15%">NID/Other</th>
                                            <th width="15%">Payslip</th>
                                            <th width="15%">Student/Employee Card</th>
                                            <th width="20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($user->documents as $index => $document)
                                            <tr>
                                                <td class="align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <i class="fas fa-user-circle text-primary mr-1"></i>
                                                    {{ $document->person_name }}
                                                </td>
                                                <td class="align-middle">
                                                    @if ($document->passport)
                                                        <a href="{{ Storage::url($document->passport) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-passport mr-1"></i>View Passport
                                                        </a>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-times-circle mr-1"></i>Not Uploaded
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if ($document->nid_or_other)
                                                        <a href="{{ Storage::url($document->nid_or_other) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-id-card mr-1"></i>View NID
                                                        </a>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-times-circle mr-1"></i>Not Uploaded
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if ($document->payslip)
                                                        <a href="{{ Storage::url($document->payslip) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-file-invoice-dollar mr-1"></i>View Payslip
                                                        </a>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-times-circle mr-1"></i>Not Uploaded
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if ($document->student_card)
                                                        <a href="{{ Storage::url($document->student_card) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-id-badge mr-1"></i>View Card
                                                        </a>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-times-circle mr-1"></i>Not Uploaded
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-info"
                                                            wire:click="editDocument({{ $document->id }})"
                                                            title="Edit Document">
                                                            <i class="fas fa-edit mr-1"></i>Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            wire:click="deleteDocument({{ $document->id }})"
                                                            onclick="return confirm('Are you sure you want to delete this document?')"
                                                            title="Delete Document">
                                                            <i class="fas fa-trash-alt mr-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-folder-open fa-2x mb-3"></i>
                                                        <p class="mb-0">No documents have been uploaded yet.</p>
                                                        <small>Upload your first document using the form above.</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        <!-- Partner Role Content -->
        @role('Partner')
            <div class="row">
                <!-- Partner Documents -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-upload mr-2"></i>Partner Documents
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-file-contract mr-1"></i>Gas Certificate
                                    </label>
                                    <input type="hidden" name="proof_type_1" value="Gas Certificate">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="proof_path_1"
                                            name="proof_path_1">
                                        <label class="custom-file-label" for="proof_path_1">Choose file</label>
                                    </div>
                                    @error('proof_path_1')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-bolt mr-1"></i>Electric Certificate
                                    </label>
                                    <input type="hidden" name="proof_type_2" value="Electric Certificate">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="proof_path_2"
                                            name="proof_path_2">
                                        <label class="custom-file-label" for="proof_path_2">Choose file</label>
                                    </div>
                                    @error('proof_path_2')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-home mr-1"></i>Landlord Certificate (HMO/Other)
                                    </label>
                                    <input type="hidden" name="proof_type_3" value="Landlord Certificate (HMO/Other)">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="proof_path_3"
                                            name="proof_path_3">
                                        <label class="custom-file-label" for="proof_path_3">Choose file</label>
                                    </div>
                                    @error('proof_path_3')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-shield-alt mr-1"></i>Building Insurance Certificate
                                    </label>
                                    <input type="hidden" name="proof_type_4" value="Building Insurance Certificate">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="proof_path_4"
                                            name="proof_path_4">
                                        <label class="custom-file-label" for="proof_path_4">Choose file</label>
                                    </div>
                                    @error('proof_path_4')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Update Documents
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-university mr-2"></i>Bank Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveBankDetails">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-user mr-1"></i>Account Holder Name
                                    </label>
                                    <input type="text" class="form-control" placeholder="Enter account holder name"
                                        wire:model="bankDetail.name">
                                    @error('bankDetail.name')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-sort-numeric-up mr-1"></i>Sort Code
                                    </label>
                                    <input type="text" class="form-control" placeholder="Enter sort code"
                                        wire:model="bankDetail.sort_code">
                                    @error('bankDetail.sort_code')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-hashtag mr-1"></i>Account Number
                                    </label>
                                    <input type="text" class="form-control" placeholder="Enter account number"
                                        wire:model="bankDetail.account">
                                    @error('bankDetail.account')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Save Bank Details
                                    </button>
                                </div>

                                @if (session()->has('message'))
                                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                        <i class="fas fa-check-circle mr-1"></i>{{ session('message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Agreement Details -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-contract mr-2"></i>Agreement Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveAgreement">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-file-signature mr-1"></i>Agreement Type
                                    </label>
                                    <input type="text" class="form-control" placeholder="Enter agreement type"
                                        wire:model="agreementDetail.agreement_type">
                                    @error('agreementDetail.agreement_type')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-calendar-alt mr-1"></i>Duration
                                    </label>
                                    <input type="text" class="form-control" placeholder="Enter duration"
                                        wire:model="agreementDetail.duration">
                                    @error('agreementDetail.duration')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-pound-sign mr-1"></i>Amount
                                    </label>
                                    <input type="number" step="0.01" class="form-control" placeholder="Enter amount"
                                        wire:model="agreementDetail.amount">
                                    @error('agreementDetail.amount')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-piggy-bank mr-1"></i>Deposit
                                    </label>
                                    <input type="number" step="0.01" class="form-control"
                                        placeholder="Enter deposit amount" wire:model="agreementDetail.deposit">
                                    @error('agreementDetail.deposit')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Save Agreement
                                    </button>
                                </div>

                                @if (session()->has('message'))
                                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                        <i class="fas fa-check-circle mr-1"></i>{{ session('message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Partner Uploaded Documents Table -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt mr-2"></i>Uploaded Documents
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="30%">Document Type</th>
                                            <th width="20%">Status</th>
                                            <th width="45%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $documents = [
                                                [
                                                    'type' => Auth::user()->proof_type_1,
                                                    'path' => Auth::user()->proof_path_1,
                                                ],
                                                [
                                                    'type' => Auth::user()->proof_type_2,
                                                    'path' => Auth::user()->proof_path_2,
                                                ],
                                                [
                                                    'type' => Auth::user()->proof_type_3,
                                                    'path' => Auth::user()->proof_path_3,
                                                ],
                                                [
                                                    'type' => Auth::user()->proof_type_4,
                                                    'path' => Auth::user()->proof_path_4,
                                                ],
                                            ];
                                        @endphp

                                        @forelse($documents as $index => $document)
                                            <tr>
                                                <td class="align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <i class="fas fa-file-alt text-primary mr-1"></i>
                                                    {{ $document['type'] }}
                                                </td>
                                                <td class="align-middle">
                                                    @if ($document['path'])
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle mr-1"></i>Uploaded
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-exclamation-circle mr-1"></i>Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if ($document['path'])
                                                        <a href="{{ asset('storage/' . $document['path']) }}"
                                                            class="btn btn-sm btn-primary mr-2" download>
                                                            <i class="fas fa-download mr-1"></i>Download
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-info"
                                                            onclick="document.getElementById('proof_path_{{ $index + 1 }}').click();">
                                                            <i class="fas fa-upload mr-1"></i>Update
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="document.getElementById('proof_path_{{ $index + 1 }}').click();">
                                                            <i class="fas fa-upload mr-1"></i>Upload
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-folder-open fa-2x mb-3"></i>
                                                        <p class="mb-0">No documents have been uploaded yet.</p>
                                                        <small>Upload your documents using the form above.</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        <!-- Edit Document Modal -->
        @if ($showEditModal)
            <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit mr-2"></i>Edit Document
                            </h5>
                            <button type="button" class="close text-white" wire:click="closeEditModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="updateDocument">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-user-circle mr-1"></i>Person Name
                                    </label>
                                    <input type="text" class="form-control" wire:model="editPersonName" required>
                                    @error('editPersonName')
                                        <small class="text-danger">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-passport mr-1"></i>Passport
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input"
                                                    wire:model="editPassport" id="editPassport">
                                                <label class="custom-file-label" for="editPassport">
                                                    Choose file
                                                </label>
                                            </div>
                                            @if ($editPassport)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle mr-1"></i>New file selected
                                                </small>
                                            @endif
                                            @error('editPassport')
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-id-card mr-1"></i>NID/Other
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input"
                                                    wire:model="editNidOrOther" id="editNidOrOther">
                                                <label class="custom-file-label" for="editNidOrOther">
                                                    Choose file
                                                </label>
                                            </div>
                                            @if ($editNidOrOther)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle mr-1"></i>New file selected
                                                </small>
                                            @endif
                                            @error('editNidOrOther')
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-file-invoice-dollar mr-1"></i>Payslip
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input"
                                                    wire:model="editPayslip" id="editPayslip">
                                                <label class="custom-file-label" for="editPayslip">
                                                    Choose file
                                                </label>
                                            </div>
                                            @if ($editPayslip)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle mr-1"></i>New file selected
                                                </small>
                                            @endif
                                            @error('editPayslip')
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-id-badge mr-1"></i>Student/Employee Card
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input"
                                                    wire:model="editStudentCard" id="editStudentCard">
                                                <label class="custom-file-label" for="editStudentCard">
                                                    Choose file
                                                </label>
                                            </div>
                                            @if ($editStudentCard)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle mr-1"></i>New file selected
                                                </small>
                                            @endif
                                            @error('editStudentCard')
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer mt-4">
                                    <button type="button" class="btn btn-secondary" wire:click="closeEditModal">
                                        <i class="fas fa-times mr-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if ($showDeleteModal)
            <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                            </h5>
                            <button type="button" class="close text-white" wire:click="closeDeleteModal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this document? This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="confirmDelete">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- Initialize Custom File Input -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle custom file input change
            const handleFileInput = () => {
                const fileInputs = document.querySelectorAll('.custom-file-input');
                fileInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const fileName = this.value.split('\\').pop();
                        const label = this.nextElementSibling;
                        if (label) {
                            label.classList.add('selected');
                            label.innerHTML = fileName || 'Choose file';
                        }
                    });
                });
            };

            // Handle form submissions
            const handleFormSubmission = () => {
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const submitButton = this.querySelector('button[type="submit"]');
                        if (submitButton) {
                            // Store original button text if not already stored
                            if (!submitButton.getAttribute('data-original-text')) {
                                submitButton.setAttribute('data-original-text', submitButton
                                    .innerHTML);
                            }

                            // Disable button and show loading state
                            submitButton.disabled = true;
                            submitButton.innerHTML =
                                '<i class="fas fa-spinner fa-spin mr-1"></i>Processing...';

                            // Reset button after submission (adjust timeout as needed)
                            setTimeout(() => {
                                submitButton.disabled = false;
                                submitButton.innerHTML = submitButton.getAttribute(
                                    'data-original-text');
                            }, 2000); // Adjust timeout as needed
                        }
                    });
                });
            };

            // Initialize all handlers
            handleFileInput();
            handleFormSubmission();

            // Optional: Re-initialize handlers after dynamic content changes
            // You might need to call these functions after adding new forms or file inputs
            const reinitializeHandlers = () => {
                handleFileInput();
                handleFormSubmission();
            };

            // Make reinitializeHandlers available globally if needed
            window.reinitializeHandlers = reinitializeHandlers;
        });
    </script>

    <!-- Add required styles -->
    <style>
        .modal {
            overflow-y: auto;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .btn-group {
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .badge {
            font-size: 85%;
        }

        .close {
            opacity: 0.8;
        }

        .close:hover {
            opacity: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-group {
                display: flex;
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 0.25rem;
                border-radius: 0.25rem !important;
            }

            .table-responsive {
                border: 0;
            }
        }
    </style>

</div>
