function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function renderStars(rating) {
    return `${'\u2605'.repeat(rating)}${'\u2606'.repeat(5 - rating)}`;
}

function ensureToast() {
    let toast = document.getElementById('catalogToast');

    if (toast) {
        return toast;
    }

    toast = document.createElement('div');
    toast.id = 'catalogToast';
    toast.className = 'catalog-toast';
    document.body.appendChild(toast);

    return toast;
}

function showToast(message, tone = 'warning') {
    const toast = ensureToast();

    toast.textContent = message;
    toast.dataset.tone = tone;
    toast.classList.add('is-visible');

    window.clearTimeout(showToast.hideTimeout);
    showToast.hideTimeout = window.setTimeout(() => {
        toast.classList.remove('is-visible');
    }, 3200);
}

function showError(message) {
    showToast(message || 'Une erreur est survenue.', 'warning');
}

function syncActionButton(button, options) {
    if (!button) {
        return;
    }

    const {
        isDone,
        idleLabel,
        doneLabel,
    } = options;

    button.disabled = isDone;
    button.textContent = isDone ? doneLabel : idleLabel;
    button.classList.toggle('opacity-70', isDone);
    button.classList.toggle('cursor-not-allowed', isDone);
}

let sharedStorefrontInitialized = false;

function setFeedback(element, message, tone) {
    if (!element) {
        return;
    }

    element.textContent = message;
    element.classList.remove('hidden');
    element.classList.toggle('border-emerald-200', tone === 'success');
    element.classList.toggle('bg-emerald-50', tone === 'success');
    element.classList.toggle('text-emerald-700', tone === 'success');
    element.classList.toggle('border-rose-200', tone === 'error');
    element.classList.toggle('bg-rose-50', tone === 'error');
    element.classList.toggle('text-rose-700', tone === 'error');
}

function initNewsletterModal() {
    const modal = document.querySelector('[data-newsletter-modal]');

    if (!modal) {
        return;
    }

    const form = modal.querySelector('[data-newsletter-form]');
    const feedback = modal.querySelector('[data-newsletter-feedback]');
    const storageKey = 'newsletter-shown';
    let opened = false;

    const openModal = () => {
        if (opened || window.localStorage.getItem(storageKey) === 'true') {
            return;
        }

        opened = true;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        window.requestAnimationFrame(() => {
            modal.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        window.localStorage.setItem(storageKey, 'true');

        window.setTimeout(() => {
            if (!modal.classList.contains('is-open')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.body.style.overflow = '';
        }, 260);
    };

    modal.querySelectorAll('[data-newsletter-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            const payload = new FormData(form);
            const response = await window.axios.post(form.action, payload);
            const code = response.data?.code;

            setFeedback(
                feedback,
                code ? `Inscription enregistree. Votre code est ${code}.` : 'Inscription enregistree.',
                'success',
            );
            showToast('Newsletter activee.', 'success');
            form.reset();
            window.localStorage.setItem(storageKey, 'true');
            window.setTimeout(closeModal, 1200);
        } catch (error) {
            setFeedback(
                feedback,
                error.response?.data?.message || "Impossible d'enregistrer votre inscription pour le moment.",
                'error',
            );
        }
    });

    if (window.localStorage.getItem(storageKey) !== 'true') {
        window.setTimeout(openModal, 15000);

        const onScroll = () => {
            const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;

            if (scrollableHeight <= 0) {
                return;
            }

            const percentage = (window.scrollY / scrollableHeight) * 100;

            if (percentage >= 30) {
                openModal();
                window.removeEventListener('scroll', onScroll);
            }
        };

        window.addEventListener('scroll', onScroll, { passive: true });
    }
}

function initFlashNotifications() {
    const buttons = Array.from(document.querySelectorAll('[data-notification-enable]'));

    if (buttons.length === 0) {
        return;
    }

    const subscribeUrl = buttons[0].dataset.notificationSubscribeUrl;
    const latestUrl = buttons[0].dataset.notificationLatestUrl;
    const email = buttons[0].dataset.notificationEmail || '';
    let pollHandle = null;

    const syncPermission = async (permission) => {
        if (!subscribeUrl) {
            return;
        }

        try {
            await window.axios.post(subscribeUrl, {
                permission,
                email,
                source_path: window.location.pathname,
            });
        } catch (error) {
            showError(error.response?.data?.message || "Impossible d'activer les alertes flash.");
        }
    };

    const notifyCampaign = (campaign) => {
        if (!('Notification' in window) || Notification.permission !== 'granted' || !campaign) {
            return;
        }

        const body = campaign.discountCode
            ? `${campaign.message} Code: ${campaign.discountCode}`
            : campaign.message;
        const notification = new Notification(campaign.title, { body });

        notification.onclick = () => {
            if (campaign.ctaUrl) {
                window.open(campaign.ctaUrl, '_blank');
            }
        };
    };

    const pollCampaign = async () => {
        if (!latestUrl) {
            return;
        }

        try {
            const response = await window.axios.get(latestUrl);
            const campaign = response.data?.campaign;

            if (campaign && response.data?.shouldNotify) {
                notifyCampaign(campaign);
                showToast(campaign.discountCode ? `Alerte flash: ${campaign.discountCode}` : campaign.title, 'success');
            }
        } catch {
            // Silent polling failure: keep the storefront usable.
        }
    };

    const startPolling = () => {
        if (pollHandle !== null) {
            return;
        }

        pollCampaign();
        pollHandle = window.setInterval(pollCampaign, 90000);
    };

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            if (!('Notification' in window)) {
                showError('Les notifications du navigateur ne sont pas disponibles ici.');
                return;
            }

            const permission = await Notification.requestPermission();
            await syncPermission(permission);

            if (permission === 'granted') {
                showToast('Alertes flash activees.', 'success');
                startPolling();
                return;
            }

            showError('Les alertes flash ont ete refusees.');
        });
    });

    if ('Notification' in window && Notification.permission !== 'default') {
        syncPermission(Notification.permission);
    }

    if ('Notification' in window && Notification.permission === 'granted') {
        startPolling();
    }
}

