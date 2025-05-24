import {math} from 'mathjs';
import localforage from 'localforage';

export const init = async() => {
    // Load activity data
    const [criteria, formula] = await Promise.all([
        localforage.getItem('customeval_criteria'),
        localforage.getItem('customeval_formula')
    ]);

    /**
     * Calculate grade offline using math.js
     * @param {Object} selectedAnswers Map of {criterionid: answerid}
     */
    const calculateGrade = (selectedAnswers) => {
        // Create answer value map {answerid: value}
        const answerValues = criteria.reduce((acc, criterion) => {
            criterion.answers.forEach(answer => {
                acc[answer.answerid] = parseFloat(answer.value);
            });
            return acc;
        }, {});

        // Create aggregation maps
        const aggregations = {
            sum: {},
            count: {}
        };

        // Populate aggregations from selected answers
        Object.values(selectedAnswers).forEach(answerid => {
            aggregations.sum[answerid] = (aggregations.sum[answerid] || 0) + answerValues[answerid];
            aggregations.count[answerid] = (aggregations.count[answerid] || 0) + 1;
        });

        // Replace functions in formula
        let expr = formula.replace(/(sum|avg|count|max|min|median)\(([a-z0-9,]+)\)/g, 
            (match, func, args) => {
                const ids = args.split(',').map(id => id.trim());
                const values = ids.map(id => {
                    switch(func) {
                        case 'sum': return aggregations.sum[id] || 0;
                        case 'count': return aggregations.count[id] || 0;
                        case 'avg': 
                            const sum = aggregations.sum[id] || 0;
                            const count = aggregations.count[id] || 1;
                            return count ? sum / count : 0;
                        default: return 0;
                    }
                });
                
                switch(func) {
                    case 'max': return Math.max(...values);
                    case 'min': return Math.min(...values);
                    case 'median': {
                        const sorted = [...values].sort((a, b) => a - b);
                        const mid = Math.floor(sorted.length / 2);
                        return sorted.length % 2 !== 0 ? sorted[mid] : (sorted[mid - 1] + sorted[mid]) / 2;
                    }
                    default: return values.reduce((a, b) => a + b, 0);
                }
            }
        );

        // Evaluate final expression
        try {
            return math.evaluate(expr);
        } catch (e) {
            console.error('Formula error:', e);
            return 0;
        }
    };

    return {
        calculateGrade,
        criteria,
        formula
    };
};
