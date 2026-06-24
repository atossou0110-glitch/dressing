import './bootstrap';

import Alpine from 'alpinejs';
import { initCatalog, initProductDetail, initSharedStorefront } from './storefront';

window.Alpine = Alpine;
window.Dressingue = {
    initCatalog,
    initProductDetail,
    initSharedStorefront,
};

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function syncMotionMode() {
    document.documentElement.classList.toggle('motion-soft', prefersReducedMotion.matches);
}

function initHeroMarquee() {
    const rails = Array.from(document.querySelectorAll('[data-hero-marquee]'));

    if (rails.length === 0) {
        return;
    }

    let frameId = null;
    let lastTime = null;
    let measurements = [];

    const rebuild = () => {
        measurements = rails.map((rail) => {
            const loopWidth = rail.scrollWidth / 2;

            if (!loopWidth) {
                return null;
            }

            const isReverse = rail.dataset.heroMarquee === 'reverse';
            const speed = Number.parseFloat(rail.dataset.heroSpeed || '') || (isReverse ? 18 : 22);
            const offset = isReverse ? -loopWidth : 0;

            rail.classList.add('hero-background-track-js');
            rail.style.transform = `translate3d(${offset}px, 0, 0)`;

            return {
                rail,
                loopWidth,
                speed,
                isReverse,
                offset,
            };
        }).filter(Boolean);
    };

    const stop = () => {
        if (frameId !== null) {
            window.cancelAnimationFrame(frameId);
            frameId = null;
        }
    };

    const step = (time) => {
        if (lastTime === null) {
            lastTime = time;
        }

        const delta = Math.min(64, time - lastTime);
        lastTime = time;

        measurements.forEach((item) => {
            const direction = item.isReverse ? 1 : -1;
            item.offset += direction * item.speed * (delta / 1000);

            if (!item.isReverse && item.offset <= -item.loopWidth) {
                item.offset += item.loopWidth;
            }

            if (item.isReverse && item.offset >= 0) {
                item.offset -= item.loopWidth;
            }

            item.rail.style.transform = `translate3d(${item.offset}px, 0, 0)`;
        });

        frameId = window.requestAnimationFrame(step);
    };

    const start = () => {
        stop();
        lastTime = null;

        if (measurements.length === 0) {
            rebuild();
        }

        if (measurements.length > 0) {
            frameId = window.requestAnimationFrame(step);
        }
    };

    rebuild();
    start();

    window.addEventListener('resize', () => {
        rebuild();
        start();
    }, { passive: true });

    window.addEventListener('load', () => {
        rebuild();
        start();
    }, { once: true });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stop();
            return;
        }

        start();
    });
}

function initSmartVideos() {
    const videos = Array.from(document.querySelectorAll('[data-smart-video]'));

    if (videos.length === 0) {
        return;
    }

    const playVideo = (video) => {
        const playback = video.play();

        if (playback && typeof playback.catch === 'function') {
            playback.catch(() => {});
        }
    };

    const pauseVideo = (video) => {
        if (!video.paused) {
            video.pause();
        }
    };

    videos.forEach((video) => {
        video.muted = true;
        video.defaultMuted = true;
        video.playsInline = true;
        video.autoplay = true;
        video.loop = true;
        video.addEventListener('loadeddata', () => {
            if (!document.hidden) {
                playVideo(video);
            }
        });
        video.addEventListener('canplay', () => {
            if (!document.hidden) {
                playVideo(video);
            }
        });
    });

    if (typeof IntersectionObserver === 'undefined') {
        videos.forEach((video) => playVideo(video));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            const video = entry.target;
            const shouldPlay = entry.isIntersecting && entry.intersectionRatio >= 0.05 && !document.hidden;

            video.dataset.videoActive = shouldPlay ? 'true' : 'false';

            if (shouldPlay) {
                playVideo(video);
                return;
            }

            pauseVideo(video);
        });
    }, {
        threshold: [0, 0.05, 0.2, 0.45],
        rootMargin: '0px 0px -10% 0px',
    });

    videos.forEach((video) => {
        observer.observe(video);
        if (!document.hidden) {
            playVideo(video);
        }
    });

    document.addEventListener('visibilitychange', () => {
        videos.forEach((video) => {
            if (document.hidden || video.dataset.videoActive !== 'true') {
                pauseVideo(video);
                return;
            }

            playVideo(video);
        });
    });

    const resumeOnInteraction = () => {
        videos.forEach((video) => {
            if (!document.hidden) {
                playVideo(video);
            }
        });
    };

    document.addEventListener('pointerdown', resumeOnInteraction, { passive: true });
    document.addEventListener('keydown', resumeOnInteraction, { passive: true });
}