function initUgcForms() {
    document.querySelectorAll('[data-ugc-form]').forEach((form) => {
        const feedback = form.querySelector('[data-ugc-feedback]');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            try {
                const payload = new FormData(form);
                const response = await window.axios.post(form.action, payload, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                setFeedback(feedback, response.data?.message || 'Photo envoyee pour moderation.', 'success');
                showToast('Photo client envoyee.', 'success');
                form.reset();
            } catch (error) {
                setFeedback(
                    feedback,
                    error.response?.data?.message || "Impossible d'envoyer cette photo pour le moment.",
                    'error',
                );
            }
        });
    });
}

function appendSupportMessage(container, role, body) {
    if (!container) {
        return;
    }

    const message = document.createElement('article');
    const baseClasses = 'max-w-[85%] rounded-[1.4rem] px-4 py-3 text-sm leading-7 shadow-[0_12px_26px_rgba(2,25,31,0.08)]';

    if (role === 'user') {
        message.className = `${baseClasses} ml-auto rounded-tr-md bg-[var(--brand-deep)] text-white`;
    } else {
        message.className = `${baseClasses} rounded-tl-md bg-white text-[var(--brand-copy)]`;
    }

    message.innerHTML = escapeHtml(body);
    container.appendChild(message);
    container.scrollTop = container.scrollHeight;
}

function renderSupportQuickReplies(container, replies) {
    if (!container) {
        return;
    }

    container.innerHTML = '';

    replies.forEach((reply) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.supportQuick = reply;
        button.className = 'rounded-full border border-[rgba(8,62,73,0.14)] bg-[rgba(255,247,239,0.72)] px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-[var(--brand-ink)] transition hover:border-[var(--brand-deep)] hover:bg-[rgba(255,241,233,0.96)]';
        button.textContent = reply;
        container.appendChild(button);
    });
}

