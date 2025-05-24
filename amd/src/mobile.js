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
            // Delegated event for student selection
            $(document).on('click', '.student-list-item', (e) => this.loadStudentEvaluation(e));
            
            // Delegated event for form submission
            $(document).on('submit', '#mod-customeval-mobile-form', (e) => this.handleFormSubmit(e));
        },

        loadInitialData: function() {
            const container = $('#mod-customeval-mobile-container');
            const cmid = container.data('cmid');
            const courseid = container.data('courseid');

            ajax.call([{
                methodname: 'mod_customeval_get_mobile_data',
                args: { 
                    cmid: cmid,
                    courseid: courseid
                },
                done: (response) => {
                    this.renderActivityView(response);
                },
                fail: (error) => {
                    notification.exception(error);
                    this.showErrorMessage('loaderror');
                }
            }]);
        },

        renderActivityView: function(response) {
            templates.render('mod_customeval/mobile_view_activity', response)
                .then((html) => {
                    $('#mod-customeval-mobile-container').html(html);
                    return str.get_string('activityloaded', 'mod_customeval');
                })
                .then((message) => {
                    notification.addNotification({
                        message: message,
                        type: 'success'
                    });
                })
                .catch((error) => {
                    notification.exception(error);
                    this.showErrorMessage('rendererror');
                });
        },

        loadStudentEvaluation: function(e) {
            const studentId = $(e.currentTarget).data('userid');
            const cmid = $('#mod-customeval-mobile-container').data('cmid');

            ajax.call([{
                methodname: 'mod_customeval_get_evaluation_form',
                args: {
                    userid: studentId,
                    cmid: cmid
                },
                done: (response) => {
                    this.renderEvaluationForm(response);
                },
                fail: (error) => {
                    notification.exception(error);
                    this.showErrorMessage('loaderror');
                }
            }]);
        },

        renderEvaluationForm: function(response) {
            templates.render('mod_customeval/mobile_evaluation_form', response)
                .then((html) => {
                    $('#evaluation-container').html(html);
                    return str.get_string('formloaded', 'mod_customeval');
                })
                .then((message) => {
                    notification.addNotification({
                        message: message,
                        type: 'success'
                    });
                })
                .catch((error) => {
                    notification.exception(error);
                    this.showErrorMessage('rendererror');
                });
        },

        handleFormSubmit: function(e) {
            e.preventDefault();
            const form = $(e.target);
            const formData = form.serialize();

            str.get_strings([
                { key: 'saving', component: 'mod_customeval' },
                { key: 'savesuccess', component: 'mod_customeval' },
                { key: 'saveerror', component: 'mod_customeval' }
            ]).then((strings) => {
                notification.addNotification({
                    message: strings[0],
                    type: 'info'
                });

                ajax.call([{
                    methodname: 'mod_customeval_submit_evaluation',
                    args: formData,
                    done: (response) => {
                        notification.addNotification({
                            message: strings[1],
                            type: 'success'
                        });
                        this.loadInitialData(); // Refresh the list
                    },
                    fail: (error) => {
                        notification.exception(error);
                        notification.addNotification({
                            message: strings[2],
                            type: 'error'
                        });
                    }
                }]);
            }).catch((error) => {
                notification.exception(error);
                this.showErrorMessage('genericerror');
            });
        },

        showErrorMessage: function(errorKey) {
            str.get_string(errorKey, 'mod_customeval')
                .then((message) => {
                    notification.addNotification({
                        message: message,
                        type: 'error'
                    });
                })
                .catch(() => {
                    notification.addNotification({
                        message: 'An unknown error occurred',
                        type: 'error'
                    });
                });
        }
    };
});
