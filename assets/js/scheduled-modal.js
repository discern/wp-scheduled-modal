(function ($) {
    "use strict";

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return null;
    }

    function isModalSuppressed(cookieName) {
        const cookie = getCookie(cookieName);
        if (!cookie) return false;

        const match = cookie.match(/suppressed_until:(.+)/);
        if (!match) return false;

        const expirationDate = new Date(match[1]);
        return new Date() < expirationDate;
    }

    function isModalExpired(endTimestamp) {
        if (!endTimestamp) {
            return false;
        }

        const now = Date.now();
        return now > endTimestamp;
    }

    function setCookie(cName, cValue, expires) {
        document.cookie = cName + "=" + encodeURIComponent(cValue) +
            "; expires=" + expires +
            "; path=/; " + "Domain=." + window.location.hostname +
            "; SameSite=Lax" + (window.location.protocol === 'https:' ? '; Secure' : '');
    }

    $(function () {
        if (typeof kwpModalMenuParams === 'undefined') {
            return;
        }

        const menuID = kwpModalMenuParams?.id ?? null;
        const menuItemLabel = kwpModalMenuParams?.label ?? null;
        const menuItemClass = kwpModalMenuParams?.class ?? null;
        const menuItemHref = kwpModalMenuParams?.href ?? '';

        if (menuID && menuID.length && menuItemLabel && menuItemLabel.length && menuItemHref && menuItemHref.length) {
            const menu = $('#' + menuID);

            if (menu.length) {
                const menuItem = `
                    <li class="${menuItemClass}">
                        <a href="${menuItemHref}">${menuItemLabel}</a>
                    </li>
                `;

                menu.append(menuItem);
            } else {
                console.warn(`Menu with ID '${menuID}' not found.`);
            }
        }
    });

    $(function () {
        const modal = $('.kwp-modal');

        if (modal.length) {
            const modalId = modal.attr('id') || 'default_modal';
            const delay = parseInt(modal.data('delay'), 10);
            const suppress = parseInt(modal.data('suppress'), 10);
            const endTimestamp = modal.data('ends'); // Get end time from data attribute
            const expires = new Date(Date.now() + suppress).toUTCString();

            console.error('endTimestamp:', new Date(endTimestamp).toString());

            // Check if modal should be shown
            if (!isModalSuppressed(modalId) && !isModalExpired(endTimestamp)) {
                setTimeout(() => {
                    modal.fadeIn();
                }, delay);
            }

            modal.find('.kwp-suppress').on('click', function (e) {
                const href = $(this).attr('href');
                const target = $(this).attr('target');

                if (href) {
                    e.preventDefault();
                    setCookie(modalId, `suppressed_until:${expires}`, expires);
                    modal.fadeOut();

                    setTimeout(() => {
                        if (target === '_blank') {
                            window.open(href, '_blank', 'noopener,noreferrer');
                        } else {
                            window.location.href = href;
                        }
                    }, 125);
                } else {
                    // no href, just close the modal
                    setCookie(modalId, `suppressed_until:${expires}`, expires);
                    modal.fadeOut();
                }
            });
        }
    });

    // Allow the escape key to close the modal, if it's open
    $(document).on('keydown', function (e) {
        if (e.key === "Escape") {
            const modal = $('.kwp-modal');
            if (modal.is(':visible')) {
                modal.fadeOut();
            }
        }
    });
})(jQuery);
