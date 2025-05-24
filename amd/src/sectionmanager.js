define(['jquery', 'core/ajax', 'core/templates', 'core/notification'],
       function($, ajax, templates, notification) {
           return {
               init: function(contextid) {
                   var self = this;
                   var container = $('#section-management');

                   // Initialize section management UI
                   self.loadExistingSections(contextid);

                   // Event handlers
                   container.on('click', '.add-section', function() {
                       self.addNewSection();
                   });

                   container.on('click', '.delete-section', function() {
                       $(this).closest('.section-container').remove();
                       self.updateFormData();
                   });

                   container.on('click', '.add-option', function() {
                       self.addNewOption($(this).closest('.section-container'));
                   });

                   container.on('click', '.delete-option', function() {
                       $(this).closest('.option-row').remove();
                       self.updateFormData();
                   });

                   // Update hidden field before form submission
                   $('#id_submitbutton').on('click', function() {
                       self.updateFormData();
                   });
               },

               loadExistingSections: function(contextid) {
                   // Load sections via AJAX if editing existing activity
                   if (typeof initialData !== 'undefined') {
                       initialData.sections.forEach(function(section) {
                           this.addSectionToUI(section);
                       }.bind(this));
                   }
               },

               addNewSection: function() {
                   var sectionId = 'new-section-' + Date.now();
                   templates.render('mod_customeval/section_editor', {
                       sectionid: sectionId,
                       sectionname: '',
                       sectiondescription: '',
                       options: []
                   }).then(function(html) {
                       $('#section-management').append(html);
                       return;
                   }).catch(notification.exception);
               },

               addNewOption: function(section) {
                   var optionId = 'option-' + section.attr('id') + '-' + Date.now();
                   templates.render('mod_customeval/option_editor', {
                       optionid: optionId,
                       optiontext: '',
                       optionvalue: '',
                       weight: 0
                   }).then(function(html) {
                       section.find('.section-options').append(html);
                       return;
                   }).catch(notification.exception);
               },

               updateFormData: function() {
                   var sections = [];

                   $('.section-container').each(function() {
                       var section = {
                           name: $(this).find('.section-name').val(),
                                                description: $(this).find('.section-description').val(),
                                                options: []
                       };

                       $(this).find('.option-row').each(function() {
                           section.options.push({
                               text: $(this).find('.option-text').val(),
                                                value: $(this).find('.option-value').val(),
                                                weight: parseFloat($(this).find('.option-weight').val())
                           });
                       });

                       sections.push(section);
                   });

                   $('#id_sectionsjson').val(JSON.stringify(sections));
               }
           };
       });