function initSupportWidget() {
    const widget = document.querySelector('[data-support-widget]');

    if (!widget) {
        return;
    }

    const panel = widget.querySelector('[data-support-panel]');
    const toggle = widget.querySelector('[data-support-toggle]');
    const close = widget.querySelector('[data-support-close]');
    const form = widget.querySelector('[data-support-form]');
    const input = widget.querySelector('[data-support-input]');
    const messages = widget.querySelector('[data-support-messages]');
    const feedback = widget.querySelector('[data-support-feedback]');
    const quickActions = widget.querySelector('[data-support-quick-actions]');
    const chatUrl = widget.dataset.chatUrl;
    const sourcePath = widget.dataset.sourcePath || window.location.pathname;
    const sourceProductSlug = widget.dataset.sourceProductSlug || '';

    const openPanel = () => {
        if (!panel) {
            return;
        }

        panel.classList.remove('hidden');

        window.requestAnimationFrame(() => {
            panel.classList.add('is-open');
        });

        window.setTimeout(() => {
            if (messages) {
                messages.scrollTop = messages.scrollHeight;
            }

            input?.focus();
        }, 120);
    };

    const closePanel = () => {
        if (!panel) {
            return;
        }

        panel.classList.remove('is-open');

        window.setTimeout(() => {
            if (!panel.classList.contains('is-open')) {
                panel.classList.add('hidden');
            }
        }, 240);
    };

    const sendMessage = async (rawMessage) => {
        const message = rawMessage.trim();

        if (!message || !chatUrl) {
            return;
        }

        appendSupportMessage(messages, 'user', message);
        input.value = '';
        setFeedback(feedback, '', 'success');
        feedback?.classList.add('hidden');

        try {
            const response = await window.axios.post(chatUrl, {
                message,
                source_path: sourcePath,
                source_product_slug: sourceProductSlug,
            });

            appendSupportMessage(messages, 'assistant', response.data?.reply?.body || 'Je reviens vers vous dans un instant.');
            renderSupportQuickReplies(quickActions, response.data?.reply?.quickReplies || []);
        } catch (error) {
            appendSupportMessage(messages, 'assistant', "Je n'arrive pas a repondre pour le moment. Reessayez dans un instant.");
            setFeedback(
                feedback,
                error.response?.data?.message || "Le bot n'est pas joignable pour l'instant.",
                'error',
            );
        }
    };

    toggle?.addEventListener('click', () => {
        if (panel?.classList.contains('is-open')) {
            closePanel();
            return;
        }

        openPanel();
    });
    close?.addEventListener('click', closePanel);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && panel?.classList.contains('is-open')) {
            closePanel();
        }
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        sendMessage(input?.value || '');
    });

    input?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage(input.value || '');
        }
    });

    quickActions?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-support-quick]');

        if (!button) {
            return;
        }

        sendMessage(button.dataset.supportQuick || '');
    });
}

function shouldReduceMotion() {
    return window.matchMedia?.('(prefers-reduced-motion: reduce)').matches === true;
}

