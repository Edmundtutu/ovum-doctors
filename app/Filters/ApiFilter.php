<?php
namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter {
    protected $allowedparams = [];
    protected $column_Map = [];
    
    protected $operator_Map = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=', 
        'ne' => '!=',
        'within' => 'between' 
    ];

    public function transform(Request $request) {
        $eloQuery = [];

        foreach ($this->allowedparams as $param => $operators) {
            $query = $request->query($param);
            
            if (!isset($query)) {
                continue;
            }

            $column = $this->column_Map[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $value = $query[$operator];
                    
                    // Handle range operator
                    if ($operator === 'within') {
                        $values = explode(',', $value);
                        if (count($values) !== 2) continue;  // Skip invalid format
                        
                        $eloQuery[] = [
                            $column, 
                            $this->operator_Map[$operator], 
                            [$values[0], $values[1]]
                        ];
                    } else {
                        $eloQuery[] = [
                            $column, 
                            $this->operator_Map[$operator], 
                            $value
                        ];
                    }
                }
            }
        }

        return $eloQuery;
    }
}