function initScrollAnimations() {
    const revealElements = Array.from(document.querySelectorAll('[data-reveal]'));

    if (revealElements.length === 0) {
        return;
    }

    const revealGroups = new Map();

    revealElements.forEach((element) => {
        const groupKey = element.parentElement || document.body;

        if (!revealGroups.has(groupKey)) {
            revealGroups.set(groupKey, []);
        }

        revealGroups.get(groupKey).push(element);
    });

    revealGroups.forEach((group) => {
        group.forEach((element, index) => {
            const explicitDelay = element.dataset.revealDelay;
            const delay = explicitDelay ?? `${Math.min(index, 5) * 70}ms`;

            element.style.setProperty('--reveal-delay', delay);
        });
    });

    if (typeof IntersectionObserver === 'undefined') {
        revealElements.forEach((element) => element.classList.add('in-view'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -72px 0px',
    });

    revealElements.forEach((element) => {
        observer.observe(element);
    });
}

function initActiveSectionNav() {
    const links = Array.from(document.querySelectorAll('.brand-nav-link[href^="#"]'));

    if (links.length === 0 || typeof IntersectionObserver === 'undefined') {
        return;
    }

    const targets = links.map((link) => {
        const href = link.getAttribute('href');
        const section = href ? document.querySelector(href) : null;

        return section ? { link, section, href } : null;
    }).filter(Boolean);

    if (targets.length === 0) {
        return;
    }

    const setActive = (href) => {
        targets.forEach(({ link }) => {
            link.classList.toggle('is-active', link.getAttribute('href') === href);
        });
    };

    if (window.location.hash) {
        setActive(window.location.hash);
    }

    const observer = new IntersectionObserver((entries) => {
        const visibleEntries = entries
            .filter((entry) => entry.isIntersecting)
            .sort((first, second) => second.intersectionRatio - first.intersectionRatio);

        if (visibleEntries.length === 0) {
            return;
        }

        setActive(`#${visibleEntries[0].target.id}`);
    }, {
        threshold: [0.24, 0.4, 0.62],
        rootMargin: '-18% 0px -52% 0px',
    });

    targets.forEach(({ section }) => observer.observe(section));

    links.forEach((link) => {
        link.addEventListener('click', () => {
            const href = link.getAttribute('href');

            if (href) {
                setActive(href);
            }
        });
    });
}

function initButtonEffects() {
    const buttons = document.querySelectorAll('.brand-button-primary, .brand-button-secondary, .brand-header-button, .brand-track-button');

    buttons.forEach((button) => {
        button.addEventListener('mouseenter', function () {
            this.classList.add('animate-button-pulse');
        });

        button.addEventListener('mouseleave', function () {
            this.classList.remove('animate-button-pulse');
        });

        button.addEventListener('click', function (e) {
            if (prefersReducedMotion.matches || !this.classList.contains('brand-button-primary')) {
                return;
            }

            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            window.setTimeout(() => ripple.remove(), 600);
        });
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href === '#') {
                return;
            }

            e.preventDefault();

            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
                    block: 'start',
                });
            }
        });
    });
}

function initFormAnimations() {
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], textarea, select');

    inputs.forEach((input) => {
        input.addEventListener('focus', function () {
            this.classList.add('smooth-transition');
            this.style.boxShadow = '0 0 0 3px rgba(211, 176, 130, 0.18)';
        });

        input.addEventListener('blur', function () {
            this.style.boxShadow = 'none';
        });
    });
}

