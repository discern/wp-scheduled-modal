<?php defined('ABSPATH') || exit; ?>

<div id="kwp-modal-metabox-form">
    <!-- Modal content -->
    <p class="notice notice-warning kwp-notice">
        ☝️The title is included in the modal. Please ensure it is concise and relevant!
    </p>
    <p class="notice notice-info kwp-notice kwp-icon">
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                <!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                <path d="M64 416L64 192C64 139 107 96 160 96L480 96C533 96 576 139 576 192L576 416C576 469 533 512 480 512L360 512C354.8 512 349.8 513.7 345.6 516.8L230.4 603.2C226.2 606.3 221.2 608 216 608C202.7 608 192 597.3 192 584L192 512L160 512C107 512 64 469 64 416z"/>
            </svg>
        </span>
        The following three fields are all optional, but at least one of them <em>should</em> be filled in to provide content for the modal.
        While there are no explicit length limits on these fields, it's best to keep the text concise for optimal display on various screen sizes, and to avoid the text overflowing the popup.
    </p>
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_callout">Callout/Lead-in Text:</label>
            <textarea name="_kwp_modal_callout" id="kwp_modal_callout" class="widefat" rows="2"><?php echo esc_textarea($meta['callout'] ?? '') ?></textarea>
            <small>Appears below the title in <strong style="font-size: 115%">bold, slightly larger-than-normal text</strong> (but smaller than the title).</small>
        </div>
    </div>
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_supporting">Supporting Text/Details:</label>
            <textarea name="_kwp_modal_supporting" id="kwp_modal_supporting" class="widefat" rows="4"><?php echo esc_textarea($meta['supporting']) ?></textarea>
            <small>Short description, supporting text, location, date/time, etc. Normal-sized text.</small>
        </div>
    </div>
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_extra">Extra Text:</label>
            <textarea name="_kwp_modal_extra" id="kwp_modal_extra" class="widefat" rows="3"><?php echo esc_textarea($meta['extra']) ?></textarea>
            <small>Any extra text in addition to the description.</small>
        </div>
    </div>

    <div>
        <p class="notice notice-info kwp-notice kwp-icon">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                    <!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64zM296 184L296 320C296 328 300 335.5 306.7 340L402.7 404C413.7 411.4 428.6 408.4 436 397.3C443.4 386.2 440.4 371.4 429.3 364L344 307.2L344 184C344 170.7 333.3 160 320 160C306.7 160 296 170.7 296 184z"/>
                </svg>
            </span>
            Timing
        </p>
    </div>

    <!-- Start/End time -->
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_start_time">Start Time (required):</label>
            <input type="datetime-local" name="_kwp_modal_start_time" id="kwp_modal_start_time" value="<?php echo esc_attr($meta['start_time']) ?>" class="widefat" required>
            <small>Required for the popup to appear. To have it appear immediately, selet a date in the past.</small>
        </div>
        <div>
            <label for="kwp_modal_end_time">End Time:</label>
            <input type="datetime-local" name="_kwp_modal_end_time" id="kwp_modal_end_time" value="<?php echo esc_attr($meta['end_time']) ?>" class="widefat">
            <small>Optional; if set, the popup will stop appearing after that time.</small>
        </div>
    </div>

    <!--Delay and suppress times-->
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_delay">Delay before showing popup (seconds):</label>
            <input type="number" name="_kwp_modal_delay" id="kwp_modal_delay" value="<?php echo esc_attr($meta['delay']) ?>" min="0" class="widefat">
            <small>Generally, it's recommended to wait a few seconds after page load before showing the popup.</small>
        </div>
        <div>
            <label for="kwp_modal_suppress" style="display:block">Suppress popup after closing (days):</label>
            <input type="number" name="_kwp_modal_suppress" id="kwp_modal_suppress" value="<?php echo esc_attr($meta['suppress']) ?>" min="1" class="widefat">
            <small>How long the popup will remain hidden after the visitor closes it. (Saving a modal resets this for all users.)</small>
        </div>
    </div>

    <div>
        <p class="notice notice-info kwp-notice kwp-icon">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                    <!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M451.5 160C434.9 160 418.8 164.5 404.7 172.7C388.9 156.7 370.5 143.3 350.2 133.2C378.4 109.2 414.3 96 451.5 96C537.9 96 608 166 608 252.5C608 294 591.5 333.8 562.2 363.1L491.1 434.2C461.8 463.5 422 480 380.5 480C294.1 480 224 410 224 323.5C224 322 224 320.5 224.1 319C224.6 301.3 239.3 287.4 257 287.9C274.7 288.4 288.6 303.1 288.1 320.8C288.1 321.7 288.1 322.6 288.1 323.4C288.1 374.5 329.5 415.9 380.6 415.9C405.1 415.9 428.6 406.2 446 388.8L517.1 317.7C534.4 300.4 544.2 276.8 544.2 252.3C544.2 201.2 502.8 159.8 451.7 159.8zM307.2 237.3C305.3 236.5 303.4 235.4 301.7 234.2C289.1 227.7 274.7 224 259.6 224C235.1 224 211.6 233.7 194.2 251.1L123.1 322.2C105.8 339.5 96 363.1 96 387.6C96 438.7 137.4 480.1 188.5 480.1C205 480.1 221.1 475.7 235.2 467.5C251 483.5 269.4 496.9 289.8 507C261.6 530.9 225.8 544.2 188.5 544.2C102.1 544.2 32 474.2 32 387.7C32 346.2 48.5 306.4 77.8 277.1L148.9 206C178.2 176.7 218 160.2 259.5 160.2C346.1 160.2 416 230.8 416 317.1C416 318.4 416 319.7 416 321C415.6 338.7 400.9 352.6 383.2 352.2C365.5 351.8 351.6 337.1 352 319.4C352 318.6 352 317.9 352 317.1C352 283.4 334 253.8 307.2 237.5z"/>
                </svg>
            </span>
            The button is optional. If both a page/post and an external URL are provided, the page/post will take precedence.
        </p>
    </div>

    <!--Button URL-->
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_target_post_id">Page/Post:</label><?php
            wp_dropdown_pages([
                    'name'              => '_kwp_modal_target_post_id',
                    'show_option_none'  => '-- None --',
                    'option_none_value' => '',
                    'selected'          => intval($meta['target_post_id']),
                    'class'             => 'widefat',
            ]);
            ?>
        </div>
        <div>
            <label for="kwp_modal_target_url">External URL (optional; internal page takes precedence):</label>
            <input type="url" name="_kwp_modal_target_url" id="kwp_modal_target_url" value="<?php echo esc_attr($meta['target_url']) ?>" class="widefat">
        </div>
    </div>

    <!--Button text and color-->
    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_button_text">Button Text:</label>
            <input type="text" name="_kwp_modal_button_text" id="kwp_modal_button_text" value="<?php echo esc_attr($meta['button_text']) ?>" class="widefat">
        </div>
        <div>
            <label for="kwp_modal_button_color">Button Color:</label>
            <select name="_kwp_modal_button_color" id="kwp_modal_button_color" class="widefat">
                <?php foreach ($this->getButtonColors() as $color) { ?>
                    <?php $color_class = strtolower("kwp-button-$color") ?>
                    <option <?php echo($meta['button_color'] === $color_class ? 'selected' : '') ?> value="<?php echo esc_attr($color_class) ?>" style="color:<?php echo esc_attr(strtolower($color)) ?>;">
                        <?php echo esc_html($color) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div>
        <p class="notice notice-info kwp-notice kwp-icon">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                    <!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/>
                </svg>
            </span>

            The menu item is optional.
            If a label is provided, it will be <strong>appended</strong> to the main menu.
            The CSS class is also optional, but can be used to style the menu item differently.
            If both fields are filled in, the menu item will be added with the specified label and class.
            If only the label is filled in, it will be added with no special class.
            If only the class is filled in, it will be ignored since there is no label to display.
            Note that the menu item will link to the same URL as the button in the popup.
        </p>
    </div>

    <div class="kwp-meta-flex">
        <div>
            <label for="kwp_modal_menu_item_label">Menu item label (optional)</label>
            <input type="text" name="_kwp_modal_menu_item_label" id="kwp_modal_menu_item_label" value="<?php echo esc_attr($meta['menu_item_label']) ?>" maxlength="16" class="widefat" placeholder="e.g. Donate">
            <small>Will be appended to the main menu if this input has any text</small>
        </div>
        <div>
            <label for="kwp_modal_menu_item_class">CSS Class (optional)</label>
            <input type="text" name="_kwp_modal_menu_item_class" id="kwp_modal_menu_item_class" value="<?php echo esc_attr($meta['menu_item_class']) ?>" maxlength="16" class="widefat" placeholder="e.g. wine-event or donate">
            <small>Examples: <code class="wine-event">wine-event</code>, <code class="donate">donate</code></small>
        </div>
    </div>

    <div>
        <p class="notice notice-info kwp-notice kwp-icon">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                    <!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M160 96C124.7 96 96 124.7 96 160L96 480C96 515.3 124.7 544 160 544L480 544C515.3 544 544 515.3 544 480L544 160C544 124.7 515.3 96 480 96L160 96zM224 176C250.5 176 272 197.5 272 224C272 250.5 250.5 272 224 272C197.5 272 176 250.5 176 224C176 197.5 197.5 176 224 176zM368 288C376.4 288 384.1 292.4 388.5 299.5L476.5 443.5C481 450.9 481.2 460.2 477 467.8C472.8 475.4 464.7 480 456 480L184 480C175.1 480 166.8 475 162.7 467.1C158.6 459.2 159.2 449.6 164.3 442.3L220.3 362.3C224.8 355.9 232.1 352.1 240 352.1C247.9 352.1 255.2 355.9 259.7 362.3L286.1 400.1L347.5 299.6C351.9 292.5 359.6 288.1 368 288.1z"/>
                </svg>
            </span>
            The background image and/or gradient overlay are optional, but at least one of them should be provided to ensure the text is readable, and the popup is visually appealing.
        </p>
    </div>


    <!--Background image using WP Media Uploader and gradient overlay-->
    <div class="kwp-meta-flex">
        <div>
            <!-- Image Selector -->
            <label for="kwp_modal_bg_image_button">Background Image:</label>
            <input type="hidden" id="kwp_modal_bg_image" name="_kwp_modal_bg_image" value="<?php echo esc_attr($image_id ?? '') ?>">
            <button type="button" class="button" id="kwp_modal_bg_image_button">Select / Upload Image</button>
            <!-- / Image Selector -->

            <div class="kwp-meta-flex">
                <div>
                    <label for="kwp_gradient_primary">Primary Color</label>
                    <input type="text" class="color-field" name="_kwp_modal_gradient_primary" id="kwp_gradient_primary" value="<?php echo esc_attr($meta['gradient']['primary'] ?? '#4a044e') ?>">
                </div>

                <div>
                    <label for="kwp_gradient_secondary">Secondary Color</label>
                    <input type="text" class="color-field" name="_kwp_modal_gradient_secondary" id="kwp_gradient_secondary" value="<?php echo esc_attr($meta['gradient']['secondary'] ?? '#111827') ?>">
                </div>
            </div>

            <div class="kwp-meta-flex">
                <div>
                    <label for="kwp_modal_gradient_direction">Direction</label>
                    <select name="_kwp_modal_gradient_direction" id="kwp_modal_gradient_direction" class="widefat">
                        <option value="to right"<?php echo $meta['gradient']['direction'] == 'to right' ? ' selected' : '' ?>>Left to Right</option>
                        <option value="to left"<?php echo $meta['gradient']['direction'] == 'to left' ? ' selected' : '' ?>>Right to Left</option>
                        <option value="to top"<?php echo $meta['gradient']['direction'] == 'to top' ? ' selected' : '' ?>>Bottom to Top</option>
                        <option value="to bottom"<?php echo $meta['gradient']['direction'] == 'to bottom' ? ' selected' : '' ?>>Top to Bottom</option>
                    </select>
                </div>
                <div>
                    <label for="kwp_modal_gradient_opacity">Opacity</label>
                    <input
                            class="widefat"
                            id="kwp_modal_gradient_opacity"
                            max="1"
                            min="0"
                            name="_kwp_modal_gradient_opacity"
                            step="0.05"
                            type="range"
                            value="<?php echo esc_attr($meta['gradient']['opacity'] ?? '0.6') ?>"
                    >
                </div>
            </div>
            <div class="kwp-meta-flex">
                <div>
                    <label for="kwp_modal_text_color">Text Color</label>
                    <select name="_kwp_modal_text_color" id="kwp_modal_text_color" class="widefat">
                        <option value="kwp-modal-text-light"<?php echo $meta['text_color'] === 'kwp-modal-text-light' ? ' selected' : '' ?>>Light</option>
                        <option value="kwp-modal-text-dark"<?php echo $meta['text_color'] === 'kwp-modal-text-dark' ? ' selected' : '' ?>>Dark</option>
                    </select>
                </div>
                <div>
                    <label for="kwp_modal_text_shadow">Text Shadow</label>
                    <select name="_kwp_modal_text_shadow" id="kwp_modal_text_shadow" class="widefat">
                        <option value="0"<?php echo $meta['text_shadow'] === '0' ? ' selected' : '' ?>>Off</option>
                        <option value="1"<?php echo $meta['text_shadow'] === '1' ? ' selected' : '' ?>>On</option>
                    </select>
                </div>
            </div>
        </div>

        <div>
            <div id="kwp-modal-image-preview">
                <img id="kwp_modal_bg_image_preview"
                     src="<?php echo esc_url($image_url ?? '') ?>"
                     class="kwp-meta-image-preview"
                     alt="Modal Background Image Preview"
                >

                <div id="kwp-image-overlay"></div>
                <div id="kwp-modal-text-preview"
                     class="kwp-modal-text-preview
                            <?php echo $meta['text_shadow'] == 1 ? ' kwp-modal-text-shadow' : '' ?>
                            <?php echo esc_html($meta['text_color']) ?>
                ">
                    <div id="kwp_modal_title-preview">
                        <?php echo esc_html(wp_strip_all_tags($title ?? '')) ?>
                    </div>
                    <div id="kwp_modal_callout-preview">
                        <?php echo esc_html(wp_strip_all_tags($meta['callout'])) ?>
                    </div>
                    <div id="kwp_modal_supporting-preview">
                        <?php echo esc_html(wp_strip_all_tags($meta['supporting'])) ?>
                    </div>
                    <div id="kwp_modal_extra-preview">
                        <?php echo esc_html(wp_strip_all_tags($meta['extra'])) ?>
                    </div>
                    <div id="kwp_modal_button-preview">
                        <a href="<?php echo esc_url($button_href ?? '') ?>" class="kwp-button <?php echo esc_attr($meta['button_color'] ?: 'kwp-button-red') ?>">
                            <?php echo esc_html($meta['button_text'] ?: 'Button Text') ?>
                        </a>
                    </div>
                </div>
            </div>
            <small class="kwp-preview-note">
                <strong>This is just a rough preview!</strong>
                The actual appearance of the popup will vary,
                based on your <strong>theme</strong> and the
                <strong>device</strong> used to view it.
            </small>
        </div>
    </div>
</div>
