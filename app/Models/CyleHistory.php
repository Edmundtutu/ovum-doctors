<?php
// -------------------------------------------------
// Model: CyleHistory.php
// -------------------------------------------------
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Patient;
use Carbon\Carbon;

/**
 * CyleHistory - Manages menstrual cycle tracking data and business logic
 * 
 * Core Responsibilities:
 * 1. Maintains cycle-related temporal data and status transitions
 * 2. Enforces data integrity through casting and fillable properties
 * 3. Implements period tracking business logic
 * 4. Manages cycle lifecycle through start/end operations
 */
class CyleHistory extends Model
{
    // Enables model factory support for testing
    use HasFactory;

    /**
     * Database table name specification
     * @var string
     */
    protected $table = 'cyle_histories';

    /**
     * Mass assignable attributes
     * @var array
     * 
     * Security Note: Patient_id is guarded against mass assignment
     * through controller-level validation
     */
    protected $fillable = [
        'patient_id',        // Foreign key to associated Patient
        'month',             // Tracking month in 'YYYY-MM' format
        'cycle_length',      // Expected total days in cycle
        'period_length',     // Expected menstrual flow duration
        'symptoms',          // JSON array of reported symptoms
        'cycle_start_date',  // Actual cycle commencement date
        'cycle_end_date',    // Calculated cycle end date
        'period_start_date', // Actual menstrual flow start
        'period_end_date',   // Calculated flow end date
        'cycle_status',      // Lifecycle state: new/in_progress/completed
    ];

    /**
     * Attribute type casting
     * @var array
     * 
     * Ensures proper data type handling for:
     * - Temporal fields (Carbon instances)
     * - Numeric values
     * - JSON array storage
     */
    protected $casts = [
        'month'             => 'date:Y-m',  // Partial date cast
        'cycle_length'      => 'integer',
        'period_length'     => 'integer',
        'symptoms'          => 'array',     // JSON ↔ array conversion
        'cycle_start_date'  => 'date',
        'cycle_end_date'    => 'date',
        'period_start_date' => 'date',
        'period_end_date'   => 'date',
    ];

    /**
     * Patient relationship definition
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Records menstrual period commencement and updates cycle state
     * 
     * Business Rules:
     * 1. Requires prior cycle initialization with start date
     * 2. Derives period end from period_length if available
     * 3. Calculates cycle end date based on cycle_length
     * 4. Updates tracking month based on cycle start date
     * 
     * @param string|null $startDate Optional override date (Y-m-d format)
     * @return $this
     */
    public function logPeriodStart(?string $startDate = null)
    {
        // Determine effective start date
        $start = $startDate ? Carbon::parse($startDate) : Carbon::today();

        // Set period start date
        $this->period_start_date = $start->toDateString();

        // Calculate period end date if period length exists
        if ($this->period_length) {
            $this->period_end_date = $start->copy()
                ->addDays($this->period_length - 1)
                ->toDateString();
        }

        // Calculate cycle end date if cycle length exists
        if ($this->cycle_start_date && $this->cycle_length) {
            $cycleStart = Carbon::parse($this->cycle_start_date);
            $this->cycle_end_date = $cycleStart
                ->addDays($this->cycle_length - 1)
                ->toDateString();
        }

        // Update cycle state
        $this->cycle_status = 'in_progress';

        // Synchronize month tracking with cycle start
        $this->month = Carbon::parse($this->cycle_start_date)->format('Y-m');

        return $this;
    }

    /**
     * Finalizes current cycle and initializes subsequent cycle
     * 
     * Operational Flow:
     * 1. Calculates actual period duration
     * 2. Finalizes current cycle dates
     * 3. Transitions cycle status to completed
     * 4. Creates new cycle record with inherited/default values
     * 
     * @param string|null $endDate Period conclusion date override
     * @param int|null $newCycleLength Custom duration for next cycle
     * @param int|null $newPeriodLength Custom duration for next period
     * @return CyleHistory Newly created cycle record
     */
    public function logPeriodEnd(?string $endDate = null, ?int $newCycleLength = null, ?int $newPeriodLength = null)
    {
        // Determine effective end date
        $end = $endDate ? Carbon::parse($endDate) : Carbon::today();

        // Set period end date
        $this->period_end_date = $end->toDateString();

        // Calculate actual period duration
        if ($this->period_start_date) {
            $start = Carbon::parse($this->period_start_date);
            $this->period_length = $start->diffInDays($end) + 1;
        }

        // Finalize cycle dates
        if ($this->cycle_start_date && $this->cycle_length) {
            $cycleStart = Carbon::parse($this->cycle_start_date);
            $this->cycle_end_date = $cycleStart
                ->addDays($this->cycle_length - 1)
                ->toDateString();
        }

        // Transition cycle state
        $this->cycle_status = 'completed';
        $this->save();

        // Initialize subsequent cycle
        $nextStart = $end->copy()->addDay();
        return self::create([
            'patient_id'       => $this->patient_id,
            'cycle_start_date' => $nextStart->toDateString(),
            'cycle_length'    => $newCycleLength ?? $this->cycle_length,
            'period_length'   => $newPeriodLength ?? $this->period_length,
            'symptoms'        => [],
            'cycle_status'    => 'new',
            'month'           => $nextStart->format('Y-m'),
        ]);
    }
}