function initDecisionTools() {
    const root = document.querySelector('[data-decision-tools]');
    const triggers = Array.from(document.querySelectorAll('[data-decision-tool-open]'));
    const closers = Array.from(document.querySelectorAll('[data-decision-tool-close]'));
    const modals = Array.from(document.querySelectorAll('.brand-modal-shell[id]'));
    const productsData = root?.querySelector('[data-decision-products]');
    let products = [];

    if (triggers.length === 0 || modals.length === 0) {
        return;
    }

    if (productsData?.textContent) {
        try {
            products = JSON.parse(productsData.textContent);
        } catch {
            products = [];
        }
    }

    const productBySlug = new Map(products.map((product) => [product.slug, product]));
    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    const formatDimensionValue = (value) => Number.isInteger(value) ? String(value) : String(value).replace(/\.0$/, '');
    const formatDimensions = (product) => product?.dimensions?.label || product?.dimensionsLabel || 'Dimensions non communiquees';
    const formatPrice = (product) => {
        if (product?.homePrice) {
            return product.homePrice;
        }

        if (Number.isFinite(product?.priceValue)) {
            return `${new Intl.NumberFormat('fr-FR').format(product.priceValue)} FCFA`;
        }

        return 'Prix sur demande';
    };

    // Move decision-tool modals to <body> so fixed positioning is not trapped
    // by animated page containers.
    modals.forEach((modal) => {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    const syncBodyLock = () => {
        const hasOpenModal = modals.some((modal) => modal.classList.contains('is-open'));
        document.body.style.overflow = hasOpenModal ? 'hidden' : '';
    };

    const openModal = (id) => {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modals.forEach((candidate) => {
            if (candidate !== modal && candidate.classList.contains('is-open')) {
                candidate.classList.remove('is-open');
                candidate.classList.add('hidden');
                candidate.classList.remove('flex');
                candidate.setAttribute('aria-hidden', 'true');
            }
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');

        window.requestAnimationFrame(() => {
            modal.classList.add('is-open');
            syncBodyLock();

            const firstFocusable = modal.querySelector('input, select, textarea, button, a[href]');
            firstFocusable?.focus();
        });
    };

    const closeModal = (id) => {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        window.setTimeout(() => {
            if (!modal.classList.contains('is-open')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            syncBodyLock();
        }, 260);
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            openModal(trigger.dataset.decisionToolOpen);
        });
    });

    closers.forEach((closer) => {
        closer.addEventListener('click', () => {
            closeModal(closer.dataset.decisionToolClose);
        });
    });

    modals.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        modals.forEach((modal) => {
            if (modal.classList.contains('is-open')) {
                closeModal(modal.id);
            }
        });
    });

    const calculateButton = document.querySelector('[data-dimension-calculate]');
    const widthField = document.getElementById('space-width');
    const heightField = document.getElementById('space-height');
    const collectionField = document.getElementById('space-collection');
    const result = document.getElementById('compatibility-result');
    const resultSummary = result?.querySelector('[data-dimension-summary]');
    const resultMatches = result?.querySelector('[data-dimension-matches]');
    const compareSelects = Array.from(document.querySelectorAll('[data-compare-select]'));
    const comparisonTable = document.getElementById('comparison-table');

    const renderProductCard = (product, meta, tone = 'success') => {
        const toneClasses = tone === 'warning'
            ? 'border-[rgba(184,126,60,0.24)] bg-[rgba(255,247,236,0.92)]'
            : 'border-[rgba(8,62,73,0.12)] bg-white/90';
        const featureList = Array.isArray(product.features) && product.features.length > 0
            ? `<ul class="mt-3 space-y-2 text-sm leading-6 text-[var(--brand-copy)]">${product.features.map((feature) => `<li>${escapeHtml(feature)}</li>`).join('')}</ul>`
            : `<p class="mt-3 text-sm leading-6 text-[var(--brand-copy)]">Points forts a decouvrir sur la fiche produit.</p>`;

        return `
            <article class="rounded-[1.5rem] border ${toneClasses} p-4 shadow-[0_12px_30px_rgba(2,25,31,0.06)]">
                <div class="flex gap-4">
                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-[1.1rem] bg-[var(--brand-ivory-2)]">
                        <img src="${escapeHtml(product.imageUrl || '')}" alt="${escapeHtml(product.name)}" class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-[var(--brand-teal-soft)]">${escapeHtml(product.collectionLabel)} . ${escapeHtml(product.categoryLabel)}</p>
                        <h3 class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">${escapeHtml(product.name)}</h3>
                        <p class="mt-2 text-sm font-medium text-[var(--brand-copy)]">${escapeHtml(meta)}</p>
                        <p class="mt-2 text-sm font-semibold text-[var(--brand-ink)]">${escapeHtml(formatDimensions(product))}</p>
                        <p class="mt-1 text-sm text-[var(--brand-copy)]">${escapeHtml(formatPrice(product))}</p>
                        ${featureList}
                        <a href="${escapeHtml(product.detailsUrl || '#')}" class="brand-guide-link mt-4 inline-flex text-xs font-semibold uppercase tracking-[0.14em]">Voir la fiche</a>
                    </div>
                </div>
            </article>
        `;
    };

    const renderDimensionResults = () => {
        const width = Number.parseFloat(widthField?.value || '');
        const height = Number.parseFloat(heightField?.value || '');
        const collection = collectionField?.value || '';

        if (!Number.isFinite(width) || width <= 0 || !Number.isFinite(height) || height <= 0) {
            window.alert('Veuillez saisir une largeur et une hauteur valides.');
            return;
        }

        const eligibleProducts = products.filter((product) => {
            if (!product?.dimensions) {
                return false;
            }

            if (collection && product.collection !== collection) {
                return false;
            }

            return true;
        });

        if (eligibleProducts.length === 0) {
            if (resultSummary) {
                resultSummary.innerHTML = '<p>Aucune donnee dimensionnelle n est disponible pour cette selection.</p>';
            }

            if (resultMatches) {
                resultMatches.innerHTML = '';
            }

            result?.classList.remove('hidden');
            return;
        }

        const matchingProducts = eligibleProducts
            .filter((product) => width >= product.dimensions.width && height >= product.dimensions.height)
            .sort((first, second) => (second.dimensions.width + second.dimensions.height) - (first.dimensions.width + first.dimensions.height));

        const closestProducts = eligibleProducts
            .filter((product) => !matchingProducts.includes(product))
            .sort((first, second) => {
                const firstGap = Math.max(0, first.dimensions.width - width) + Math.max(0, first.dimensions.height - height);
                const secondGap = Math.max(0, second.dimensions.width - width) + Math.max(0, second.dimensions.height - height);

                return firstGap - secondGap;
            })
            .slice(0, 2);

        const formattedWidth = formatDimensionValue(width);
        const formattedHeight = formatDimensionValue(height);

        if (matchingProducts.length > 0) {
            if (resultSummary) {
                resultSummary.innerHTML = `<p><strong>${matchingProducts.length}</strong> meuble(s) peuvent entrer dans un espace de ${escapeHtml(formattedWidth)} cm x ${escapeHtml(formattedHeight)} cm.</p>`;
            }

            if (resultMatches) {
                resultMatches.innerHTML = matchingProducts.map((product) => {
                    const remainingWidth = Math.max(0, width - product.dimensions.width);
                    const remainingHeight = Math.max(0, height - product.dimensions.height);
                    const meta = `Marge restante: ${formatDimensionValue(remainingWidth)} cm en largeur et ${formatDimensionValue(remainingHeight)} cm en hauteur.`;

                    return renderProductCard(product, meta);
                }).join('');
            }
        } else {
            if (resultSummary) {
                resultSummary.innerHTML = `<p>Aucun meuble ne passe dans ${escapeHtml(formattedWidth)} cm x ${escapeHtml(formattedHeight)} cm. Voici les options les plus proches a envisager.</p>`;
            }

            if (resultMatches) {
                resultMatches.innerHTML = closestProducts.map((product) => {
                    const missingWidth = Math.max(0, product.dimensions.width - width);
                    const missingHeight = Math.max(0, product.dimensions.height - height);
                    const meta = `Il manque environ ${formatDimensionValue(missingWidth)} cm en largeur et ${formatDimensionValue(missingHeight)} cm en hauteur.`;

                    return renderProductCard(product, meta, 'warning');
                }).join('');
            }
        }

        result?.classList.remove('hidden');
        result?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const renderComparison = () => {
        if (!comparisonTable) {
            return;
        }

        const selectedProducts = Array.from(new Set(compareSelects
            .map((select) => select.value)
            .filter(Boolean)))
            .map((slug) => productBySlug.get(slug))
            .filter(Boolean);

        if (selectedProducts.length === 0) {
            comparisonTable.innerHTML = '<p class="text-sm text-[var(--brand-copy)]">Aucun produit selectionne pour la comparaison.</p>';
            return;
        }

        const rows = [
            {
                label: 'Collection',
                render: (product) => `${escapeHtml(product.collectionLabel)} / ${escapeHtml(product.categoryLabel)}`,
            },
            {
                label: 'Dimensions',
                render: (product) => escapeHtml(formatDimensions(product)),
            },
            {
                label: 'Prix',
                render: (product) => escapeHtml(formatPrice(product)),
            },
            {
                label: 'Usage',
                render: (product) => escapeHtml(product.homeHighlight || 'A decouvrir sur la fiche produit'),
            },
            {
                label: 'Atouts',
                render: (product) => {
                    if (!Array.isArray(product.features) || product.features.length === 0) {
                        return '<span class="text-sm text-[var(--brand-copy)]">Non renseigne</span>';
                    }

                    return `<ul class="space-y-2 text-sm leading-6 text-[var(--brand-copy)]">${product.features.map((feature) => `<li>${escapeHtml(feature)}</li>`).join('')}</ul>`;
                },
            },
        ];

        comparisonTable.innerHTML = `
            <table class="min-w-full border-separate border-spacing-0 overflow-hidden rounded-[1.75rem]">
                <thead>
                    <tr>
                        <th class="min-w-[11rem] rounded-tl-[1.75rem] border border-[rgba(8,62,73,0.12)] bg-[rgba(248,239,227,0.72)] px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">Critere</th>
                        ${selectedProducts.map((product, index) => `
                            <th class="min-w-[15rem] border border-[rgba(8,62,73,0.12)] ${index === selectedProducts.length - 1 ? 'rounded-tr-[1.75rem]' : ''} bg-white px-4 py-4 text-left align-top">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-[var(--brand-teal-soft)]">${escapeHtml(product.code)}</p>
                                <p class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">${escapeHtml(product.name)}</p>
                                <a href="${escapeHtml(product.detailsUrl || '#')}" class="brand-guide-link mt-3 inline-flex text-xs font-semibold uppercase tracking-[0.14em]">Voir la fiche</a>
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${rows.map((row) => `
                        <tr>
                            <th scope="row" class="border border-t-0 border-[rgba(8,62,73,0.12)] bg-[rgba(248,239,227,0.56)] px-4 py-4 text-left text-sm font-semibold text-[var(--brand-ink)]">${row.label}</th>
                            ${selectedProducts.map((product) => `
                                <td class="border border-t-0 border-[rgba(8,62,73,0.12)] bg-white px-4 py-4 align-top">
                                    ${row.render(product)}
                                </td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    };

    calculateButton?.addEventListener('click', renderDimensionResults);
    compareSelects.forEach((select) => {
        select.addEventListener('change', renderComparison);
    });

    renderComparison();
}

function initCheckoutMethodSelection() {
    const checkoutForms = document.querySelectorAll('[data-checkout-form]');

    checkoutForms.forEach((form) => {
        const options = Array.from(form.querySelectorAll('[data-payment-option]'));

        if (options.length === 0) {
            return;
        }

        const summaryLabel = form.querySelector('[data-payment-method-current]');
        const summaryMode = form.querySelector('[data-payment-method-mode]');
        const submitLabel = form.querySelector('[data-payment-submit-label]');

        const syncSelectedOption = () => {
            const selectedOption = options.find((option) => {
                const input = option.querySelector('.checkout-method-input');
                return input?.checked;
            }) || options[0];

            const selectedInput = selectedOption.querySelector('.checkout-method-input');

            if (selectedInput && !selectedInput.checked) {
                selectedInput.checked = true;
            }

            options.forEach((option) => {
                option.classList.toggle('is-selected', option === selectedOption);
                option.setAttribute('aria-checked', option === selectedOption ? 'true' : 'false');
            });

            const checkoutMode = selectedOption.dataset.checkoutMode || 'direct';
            const paymentLabel = selectedOption.dataset.paymentLabel || 'Paiement';

            if (summaryLabel) {
                summaryLabel.textContent = paymentLabel;
            }

            if (summaryMode) {
                summaryMode.textContent = checkoutMode === 'direct'
                    ? 'Le paiement sera lance sur le numero renseigne.'
                    : 'Vous serez redirige vers une page de paiement securisee.';
            }

            if (submitLabel) {
                submitLabel.textContent = checkoutMode === 'direct'
                    ? `Payer avec ${paymentLabel}`
                    : 'Continuer vers le paiement';
            }
        };

        options.forEach((option) => {
            const input = option.querySelector('.checkout-method-input');

            if (!input) {
                return;
            }

            input.addEventListener('change', syncSelectedOption);

            option.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                event.preventDefault();
                input.checked = true;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        syncSelectedOption();
    });
}

function initNavbarEffect() {
    const navbar = document.querySelector('.brand-header, header, nav');

    if (!navbar) {
        return;
    }

    const syncState = () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        navbar.classList.toggle('is-scrolled', scrollTop > 24);
    };

    syncState();
    window.addEventListener('scroll', syncState, { passive: true });
}

function initStorefrontFixedStage() {
    const frames = Array.from(document.querySelectorAll('[data-storefront-fixed-frame]'))
        .map((frame) => {
            const stage = frame.querySelector('[data-storefront-fixed-stage]');

            if (!stage) {
                return null;
            }

            return {
                frame,
                stage,
                designWidth: Number.parseFloat(stage.dataset.storefrontStageWidth || '') || 1280,
            };
        })
        .filter(Boolean);

    if (frames.length === 0) {
        return;
    }

    const getViewportWidth = () => window.visualViewport?.width || window.innerWidth || document.documentElement.clientWidth;

    const sync = () => {
        const shouldScale = false;

        frames.forEach(({ frame, stage, designWidth }) => {
            frame.classList.toggle('is-scaled', shouldScale);

            if (!shouldScale) {
                frame.style.height = '';
                stage.style.width = '';
                stage.style.transform = '';
                return;
            }

            const frameWidth = Math.max(1, frame.clientWidth || getViewportWidth());
            const scale = Math.min(1, frameWidth / designWidth);

            stage.style.width = `${designWidth}px`;
            stage.style.transform = `scale(${scale})`;
            frame.style.height = `${Math.ceil(stage.scrollHeight * scale)}px`;
        });
    };

    const syncSoon = () => {
        window.requestAnimationFrame(sync);
    };

    if (typeof ResizeObserver !== 'undefined') {
        frames.forEach(({ stage }) => {
            const observer = new ResizeObserver(syncSoon);
            observer.observe(stage);
        });
    }

    sync();

    window.addEventListener('resize', syncSoon, { passive: true });
    window.addEventListener('load', syncSoon, { once: true });

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', syncSoon, { passive: true });
    }
}

function initMobileHeaderMenu() {
    const headers = Array.from(document.querySelectorAll('[data-mobile-header]'));

    if (headers.length === 0) {
        return;
    }

    headers.forEach((header) => {
        const toggle = header.querySelector('[data-mobile-menu-toggle]');
        const menu = header.querySelector('[data-mobile-menu]');

        if (!toggle || !menu) {
            return;
        }

        let open = false;

        const sync = () => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

            if (open) {
                menu.hidden = false;
                menu.classList.add('is-open');
                return;
            }

            menu.classList.remove('is-open');
            window.setTimeout(() => {
                if (!open) {
                    menu.hidden = true;
                }
            }, 220);
        };

        const closeMenu = () => {
            open = false;
            sync();
        };

        toggle.addEventListener('click', () => {
            open = !open;
            sync();
        });

        menu.querySelectorAll('a[href], button[type="submit"]').forEach((item) => {
            item.addEventListener('click', closeMenu);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });

        document.addEventListener('click', (event) => {
            if (!open || header.contains(event.target)) {
                return;
            }

            closeMenu();
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && open) {
                closeMenu();
            }
        }, { passive: true });

        sync();
    });
}

function initAllAnimations() {
    initSharedStorefront();
    syncMotionMode();
    initStorefrontFixedStage();
    initHeroMarquee();
    initSmartVideos();
    initScrollAnimations();
    initActiveSectionNav();
    initButtonEffects();
    initSmoothScroll();
    initFormAnimations();
    initDecisionTools();
    initCheckoutMethodSelection();
    initMobileHeaderMenu();
    initNavbarEffect();
}

if (typeof prefersReducedMotion.addEventListener === 'function') {
    prefersReducedMotion.addEventListener('change', syncMotionMode);
} else if (typeof prefersReducedMotion.addListener === 'function') {
    prefersReducedMotion.addListener(syncMotionMode);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllAnimations);
} else {
    initAllAnimations();
}

Alpine.start();
