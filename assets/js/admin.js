(function($) {
    'use strict';

    $(document).ready(function() {
        var frame;
        
        // Open media uploader when Add Attachment button is clicked
        $('#waa-add-attachment').on('click', function(e) {
            e.preventDefault();

            // If the media frame already exists, reopen it
            if (frame) {
                frame.open();
                return;
            }

            // Create the media frame
            frame = wp.media({
                title: waaData.title,
                button: {
                    text: waaData.button
                },
                multiple: false,
                library: {
                    type: ['application/pdf', 'image', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                }
            });

            // When an attachment is selected, add it to our list
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                
                var attachmentItem = $(
                    '<div class="waa-attachment-item" data-id="' + attachment.id + '">' +
                        '<input type="hidden" name="waa_attachments[]" value="' + attachment.id + '">' +
                        '<span class="waa-attachment-title">' + attachment.title + '</span>' +
                        '<a href="' + attachment.url + '" target="_blank" class="waa-view-link button button-secondary">View</a>' +
                        '<button type="button" class="waa-remove-button button button-secondary">Remove</button>' +
                    '</div>'
                );
                
                $('#waa-attachments-container').append(attachmentItem);
                $('.waa-no-attachments').hide();
            });

            // Open the modal
            frame.open();
        });

        // Handle removing attachments
        $(document).on('click', '.waa-remove-button', function() {
            $(this).closest('.waa-attachment-item').remove();
            
            if ($('#waa-attachments-container').children().length === 0) {
                $('.waa-no-attachments').show();
            }
        });
    });

})(jQuery);