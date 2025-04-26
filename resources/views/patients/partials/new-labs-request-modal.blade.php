{{-- Modal to request to use the current/latest Lab results or new Labs --}}
<div class="modal fade" id="newLabsRequestModal" tabindex="-1" aria-labelledby="newLabsRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white">
                <h5 class="modal-title" id="newLabsRequestModalLabel">
                    <i class="fas fa-flask me-2"></i> Lab Test Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">Recent Lab Results</h6>
                    
                    @if(isset($patient) && $patient->latest_visit && $patient->latest_visit->labs->isNotEmpty())
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <p class="mb-1">Last lab results from {{ $patient->latest_visit->visited_at ? $patient->latest_visit->visited_at->format('M d, Y') : 'N/A' }}</p>
                                    <a href="{{ route('visits.labs.show', [$patient->latest_visit, $patient->latest_visit->labs->first()]) }}" class="btn btn-sm btn-outline-primary">
                                        View Previous Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <p class="mb-0">No previous lab results available for this patient.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <form id="labRequestForm" action="#" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Visit for Lab Results:</label>
                        <select name="visit_id" id="visit_id" class="form-select" required>
                            <option value="">-- Select a Visit --</option>
                            @forelse($patient->visits->sortByDesc('visited_at') as $visit)
                                <option value="{{ $visit->id }}">
                                    {{ $visit->visited_at ? $visit->visited_at->format('M d, Y') : 'Date not recorded' }} - 
                                    {{ $visit->type }} with Dr. {{ $visit->doctor->name }}
                                </option>
                            @empty
                                <option value="" disabled>No visits available</option>
                            @endforelse
                        </select>
                        <div class="form-text">Choose a visit to associate with these lab results</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tests to Request:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="tests[]" value="basic" id="basicTests" checked>
                                    <label class="form-check-label" for="basicTests">Basic Measurements (BP, Respiratory Rate)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="tests[]" value="hormone" id="hormoneTests" checked>
                                    <label class="form-check-label" for="hormoneTests">Hormone Panel (FSH, LH, AMH, TSH)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="tests[]" value="pregnancy" id="pregnancyTests" checked>
                                    <label class="form-check-label" for="pregnancyTests">Pregnancy Indicators (HCG, Progesterone)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="tests[]" value="ultrasound" id="ultrasoundTests" checked>
                                    <label class="form-check-label" for="ultrasoundTests">Ultrasound Findings</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label fw-bold">Priority:</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="routine">Routine</option>
                            <option value="urgent">Urgent</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Additional Notes:</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any specific instructions or concerns..."></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="submitLabRequestBtn" class="btn btn-primary">Send Lab Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labRequestForm = document.getElementById('labRequestForm');
    const visitIdSelect = document.getElementById('visit_id');
    const submitLabRequestBtn = document.getElementById('submitLabRequestBtn');
    const labModal = document.getElementById('newLabsRequestModal');
    const bsLabModal = new bootstrap.Modal(labModal);
    
    if (submitLabRequestBtn && visitIdSelect) {
        submitLabRequestBtn.addEventListener('click', function() {
            const visitId = visitIdSelect.value;
            if (visitId) {
                // Use the Laravel route helper correctly; use the exact URL format that Laravel expects
                labRequestForm.action = "{{ url('/visits') }}/" + visitId + "/labs/create";
                
                // Log the URL for debugging
                console.log("Redirecting to: " + labRequestForm.action);
                
                // Submit the form (will open in new tab because of target="_blank")
                labRequestForm.submit();
                
                // Dismiss the modal after submission
                bsLabModal.hide();
                
                // Show a toast or notification if desired
                showNotification('Lab request form opened in new tab');
            } else {
                alert('Please select a visit.');
            }
        });
    }
    
    // Optional: Simple notification function
    function showNotification(message) {
        // Check if we have toast components available
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Toast !== 'undefined') {
            // Create a toast element
            const toastEl = document.createElement('div');
            toastEl.className = 'toast position-fixed bottom-0 end-0 m-3';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            
            toastEl.innerHTML = `
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            `;
            
            document.body.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            
            // Remove the toast after it's hidden
            toastEl.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toastEl);
            });
        } else {
            // Fallback to console log if toast components are not available
            console.log(message);
        }
    }
});
</script>