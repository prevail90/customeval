define([
    'jquery',
    'core/ajax',
    'core/templates',
    'core/notification',
    'core/str',
    'core/yui'
], function($, ajax, templates, notification, str, Y) {
    // Throttle utility to prevent rapid clicks
    const throttle = Y.throttle;

    return {
        init: function() {
            this.initMobileContainer();
            this.setupEventListeners();
            this.loadInitialData();
        },

        initMobileContainer: function() {
            $('#mod-customeval-mobile-container')
                .attr('role', 'main')
                .attr('aria-live', 'polite');
        },

        setupEventListeners: function() {
            // Throttled student selection handler
            $(document).on('click', '.student-list-item', throttle((e) => {
                this.handleStudentSelection(e);
            }, 1000, { leading: true, trailing: false }));

            // Form submission handler
            $(document).on('submit', '#mod-customeval-mobile-form', (e) => {
                this.handleFormSubmission(e);
            });
        },

        handleStudentSelection: function(e) {
            const $target = $(e.currentTarget);
            this.toggleLoadingState($target, true);
            
            const studentId = $target.data('userid');
            const cmid = this.getContextData('cmid');

            this.loadEvaluationData(studentId, cmid)
                .finally(() => this.toggleLoadingState($target, false));
        },

        loadInitialData: function() {
            const cmid = this.getContextData('cmid');
            this.showGlobalLoading();

            ajax.call([{
                methodname: 'mod_customeval_get_mobile_initial_data',
                args: { cmid: cmid },
                done: (response) => this.renderActivityView(response),
                fail: (error) => this.handleDataError(error)
            }]).always(() => this.hideGlobalLoading());
        },

        renderActivityView: function(response) {
            templates.render('mod_customeval/mobile_view_activity', response)
                .then((html) => {
                    $('#mod-customeval-mobile-container').html(html);
                    this.announceContentUpdate('activityloaded');
                })
                .catch((error) => this.handleRenderError(error));
        },

        loadEvaluationData: function(userId, cmid) {
            return ajax.call([{
                methodname: 'mod_customeval_load_mobile_evaluation',
                args: { userid: userId, cmid: cmid },
                done: (response) => this.renderEvaluationForm(response),
                fail: (error) => this.handleEvaluationError(error)
            }]);
        },

        renderEvaluationForm: function(response) {
            templates.render('mod_customeval/mobile_evaluation_form', response)
                .then((html) => {
                    $('#evaluation-container').html(html);
                    this.announceContentUpdate('formloaded');
                })
                .catch((error) => this.handleRenderError(error));
        },

        handleFormSubmission: function(e) {
            e.preventDefault();
            const $form = $(e.target);
            
            if (!this.validateForm($form)) return;

            const formData = $form.serialize();
            this.showSubmissionLoading($form);

            str.get_strings([
                { key: 'saving', component: 'mod_customeval' },
                { key: 'savesuccess', component: 'mod_customeval' },
                { key: 'saveerror', component: 'mod_customeval' }
            ]).then((strings) => {
                this.showProgressNotification(strings[0]);

                ajax.call([{
                    methodname: 'mod_customeval_submit_mobile_evaluation',
                    args: formData,
                    done: (response) => this.handleSubmissionSuccess(response, strings[1]),
                    fail: (error) => this.handleSubmissionError(error, strings[2])
                }]);
            }).catch((error) => this.handleStringError(error));
        },

        // ============ Helper Methods ============
        validateForm: function($form) {
            if (!$form[0].checkValidity()) {
                $form.addClass('was-validated');
                this.showFormErrors($form);
                return false;
            }
            return true;
        },

        toggleLoadingState: function($element, isLoading) {
            $element.toggleClass('loading', isLoading)
                .attr('aria-busy', isLoading);
        },

        showGlobalLoading: function() {
            $('#mobile-loading-indicator').removeClass('d-none');
        },

        hideGlobalLoading: function() {
            $('#mobile-loading-indicator').addClass('d-none');
        },

        getContextData: function(key) {
            return $('#mod-customeval-mobile-container').data(key);
        },

        announceContentUpdate: function(stringKey) {
            str.get_string(stringKey, 'mod_customeval')
                .then((message) => {
                    $('#mod-customeval-mobile-container')
                        .attr('aria-label', message)
                        .focus();
                });
        },

        // ============ Notification Handlers ============
        showProgressNotification: function(message) {
            notification.addNotification({
                message: message,
                type: 'info',
                announce: true
            });
        },

        handleSubmissionSuccess: function(response, message) {
            notification.addNotification({
                message: message,
                type: 'success',
                announce: true
            });
            this.loadInitialData(); // Refresh view
        },

        handleSubmissionError: function(error, message) {
            notification.exception(error);
            notification.addNotification({
                message: message,
                type: 'error',
                announce: true
            });
        },

        handleDataError: function(error) {
            str.get_string('dataloaderror', 'mod_customeval')
                .then((message) => {
                    notification.alert(message, error.message);
                });
        },

        handleRenderError: function(error) {
            str.get_string('rendererror', 'mod_customeval')
                .then((message) => {
                    notification.alert(message, error.message);
                });
        }
    };
});
