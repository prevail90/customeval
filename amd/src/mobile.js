define([
    'jquery',
    'core/ajax',
    'core/templates',
    'core/notification',
    'core/str'
], function($, ajax, templates, notification, str) {
    return {
        init: function() {
            this.setupEventListeners();
            this.loadInitialData();
        },

        setupEventListeners: function() {
            // Handle student selection
            $(document).on('click', '.student-list-item', (e) => this.loadStudentEvaluation(e));
            
            // Handle form submission
            $(document).on('submit', '#mod-customeval-mobile-form', (e) => this.handleFormSubmit(e));
        },

        loadInitialData: function() {
            const container = $('#mod-customeval-mobile-container');
            const cmid = container.data('cmid');

            ajax.call([{
                methodname: 'mod_customeval_get_mobile_data',
                args: { cmid: cmid },
                done: (response) => {
                    templates.render('mod_customeval/mobile_view_activity', response)
                        .then(html => $('#mod-customeval-mobile-container').html(html))
                        .catch(notification.exception);
                },
                fail: (error) => notification.exception(error)
            }]);
        },

        loadStudentEvaluation: function(e) {
            const studentId = $(e.currentTarget).data('userid');
            const cmid = $('#mod-customeval-mobile-container').data('cmid');

            ajax.call([{
                methodname: 'mod_customeval_load_evaluation',
                args: { userid: studentId, cmid: cmid },
                done: (response) => {
                    templates.render('mod_customeval/mobile_evaluation_form', response)
                        .then(html => $('#evaluation-container').html(html))
                        .catch(notification.exception);
                },
                fail: (error) => notification.exception(error)
            }]);
        },

        handleFormSubmit: function(e) {
            e.preventDefault();
            const formData = $(e.target).serialize();

            ajax.call([{
                methodname: 'mod_customeval_submit_evaluation',
                args: formData,
                done: (response) => {
                    notification.addNotification({
                        message: 'Evaluation saved!',
                        type: 'success'
                    });
                    this.loadInitialData(); // Refresh the list
                },
                fail: (error) => notification.exception(error)
            }]);
        }
    };
});
