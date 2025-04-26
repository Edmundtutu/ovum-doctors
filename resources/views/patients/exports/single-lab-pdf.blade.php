<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Result for {{ $patient->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
        }
        .patient-info {
            margin-bottom: 30px;
        }
        .patient-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-info th {
            text-align: left;
            width: 150px;
            padding: 8px;
            background-color: #f9f9f9;
        }
        .patient-info td {
            padding: 8px;
        }
        .lab-results {
            margin-bottom: 30px;
        }
        .lab-results table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .lab-results th {
            background-color: #f0f0f0;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .lab-results td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .lab-results tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .normal {
            color: #198754;
        }
        .abnormal {
            color: #dc3545;
        }
        .gauges {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }
        .gauge {
            width: 45%;
            text-align: center;
            position: relative;
        }
        .gauge-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .gauge-chart {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .gauge-fill {
            height: 100%;
            background-color: #0d6efd;
            border-radius: 10px;
        }
        .gauge-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        .contact-info {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Ovum Doctor</div>
        <div class="title">Lab Result Report</div>
        <div class="subtitle">Generated on {{ $generated_at }}</div>
    </div>
    
    <div class="patient-info">
        <table>
            <tr>
                <th>Patient Name:</th>
                <td>{{ $patient->name }}</td>
                <th>Date of Birth:</th>
                <td>{{ $patient->date_of_birth->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <th>Age:</th>
                <td>{{ $patient->age }} years</td>
                <th>Medical Condition:</th>
                <td>{{ $patient->medical_condition ?: 'None reported' }}</td>
            </tr>
            <tr>
                <th>Blood Type:</th>
                <td>{{ $patient->blood_type ?: 'Not recorded' }}</td>
                <th>Report Date:</th>
                <td>{{ $lab->created_at->format('Y-m-d') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="lab-results">
        <table>
            <tr>
                <th colspan="2">Visit Information</th>
            </tr>
            <tr>
                <td>Visit Date:</td>
                <td>{{ $visit->visited_at ? $visit->visited_at->format('Y-m-d') : 'Not recorded' }}</td>
            </tr>
            <tr>
                <td>Doctor:</td>
                <td>Dr. {{ $visit->doctor->name ?? 'Unknown' }}</td>
            </tr>
            <tr>
                <td>Visit Type:</td>
                <td>{{ $visit->type ?? 'Regular Visit' }}</td>
            </tr>
        </table>
        
        <div class="section-title">Vital Signs</div>
        <table>
            <tr>
                <th>Measurement</th>
                <th>Value</th>
                <th>Normal Range</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Respiratory Rate</td>
                <td>{{ $lab->respiratory_rate }} breaths/min</td>
                <td>12-20 breaths/min</td>
                <td class="{{ ($lab->respiratory_rate < 12 || $lab->respiratory_rate > 20) ? 'abnormal' : 'normal' }}">
                    {{ ($lab->respiratory_rate < 12 || $lab->respiratory_rate > 20) ? 'Abnormal' : 'Normal' }}
                </td>
            </tr>
            <tr>
                <td>Hemoglobin</td>
                <td>{{ $lab->hemoglobin }} g/dL</td>
                <td>12-16 g/dL</td>
                <td class="{{ ($lab->hemoglobin < 12 || $lab->hemoglobin > 16) ? 'abnormal' : 'normal' }}">
                    {{ ($lab->hemoglobin < 12 || $lab->hemoglobin > 16) ? 'Abnormal' : 'Normal' }}
                </td>
            </tr>
            <tr>
                <td>Blood Pressure</td>
                <td>{{ $lab->bp_systolic }}/{{ $lab->bp_diastolic }} mmHg</td>
                <td>90-120/60-80 mmHg</td>
                <td class="{{ ($lab->bp_systolic > 120 || $lab->bp_systolic < 90 || $lab->bp_diastolic > 80 || $lab->bp_diastolic < 60) ? 'abnormal' : 'normal' }}">
                    {{ ($lab->bp_systolic > 120 || $lab->bp_systolic < 90 || $lab->bp_diastolic > 80 || $lab->bp_diastolic < 60) ? 'Abnormal' : 'Normal' }}
                </td>
            </tr>
            <tr>
                <td>Waist-Hip Ratio</td>
                <td>{{ $lab->waist_hip_ratio }}</td>
                <td>&lt;0.85</td>
                <td class="{{ $lab->waist_hip_ratio > 0.85 ? 'abnormal' : 'normal' }}">
                    {{ $lab->waist_hip_ratio > 0.85 ? 'Above Target' : 'Optimal' }}
                </td>
            </tr>
        </table>
        
        <div class="section-title">Hormone Tests</div>
        <table>
            <tr>
                <th>Hormone</th>
                <th>Value</th>
                <th>Normal Range</th>
            </tr>
            <tr>
                <td>FSH</td>
                <td>{{ $lab->fsh }} mIU/mL</td>
                <td>4.7-21.5 mIU/mL (follicular phase)</td>
            </tr>
            <tr>
                <td>LH</td>
                <td>{{ $lab->lh }} mIU/mL</td>
                <td>2.0-14.7 mIU/mL (follicular phase)</td>
            </tr>
            <tr>
                <td>FSH/LH Ratio</td>
                <td>{{ $lab->fsh_lh_ratio }}</td>
                <td>~1 (normal), >2 (concerning)</td>
            </tr>
            <tr>
                <td>AMH</td>
                <td>{{ $lab->amh }} ng/mL</td>
                <td>1.0-4.0 ng/mL</td>
            </tr>
            <tr>
                <td>TSH</td>
                <td>{{ $lab->tsh }} mIU/L</td>
                <td>0.4-4.0 mIU/L</td>
            </tr>
            <tr>
                <td>Prolactin</td>
                <td>{{ $lab->prolactin }} ng/mL</td>
                <td>4.8-23.3 ng/mL</td>
            </tr>
        </table>
        
        <div class="section-title">Pregnancy Indicators</div>
        <table>
            <tr>
                <th>Test</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>HCG Initial</td>
                <td>{{ $lab->hcg_initial }} mIU/mL</td>
            </tr>
            <tr>
                <td>HCG Follow-up</td>
                <td>{{ $lab->hcg_followup ? $lab->hcg_followup . ' mIU/mL' : 'Not measured' }}</td>
            </tr>
            <tr>
                <td>Progesterone</td>
                <td>{{ $lab->progesterone }} ng/mL</td>
            </tr>
        </table>
        
        <div class="section-title">Additional Tests</div>
        <table>
            <tr>
                <th>Test</th>
                <th>Value</th>
                <th>Normal Range</th>
            </tr>
            <tr>
                <td>Vitamin D3</td>
                <td>{{ $lab->vitamin_d3 }} ng/mL</td>
                <td>20-50 ng/mL</td>
            </tr>
            <tr>
                <td>Random Blood Sugar</td>
                <td>{{ $lab->rbs }} mg/dL</td>
                <td>&lt;140 mg/dL</td>
            </tr>
        </table>
        
        <div class="section-title">Ultrasound Findings</div>
        <table>
            <tr>
                <th>Measurement</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Total Follicles</td>
                <td>{{ $lab->total_follicles }}</td>
            </tr>
            <tr>
                <td>Avg. Fallopian Size</td>
                <td>{{ $lab->avg_fallopian_size }} mm</td>
            </tr>
            <tr>
                <td>Endometrium Thickness</td>
                <td>{{ $lab->endometrium }} mm</td>
            </tr>
        </table>
        
        <div class="section-title">Key Indicators Visualization</div>
        <div class="gauges">
            <div class="gauge">
                <div class="gauge-title">FSH/LH Ratio</div>
                <div class="gauge-chart">
                    <div class="gauge-fill" style="width: {{ min(($lab->fsh_lh_ratio / 3) * 100, 100) }}%;"></div>
                </div>
                <div class="gauge-labels">
                    <span>0</span>
                    <span>1</span>
                    <span>2</span>
                    <span>3+</span>
                </div>
                <div>
                    <small>{{ $lab->fsh_lh_ratio < 1 ? 'Low' : ($lab->fsh_lh_ratio > 2 ? 'High (PCOS indicator)' : 'Normal') }}</small>
                </div>
            </div>
            <div class="gauge">
                <div class="gauge-title">AMH Level</div>
                <div class="gauge-chart">
                    <div class="gauge-fill" style="width: {{ min(($lab->amh / 5) * 100, 100) }}%;"></div>
                </div>
                <div class="gauge-labels">
                    <span>0</span>
                    <span>1</span>
                    <span>3</span>
                    <span>5+</span>
                </div>
                <div>
                    <small>{{ $lab->amh < 1 ? 'Low' : ($lab->amh > 4 ? 'High (PCOS indicator)' : 'Normal') }}</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>This is a medical document generated by Ovum Doctor System. Please consult with your healthcare provider for interpretation of these results.</p>
        <p>© {{ date('Y') }} Ovum Doctor. All rights reserved.</p>
    </div>
    
    <div class="contact-info">
        <p>For questions about this report, please contact your healthcare provider.</p>
        <p>Dr. {{ $visit->doctor->name ?? 'Unknown' }} | {{ $visit->doctor->email ?? 'No email available' }} | {{ $visit->doctor->phone ?? 'No phone available' }}</p>
    </div>
</body>
</html> 