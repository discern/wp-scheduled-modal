jQuery(document).ready(function ($) {
    $('.button-color-field').wpColorPicker();

    let frame;

    $('#kwp_modal_bg_image_button').on('click', function (e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select Background Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();
            $('#kwp_modal_bg_image').val(attachment.id);
            $('#kwp_modal_bg_image_preview').attr('src', attachment.url);
        });

        frame.open();
    });

    function hexToRgba(hex, alpha = 1) {
        let r = 0, g = 0, b = 0;
        if (hex.length === 4) {
            r = parseInt(hex[1] + hex[1], 16);
            g = parseInt(hex[2] + hex[2], 16);
            b = parseInt(hex[3] + hex[3], 16);
        } else if (hex.length === 7) {
            r = parseInt(hex.slice(1, 3), 16);
            g = parseInt(hex.slice(3, 5), 16);
            b = parseInt(hex.slice(5, 7), 16);
        }
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function updateOverlay() {
        console.log('Updating overlay preview...');
        const buttonClass = $('#kwp_modal_button_color').val();
        const buttonText = $('#kwp_modal_button_text').val();
        const callout = $('#kwp_modal_callout').val();
        const direction = $('#kwp_modal_gradient_direction').val() || 'to right';
        const extra = $('#kwp_modal_extra').val();
        const opacity = parseFloat($('#kwp_modal_gradient_opacity').val()) || 0.6;
        const primaryHex = $('#kwp_gradient_primary').val() || '#4a044e';
        const primaryRgba = hexToRgba(primaryHex, opacity);
        const secondaryHex = $('#kwp_gradient_secondary').val() || '#111827';
        const secondaryRgba = hexToRgba(secondaryHex, opacity);
        const supportingText = $('#kwp_modal_supporting').val();
        const textColor = $('#kwp_modal_text_color').val() || '#fff';
        const textShadow = $('#kwp_modal_text_shadow').val().toString() === '1' ? 'kwp-modal-text-shadow' : '';
        const title = $('#title').val();
        const gradientCss = `linear-gradient(${direction}, ${primaryRgba}, ${secondaryRgba})`;

        $('#kwp-image-overlay').css('background', gradientCss);
        $('#kwp_modal_title-preview').html(title || 'Your Title Here');
        $('#kwp_modal_callout-preview').html(callout || 'Your Callout Here');
        $('#kwp_modal_supporting-preview').html(supportingText || 'Supporting text goes here.');
        $('#kwp_modal_extra-preview').html(extra || 'Additional information can be placed here.');
        $('#kwp-modal-text-preview').removeClass().addClass(`kwp-modal-text-preview ${textColor} ${textShadow}`);

        if (buttonText && buttonClass) {
            console.log('Updating button preview...');
            $('#kwp_modal_button-preview > .kwp-button')
                .text(buttonText || 'Go »')
                .removeClass()
                .addClass('kwp-button ' + buttonClass);
        }
    }

    $('.color-field').wpColorPicker({
        palettes: [
            '#111827',
            '#fff',
            '#4a044e', // Pinot Noir
            '#b8860b', // Dark Goldenrod
            '#006980', // Deep Cerulean
        ],
        change: function () {
            setTimeout(updateOverlay, 0);
        }
    });

    $('#kwp-modal-metabox-form').find('input, textarea, select').on('keyup change input', function () {
        setTimeout(updateOverlay, 0);
    });

    $('#title').on('keyup change input', function () {
        setTimeout(updateOverlay, 0);
    });

    updateOverlay();

    function getStartTime() {
        const startTimeInput = document.getElementById('_kwp_modal_start_time');
        return new Date(startTimeInput.value);
    }

    function getEndTime() {
        const endTimeInput = document.getElementById('_kwp_modal_end_time');
        return endTimeInput.value ? new Date(endTimeInput.value) : null;
    }

    function modalIsActive() {
        return (getStartTime() <= new Date()) && (!getEndTime() || getEndTime() > new Date());
    }

    let kwpModalIsActive = modalIsActive();

    $('#kwp_modal_start_time, #kwp_modal_end_time').on('change', function () {
        kwpModalIsActive = modalIsActive();
    });

    const $publishBtn = $('#publish');

    if ($publishBtn.val() === 'Update') {
        // Create tooltip element
        const $tooltip = $('<div>', {
            text: 'Clicking Update will force this popup to (re)appear for all users.',
            css: {
                position: 'absolute',
                background: '#ffc',
                border: '1px solid #ccc',
                padding: '8px',
                'border-radius': '4px',
                'box-shadow': '0 0 5px rgba(0,0,0,0.2)',
                'z-index': 9999,
                'max-width': '250px',
                display: 'none',
                'font-size': '13px',
                color: '#23282d',
            }
        }).appendTo('body');

        // Show tooltip on hover
        $publishBtn.on('mouseenter', function () {
            if (!kwpModalIsActive) {
                return;
            }

            const offset = $publishBtn.offset();
            $tooltip.css({
                top: offset.top - $tooltip.outerHeight() - 6,
                left: offset.left + ($publishBtn.outerWidth() / 2) - ($tooltip.outerWidth() / 2),
                display: 'block'
            });
        });

        // Hide tooltip on mouse leave
        $publishBtn.on('mouseleave', function () {
            $tooltip.hide();
        });
    }

    $('#kwp_modal_suppress').on('input', function () {
        if (this.validity.rangeUnderflow) {
            this.setCustomValidity("A minimum value of 1 is required, lest the modal appear on every page load.");
        } else {
            this.setCustomValidity(""); // Reset to default
        }
    });
});