function initProductViewers() {
    document.querySelectorAll('[data-product-viewer]').forEach((viewer) => {
        if (viewer.dataset.productViewerReady === 'true') {
            return;
        }

        viewer.dataset.productViewerReady = 'true';

        const slides = Array.from(viewer.querySelectorAll('[data-product-view-index]'));
        const dots = Array.from(viewer.querySelectorAll('[data-product-view-dot]'));

        if (slides.length <= 1) {
            return;
        }

        let activeIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
        let rotationTimer = null;
        let resetTimer = null;
        let isPointerOver = false;
        let isVisible = false;

        const setActiveView = (nextIndex, direction = 1) => {
            if (nextIndex === activeIndex || !slides[nextIndex]) {
                return;
            }

            window.clearTimeout(resetTimer);
            viewer.style.setProperty('--view-direction', direction >= 0 ? '1' : '-1');

            const previousSlide = slides[activeIndex];

            previousSlide?.classList.remove('is-active');
            previousSlide?.classList.add('is-leaving');
            previousSlide?.setAttribute('aria-hidden', 'true');

            window.setTimeout(() => {
                previousSlide?.classList.remove('is-leaving');
            }, 760);

            slides[nextIndex].classList.add('is-active');
            slides[nextIndex].setAttribute('aria-hidden', 'false');
            dots.forEach((dot, index) => dot.classList.toggle('is-active', index === nextIndex));
            activeIndex = nextIndex;
        };

        const advance = () => {
            setActiveView((activeIndex + 1) % slides.length, 1);
        };

        const startRotation = () => {
            if (rotationTimer !== null) {
                return;
            }

            viewer.classList.add('is-rotating');
            advance();
            rotationTimer = window.setInterval(advance, shouldReduceMotion() ? 2400 : 1500);
        };

        const stopRotation = (reset = false) => {
            if (rotationTimer !== null) {
                window.clearInterval(rotationTimer);
                rotationTimer = null;
            }

            viewer.classList.remove('is-rotating');

            if (reset && activeIndex !== 0) {
                resetTimer = window.setTimeout(() => setActiveView(0, -1), 180);
            }
        };

        const syncRotation = () => {
            if (isPointerOver || isVisible) {
                startRotation();
                return;
            }

            stopRotation(true);
        };

        viewer.addEventListener('mouseenter', () => {
            isPointerOver = true;
            syncRotation();
        });
        viewer.addEventListener('focusin', () => {
            isPointerOver = true;
            syncRotation();
        });

        const syncPointerTilt = (event) => {
            if (shouldReduceMotion()) {
                return;
            }

            const rect = viewer.getBoundingClientRect();
            const pointerX = ((event.clientX - rect.left) / rect.width - 0.5) * 2;
            const pointerY = ((event.clientY - rect.top) / rect.height - 0.5) * 2;

            viewer.style.setProperty('--pointer-x', pointerX.toFixed(3));
            viewer.style.setProperty('--pointer-y', pointerY.toFixed(3));
        };

        viewer.addEventListener('pointerenter', () => {
            isPointerOver = true;
            syncRotation();
        });
        viewer.addEventListener('pointermove', syncPointerTilt);
        viewer.addEventListener('mousemove', syncPointerTilt);

        viewer.addEventListener('pointerleave', () => {
            isPointerOver = false;
            viewer.style.setProperty('--pointer-x', '0');
            viewer.style.setProperty('--pointer-y', '0');
            syncRotation();
        });

        viewer.addEventListener('mouseleave', () => {
            isPointerOver = false;
            viewer.style.setProperty('--pointer-x', '0');
            viewer.style.setProperty('--pointer-y', '0');
            syncRotation();
        });

        viewer.addEventListener('focusout', (event) => {
            if (!viewer.contains(event.relatedTarget)) {
                isPointerOver = false;
                syncRotation();
            }
        });

        if (typeof IntersectionObserver === 'undefined') {
            isVisible = true;
            syncRotation();
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                isVisible = entry.isIntersecting && entry.intersectionRatio >= 0.12;
                syncRotation();
            });
        }, {
            threshold: [0, 0.12, 0.44],
        });

        observer.observe(viewer);

        window.setTimeout(() => {
            if (rotationTimer === null) {
                isVisible = true;
                syncRotation();
            }
        }, 360);
    });
}

export function initSharedStorefront() {
    if (sharedStorefrontInitialized) {
        return;
    }

    sharedStorefrontInitialized = true;
    initNewsletterModal();
    initFlashNotifications();
    initUgcForms();
    initSupportWidget();
    initProductViewers();
}

export function initCatalog() {
    initSharedStorefront();
    initProductViewers();

    document.querySelectorAll('button[data-track-prev], button[data-track-next]').forEach((button) => {
        button.addEventListener('click', () => {
            const trackId = button.dataset.trackPrev || button.dataset.trackNext;
            const track = document.getElementById(trackId);

            if (!track) {
                return;
            }

            const direction = button.dataset.trackPrev ? -1 : 1;
            const distance = Math.max(track.clientWidth * 0.82, 320) * direction;

            track.scrollBy({
                left: distance,
                behavior: 'smooth',
            });
        });
    });
}

