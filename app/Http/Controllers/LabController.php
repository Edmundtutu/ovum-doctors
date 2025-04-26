<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabRequest;
use App\Http\Requests\UpdateLabRequest;
use App\Models\Lab;
use App\Models\Visit;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class LabController extends Controller
{
    /**
     * Display a listing of all Labs for the patient. (will be used mainly for for Api calls)
     */
    public function index(Request $request): JsonResponse
    {
        $labs = Lab::query()
            ->where('visit_id', $request->visit_id)
            ->with(['visit.patient', 'visit.doctor'])
            ->latest('created_at')
            ->get();
        return response()->json($labs);
    }
   

    /**
     * Show the form for creating a new resource.
     * This method can be called either:
     * - directly with a Visit model via visits.labs.create route
     * - or with a Patient model and visit_id param via patients.labs.create route
     */
    public function create(Request $request, $patientOrVisit)
    {
        // For debugging
        Log::info('LabController create method called with:', [
            'param_type' => gettype($patientOrVisit),
            'param_value' => $patientOrVisit,
            'request_has_visit_id' => $request->has('visit_id'),
            'request_visit_id' => $request->visit_id
        ]);
        
        // If we received a Visit model directly
        if ($patientOrVisit instanceof Visit) {
            $visit = $patientOrVisit;
            $patient = $visit->patient;
        } 
        // If we received a Patient model from the patients.labs.create route
        else if ($patientOrVisit instanceof Patient) {
            $patient = $patientOrVisit;
            $visit = null;
            
            // If a visit_id was passed, load that visit
            if ($request->has('visit_id')) {
                $visit = Visit::findOrFail($request->visit_id);
            }
        }
        // Direct ID-based route access
        else {
            try {
                // Try to load as a Visit first
                $visit = Visit::findOrFail($patientOrVisit);
                $patient = $visit->patient;
            } catch (\Exception $e) {
                // If that fails, try to load as a Patient and require visit_id param
                if ($request->has('visit_id')) {
                    $patient = Patient::findOrFail($patientOrVisit);
                    $visit = Visit::findOrFail($request->visit_id);
                } else {
                    // Log the error and re-throw if we can't resolve
                    Log::error('Cannot resolve visit or patient:', [
                        'error' => $e->getMessage(),
                        'param' => $patientOrVisit,
                        'request' => $request->all()
                    ]);
                    throw $e;
                }
            }
        }
        
        return view('patients.partials.create-new-lab', compact('visit', 'patient'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Visit $visit, StoreLabRequest $request)
    {
        $lab = $visit->labs()->create($request->validated());
        
        // Check if this was from a new tab or regular form
        if ($request->has('redirect_to_patient') && $request->redirect_to_patient) {
            // If it's a flag to indicate we want to go to the patient page
            return redirect()->route('patients.show', $visit->patient_id)
                            ->with('success', 'Lab results saved successfully.');
        }
        
        // For a regular form submission, show the newly created lab
        return redirect()->route('visits.labs.show', [$visit, $lab])
                        ->with('success', 'Lab results saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit, Lab $lab)
    {
        return view('patients.partials.show-labs', compact('visit', 'lab'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit, Lab $lab)
    {
        return view('patients.partials.edit-lab', compact('visit', 'lab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLabRequest $request, Visit $visit, Lab $lab)
    {
        $lab->update($request->validated());
        
        // Check if we should redirect to patient page
        if ($request->has('redirect_to_patient') && $request->redirect_to_patient) {
            return redirect()->route('patients.show', $visit->patient_id)
                            ->with('success', 'Lab results updated successfully.');
        }
        
        // Otherwise redirect to the lab details
        return redirect()->route('visits.labs.show', [$visit, $lab])
                        ->with('success', 'Lab results updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit, Lab $lab)
    {
        $lab->delete();
        return redirect()->route('patients.show', $visit->patient_id)
                        ->with('success', 'Lab results deleted successfully.');
    }

    /**
     * Export patient's lab results as a CSV file.
     *
     * @param Patient $patient
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Patient $patient)
    {
        // Get all lab results for the patient through visits
        $labs = $patient->labs()
            ->with(['visit.doctor'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Generate CSV filename
        $filename = $this->sanitizeFilename($patient->name) . '_lab_results_' . date('Y-m-d') . '.csv';
        
        // Headers for the CSV
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        // Create stream response
        $callback = function() use ($labs, $patient) {
            $file = fopen('php://output', 'w');
            
            // Meta information
            fputcsv($file, ['Patient Information']);
            fputcsv($file, ['Name', $patient->name]);
            fputcsv($file, ['Date of Birth', $patient->date_of_birth->format('Y-m-d')]);
            fputcsv($file, ['Generated on', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Total Lab Results', $labs->count()]);
            fputcsv($file, []); // Empty row for separation
            
            // CSV Headers
            fputcsv($file, [
                'Date',
                'Visit Type',
                'Doctor',
                'Respiratory Rate (breaths/min)',
                'Hemoglobin (g/dL)',
                'BP Systolic (mmHg)',
                'BP Diastolic (mmHg)',
                'Waist-Hip Ratio',
                'FSH (mIU/mL)',
                'LH (mIU/mL)',
                'FSH/LH Ratio',
                'AMH (ng/mL)',
                'TSH (mIU/L)',
                'HCG Initial (mIU/mL)',
                'HCG Follow-up (mIU/mL)',
                'Progesterone (ng/mL)',
                'Prolactin (ng/mL)',
                'Vitamin D3 (ng/mL)',
                'RBS (mg/dL)',
                'Total Follicles',
                'Avg. Fallopian Size (mm)',
                'Endometrium (mm)'
            ]);
            
            // CSV Data
            foreach ($labs as $lab) {
                fputcsv($file, [
                    $lab->created_at->format('Y-m-d'),
                    $lab->visit->type ?? 'Unknown',
                    $lab->visit->doctor->name ?? 'Unknown',
                    $lab->respiratory_rate,
                    $lab->hemoglobin,
                    $lab->bp_systolic,
                    $lab->bp_diastolic,
                    $lab->waist_hip_ratio,
                    $lab->fsh,
                    $lab->lh,
                    $lab->fsh_lh_ratio,
                    $lab->amh,
                    $lab->tsh,
                    $lab->hcg_initial,
                    $lab->hcg_followup,
                    $lab->progesterone,
                    $lab->prolactin,
                    $lab->vitamin_d3,
                    $lab->rbs,
                    $lab->total_follicles,
                    $lab->avg_fallopian_size,
                    $lab->endometrium
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export patient's lab results as a PDF file.
     *
     * @param Patient $patient
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Patient $patient)
    {
        // Get all lab results for the patient through visits
        $labs = $patient->labs()
            ->with(['visit.doctor'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Generate PDF filename
        $filename = $this->sanitizeFilename($patient->name) . '_lab_results_' . date('Y-m-d') . '.pdf';
        
        // Create PDF
        $pdf = Pdf::loadView('patients.exports.lab-results-pdf', [
            'patient' => $patient,
            'labs' => $labs,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Set paper size and orientation (optional, you can customize)
        $pdf->setPaper('a4', 'landscape');
        
        // Download the PDF
        return $pdf->download($filename);
    }
    
    /**
     * Export a specific lab result as a PDF file.
     *
     * @param Visit $visit
     * @param Lab $lab 
     * @return \Illuminate\Http\Response
     */
    public function exportLabPdf(Visit $visit, Lab $lab)
    {
        $patient = $visit->patient;
        
        // Generate PDF filename
        $filename = $this->sanitizeFilename($patient->name) . '_lab_result_' . $lab->id . '_' . date('Y-m-d') . '.pdf';
        
        // Create PDF
        $pdf = Pdf::loadView('patients.exports.single-lab-pdf', [
            'patient' => $patient,
            'visit' => $visit,
            'lab' => $lab,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Download the PDF
        return $pdf->download($filename);
    }
    
    /**
     * Sanitize a filename to ensure it's safe for saving.
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename($filename)
    {
        // Remove any characters that aren't alphanumeric, underscore, dash, or dot
        $filename = preg_replace('/[^\w\-\.]/', '_', $filename);
        // Remove multiple consecutive underscores
        $filename = preg_replace('/_+/', '_', $filename);
        // Trim underscores from the beginning and end
        return trim($filename, '_');
    }
}
