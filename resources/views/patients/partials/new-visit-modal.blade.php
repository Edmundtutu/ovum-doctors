<!-- New Visit Modal -->
<div class="modal fade" id="newVisitModal" tabindex="-1" aria-labelledby="newVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newVisitModalLabel">Record New Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('visits.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="visit_type" class="form-label">Visit Type</label>
                            <select class="form-select" id="visit_type" name="type" required>
                                <option value="">Select visit type</option>
                                <option value="Routine Checkup">Routine Checkup</option>
                                <option value="Urgent Care">Urgent Care</option>
                                <option value="Emergency">Emergency</option>
                                <option value="Follow-up">Follow-up</option>
                                <option value="Consultation">Consultation</option>
                                <option value="Prenatal">Prenatal Care</option>
                                <option value="Postnatal">Postnatal Care</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="visited_at" class="form-label">Visit Date</label>
                            <input type="datetime-local" class="form-control" id="visited_at" name="visited_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="chief_complaint" class="form-label">Chief Complaint</label>
                        <input type="text" class="form-control" id="chief_complaint" name="chief_complaint" placeholder="Primary reason for visit">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Visit Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Observations, findings, and recommendations"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Vitals</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="blood_pressure" class="form-label">Blood Pressure</label>
                                <input type="text" class="form-control" id="blood_pressure" name="vitals[blood_pressure]" placeholder="e.g. 120/80">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="heart_rate" class="form-label">Heart Rate (bpm)</label>
                                <input type="number" class="form-control" id="heart_rate" name="vitals[heart_rate]" placeholder="e.g. 72">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="temperature" class="form-label">Temperature (°C)</label>
                                <input type="number" step="0.1" class="form-control" id="temperature" name="vitals[temperature]" placeholder="e.g. 37.0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="weight" class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" class="form-control" id="weight" name="vitals[weight]" placeholder="e.g. 65.5">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnosis</label>
                        <input type="text" class="form-control" id="diagnosis" name="diagnosis" placeholder="Enter diagnosis">
                    </div>
                    
                    <div class="mb-3">
                        <label for="treatment" class="form-label">Treatment Plan</label>
                        <textarea class="form-control" id="treatment" name="treatment" rows="2" placeholder="Describe treatment plan"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="follow_up" class="form-label">Follow-up Recommendations</label>
                        <textarea class="form-control" id="follow_up" name="follow_up" rows="2" placeholder="Note any follow-up needed"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Visit</button>
                </div>
            </form>
        </div>
    </div>
</div> 