export function initProductDetail(config) {
    initSharedStorefront();

    const state = {
        product: config.product,
        images: Array.isArray(config.images) && config.images.length > 0
            ? config.images
            : [config.product.imageUrl],
        activeImageIndex: 0,
        selectedRating: 0,
    };

    const elements = {
        carousel: document.querySelector('[data-detail-carousel]'),
        detailImage: document.getElementById('detailImage'),
        carouselStatus: document.getElementById('detailCarouselStatus'),
        previousImageButton: document.querySelector('[data-detail-prev]'),
        nextImageButton: document.querySelector('[data-detail-next]'),
        averageRating: document.getElementById('averageRating'),
        ratingCount: document.getElementById('ratingCount'),
        detailVote: document.getElementById('detailVote'),
        voteButton: document.getElementById('voteProductButton'),
        commentForm: document.getElementById('commentForm'),
        commentName: document.getElementById('commentName'),
        commentText: document.getElementById('commentText'),
        commentSuccess: document.getElementById('commentSuccess'),
        commentList: document.getElementById('commentList'),
        starButtons: Array.from(document.querySelectorAll('button[data-star]')),
        thumbButtons: Array.from(document.querySelectorAll('button[data-detail-image]')),
    };

    let carouselTimer = null;
    let carouselPaused = false;

    function updateMetrics() {
        if (elements.averageRating) {
            elements.averageRating.textContent = Number(state.product.averageRating || 0).toFixed(1);
        }

        if (elements.ratingCount) {
            elements.ratingCount.textContent = state.product.reviewCount;
        }

        if (elements.detailVote) {
            elements.detailVote.textContent = state.product.voteCount;
        }

        syncActionButton(elements.voteButton, {
            isDone: state.product.hasVoted,
            idleLabel: 'Voter pour le produit du mois',
            doneLabel: 'Vote enregistre',
        });
    }

    function highlightStars(value) {
        elements.starButtons.forEach((button) => {
            const star = Number(button.dataset.star);
            const isActive = star <= value;

            button.classList.toggle('is-active', isActive);
            button.style.color = isActive ? 'var(--brand-sand-dark)' : '#c9c2b4';
            button.style.borderColor = isActive ? 'var(--brand-sand-dark)' : 'rgba(8, 62, 73, 0.16)';
        });
    }

    function showSuccessMessage() {
        if (!elements.commentSuccess) {
            return;
        }

        elements.commentSuccess.classList.remove('hidden');
        window.setTimeout(() => {
            elements.commentSuccess.classList.add('hidden');
        }, 3000);
    }

    function prependReview(review) {
        if (!elements.commentList) {
            return;
        }

        const emptyState = elements.commentList.querySelector('[data-empty-reviews]');

        if (emptyState) {
            emptyState.remove();
        }

        const reviewCard = document.createElement('article');
        reviewCard.className = 'brand-card p-6 shadow-[0_16px_40px_rgba(2,25,31,0.08)]';
        reviewCard.innerHTML = `
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-[var(--brand-ink)]">${escapeHtml(review.author_name)}</h3>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">A l'instant</p>
                </div>
                <div class="text-sm text-[var(--brand-sand-dark)]">${renderStars(Number(review.rating))}</div>
            </div>
            <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">${escapeHtml(review.body)}</p>
        `;

        elements.commentList.prepend(reviewCard);
    }

    function normalizeImageIndex(index) {
        const total = state.images.length;

        if (total === 0) {
            return 0;
        }

        return ((index % total) + total) % total;
    }

    function syncCarouselControls(index) {
        elements.thumbButtons.forEach((button) => {
            const buttonIndex = Number(button.dataset.detailImage);
            const isActive = buttonIndex === index;

            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            button.style.borderColor = isActive
                ? 'var(--brand-sand)'
                : 'rgba(211, 176, 130, 0.16)';
        });

        if (elements.carouselStatus) {
            elements.carouselStatus.textContent = `Photo ${index + 1} sur ${state.images.length}`;
        }
    }

    function setDetailImage(index) {
        if (!elements.detailImage) {
            return;
        }

        const nextIndex = normalizeImageIndex(index);
        const image = state.images[nextIndex] || state.images[0];

        state.activeImageIndex = nextIndex;
        elements.detailImage.classList.add('is-switching');
        elements.detailImage.src = image;
        elements.detailImage.alt = `${state.product.name} - photo ${nextIndex + 1}`;

        window.setTimeout(() => {
            elements.detailImage?.classList.remove('is-switching');
        }, 260);

        syncCarouselControls(nextIndex);
    }

    function advanceDetailImage(direction = 1) {
        setDetailImage(state.activeImageIndex + direction);
    }

    function stopDetailCarousel() {
        if (carouselTimer === null) {
            return;
        }

        window.clearInterval(carouselTimer);
        carouselTimer = null;
    }

    function shouldRunDetailCarousel() {
        return state.images.length > 1
            && !carouselPaused
            && !document.hidden;
    }

    function syncDetailCarousel() {
        if (!shouldRunDetailCarousel()) {
            stopDetailCarousel();
            return;
        }

        if (carouselTimer !== null) {
            return;
        }

        carouselTimer = window.setInterval(() => {
            advanceDetailImage(1);
        }, shouldReduceMotion() ? 5200 : 3200);
    }

    function restartDetailCarousel() {
        stopDetailCarousel();
        syncDetailCarousel();
    }

    function setCarouselPaused(paused) {
        carouselPaused = paused;
        syncDetailCarousel();
    }

    async function submitVote() {
        if (state.product.hasVoted) {
            return;
        }

        try {
            const response = await window.axios.post(state.product.voteUrl);

            state.product = {
                ...state.product,
                ...response.data.product,
            };

            updateMetrics();
            showToast('Merci pour votre vote.', 'success');
        } catch (error) {
            const payload = error.response?.data;

            if (payload?.product) {
                state.product = {
                    ...state.product,
                    ...payload.product,
                };
                updateMetrics();
            }

            showError(payload?.message);
        }
    }

    async function submitReview(event) {
        event.preventDefault();

        const authorName = elements.commentName?.value.trim();
        const body = elements.commentText?.value.trim();
        const rating = state.selectedRating || 5;

        if (!authorName || !body) {
            showError('Veuillez renseigner votre nom et votre commentaire.');
            return;
        }

        try {
            const response = await window.axios.post(state.product.reviewUrl, {
                author_name: authorName,
                body,
                rating,
            });

            state.product = {
                ...state.product,
                ...response.data.metrics,
            };

            prependReview(response.data.review);
            updateMetrics();
            elements.commentForm?.reset();
            state.selectedRating = 0;
            highlightStars(0);
            showSuccessMessage();
        } catch (error) {
            showError(error.response?.data?.message || "Impossible d'enregistrer ce commentaire pour le moment.");
        }
    }

    elements.starButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.selectedRating = Number(button.dataset.star);
            highlightStars(state.selectedRating);
        });
    });

    elements.thumbButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setDetailImage(Number(button.dataset.detailImage));
            restartDetailCarousel();
        });
    });

    elements.previousImageButton?.addEventListener('click', () => {
        advanceDetailImage(-1);
        restartDetailCarousel();
    });

    elements.nextImageButton?.addEventListener('click', () => {
        advanceDetailImage(1);
        restartDetailCarousel();
    });

    elements.carousel?.addEventListener('mouseenter', () => setCarouselPaused(true));
    elements.carousel?.addEventListener('mouseleave', () => setCarouselPaused(false));
    elements.carousel?.addEventListener('focusin', () => setCarouselPaused(true));
    elements.carousel?.addEventListener('focusout', (event) => {
        if (!elements.carousel?.contains(event.relatedTarget)) {
            setCarouselPaused(false);
        }
    });

    document.addEventListener('visibilitychange', syncDetailCarousel);

    elements.voteButton?.addEventListener('click', submitVote);
    elements.commentForm?.addEventListener('submit', submitReview);

    updateMetrics();
    setDetailImage(0);
    syncDetailCarousel();
}
