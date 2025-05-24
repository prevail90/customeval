define(['jquery', 'core/ajax', 'core/notification', 'core/log'], function($, ajax, notification, log) {
    return {
        init: function() {
            var self = this;

            // Register offline handlers
            if (typeof M !== 'undefined') {
                M.mod_customeval = {
                    offline: {
                        cacheEvaluations: function(evaluations) {
                            // Store evaluations in local storage
                            localStorage.setItem('mod_customeval_pending', JSON.stringify(evaluations));
                        },
                        syncPending: function() {
                            // Sync pending evaluations when online
                            var pending = JSON.parse(localStorage.getItem('mod_customeval_pending') || []);
                            pending.forEach(function(evaluation) {
                                self.submitEvaluation(evaluation);
                            });
                            localStorage.removeItem('mod_customeval_pending');
                        }
                    }
                };
            }
        },

        submitEvaluation: function(evaluation) {
            var promises = ajax.call([{
                methodname: 'mod_customeval_submit_evaluation',
                args: evaluation
            }]);

            promises[0].done(function(response) {
                log.info('Evaluation synced:', response);
            }).fail(function(error) {
                notification.exception(error);
            });
        }
    };
});
