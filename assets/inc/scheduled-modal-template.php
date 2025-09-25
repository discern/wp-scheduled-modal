<?php

defined('ABSPATH') || exit;

/** @var string $cookie_name */

/** @var int $callout */
/** @var int $supporting */
/** @var int $extra */
/** @var int $delay */
/** @var int $suppress */
/** @var string $bg_image */
/** @var string $title */
/** @var string $button_text */
/** @var string $button_href */
/** @var string $target_attr */
/** @var string $gradient */

?>
<div class="kwp-modal"
     id="<?php echo esc_attr($cookie_name) ?>"
     data-delay="<?php echo esc_attr($delay) ?>"
     data-suppress="<?php echo esc_attr($suppress) ?>"
     data-ends="<?php echo esc_attr($ends ?? '') ?>"
     style="display:none"
>
    <div
            class="kwp-modal-wrapper"
            style="background-color:#000;<?php echo !empty($bg_image) ? 'background-image:url(' . esc_attr(esc_url($bg_image)) . ')' : '' ?>"
    >
        <div class="kwp-modal-container" style="<?php echo !empty($gradient) ? 'background-image: ' . esc_attr($gradient) : '' ?>">
            <div class="kwp-modal-mid">
                <div class="kwp-modal-content <?php echo ($text_shadow ?? 0 == 1 ? 'kwp-modal-text-shadow' : '') . ' ' . esc_attr($text_color ?? '') ?>">
                    <div class="kwp-modal-inner">
                        <header>
                            <h4><?php echo esc_html($title) ?></h4>
                        </header>
                        <main>
                            <p class="kwp-modal-lead">
                                <?php echo wp_kses($callout, ['br' => []]) ?>
                            </p>
                            <p class="kwp-modal-supporting">
                                <?php echo wp_kses($supporting, $this->getAllowedTags()) ?>
                            </p>
                            <p class="kwp-modal-extra">
                                <?php echo wp_kses($extra, $this->getAllowedTags()) ?>
                            </p>
                            <p class="kwp-button-group">
                                <?php if ($button_text && $button_href): ?>
                                    <a href="<?php echo esc_url($button_href) ?>"
                                       class="kwp-suppress kwp-button <?php echo esc_attr($button_color ?? '') ?>"
                                            <?php echo $target_attr ? 'target="_blank" rel="noopener noreferrer"' : '' ?>
                                    >
                                        <?php echo esc_html($button_text) ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </main>
                    </div>
                    <footer>
                        <button type="button" class="kwp-suppress kwp-close-x" aria-label="Close Dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>
                        </button>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>
