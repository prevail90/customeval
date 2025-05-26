<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/mathslib.php');
/**
 * Retrieves the course, course module, and custom evaluation activity.
 *
 * @param int $cmid The course module ID
 * @return array An array containing the course, course module, and custom evaluation activity objects
 */
function customeval_get_activity($cmid) {
    global $DB;

    // Get course module info
    $cm = get_coursemodule_from_id('customeval', $cmid);
    if (!$cm) {
        throw new moodle_exception('invalidcoursemodule');
    }

    // Get course info
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    // Get the custom evaluation activity
    $customeval = $DB->get_record('customeval', array('id' => $cm->instance), '*', MUST_EXIST);

    return array($course, $cm, $customeval);
}

/**
 * Validate custom formula syntax.
 * 
 * @param string $formula The formula to validate
 * @param array $validids Allowed answer IDs (e.g., ['s1', 's2'])
 * @return bool True if valid
 */
function customeval_validate_formula(string $formula, array $validids): bool {
    // Allow Moodle functions and valid answer IDs
    $allowed_functions = 'sum|avg|max|min|median|if|sin|cos|sqrt|pi|log|exp|ceil|floor|round';
    $pattern = '/\b('.$allowed_functions.')\b\(?|\b('.implode('|', $validids).')\b/';
    $sanitized = preg_replace($pattern, '', $formula);
    
    // Check for invalid characters
    return !preg_match('/[^\d\s+\-*\/%^(),.]/', $sanitized);
}

/**
 * Calculate grade using Moodle's math library.
 * 
 * @param string $formula The grading formula
 * @param array $selectedanswers Map of criterionid => answerid
 * @param array $answervalues Map of answerid => numeric value
 * @return float Calculated grade
 */
function customeval_calculate_grade(string $formula, array $selectedanswers, array $answervalues): float {
    global $DB;

    $parser = new core_math_expression();
    $parser->set_suppress_errors(true);

    // Get aggregated values for functions
    $aggregations = [
        'sum' => [],
        'avg' => [],
        'count' => []
    ];

    foreach ($selectedanswers as $criterionid => $answerid) {
        $value = $answervalues[$answerid] ?? 0.0;
        $aggregations['sum'][$answerid] = ($aggregations['sum'][$answerid] ?? 0) + $value;
        $aggregations['count'][$answerid] = ($aggregations['count'][$answerid] ?? 0) + 1;
    }

    // Replace formula functions with aggregated values
    $formula = preg_replace_callback(
        '/(sum|avg|count|max|min|median)\(([a-z0-9,]+)\)/',
        function ($matches) use ($aggregations) {
            $func = $matches[1];
            $args = explode(',', $matches[2]);
            $values = [];
            
            foreach ($args as $arg) {
                $arg = trim($arg);
                switch ($func) {
                    case 'sum': $values[] = $aggregations['sum'][$arg] ?? 0; break;
                    case 'avg': 
                        $sum = $aggregations['sum'][$arg] ?? 0;
                        $count = $aggregations['count'][$arg] ?? 1;
                        $values[] = $count ? $sum / $count : 0;
                        break;
                    case 'count': $values[] = $aggregations['count'][$arg] ?? 0; break;
                }
            }
            
            return match($func) {
                'sum' => array_sum($values),
                'avg' => array_sum($values) / count($values),
                'count' => array_sum($values),
                'max' => max($values ?: [0]),
                'min' => min($values ?: [0]),
                'median' => customeval_median($values),
                default => 0
            };
        },
        $formula
    );

    try {
        return (float)$parser->evaluate($formula);
    } catch (Exception $e) {
        error_log("Formula error: ".$e->getMessage());
        return 0.0;
    }
}

/**
 * Calculate median of values.
 */
function customeval_median(array $values): float {
    if (empty($values)) return 0.0;
    sort($values);
    $mid = floor((count($values) - 1) / 2);
    return (count($values) % 2) ? $values[$mid] : ($values[$mid] + $values[$mid + 1]) / 2;
}
