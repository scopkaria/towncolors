import './bootstrap';

import Alpine from 'alpinejs';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import createGlobe from 'cobe';

window.Alpine = Alpine;

Alpine.start();

gsap.registerPlugin(ScrollTrigger);

const EASE_SMOOTH = 'power2.out';
const EASE_PREMIUM = 'cubic-bezier(0.34, 1.56, 0.64, 1)';
const ARUSHA = { lat: -3.3869, lng: 36.683 };

function initHomeAnimations() {
	const homeRoot = document.querySelector('[data-towncore-home]');

	if (!homeRoot) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const isDesktop = window.matchMedia('(min-width: 1024px)').matches;

	runPreloaderAndHero(homeRoot, prefersReducedMotion);
	initSectionReveals(homeRoot, prefersReducedMotion);
	initCtaAndFooter(prefersReducedMotion);
	initGlobe(homeRoot, prefersReducedMotion);

	if (isDesktop && !prefersReducedMotion) {
		initPremiumScrollFlow(homeRoot);
	}
}

function runPreloaderAndHero(homeRoot, prefersReducedMotion) {
	const preloader = document.getElementById('tc-preloader');
	const preloaderInner = preloader?.querySelector('.tc-preloader__inner');
	const preloaderDot = preloader?.querySelector('.tc-preloader__label');

	const heroItems = gsap.utils.toArray('[data-home-hero-item]');
	const headerLogo = document.querySelector('[data-home-logo]');
	const headerMenu = document.querySelector('[data-home-menu]');
	const headerActions = document.querySelector('[data-home-actions]');
	const headerTargets = [headerLogo, headerMenu, headerActions].filter(Boolean);

	const intro = gsap.timeline({ defaults: { ease: EASE_SMOOTH } });

	if (preloader) {
		intro.fromTo(
			preloaderInner,
			{ autoAlpha: 0.5, scale: 0.92 },
			{
				autoAlpha: 1,
				scale: 1,
				duration: prefersReducedMotion ? 0.2 : 0.85,
			}
		);

		if (!prefersReducedMotion) {
			intro.to(
				preloaderInner,
				{
					scale: 1.03,
					duration: 0.75,
					yoyo: true,
					repeat: 1,
				},
				0
			);

			intro.fromTo(
				preloaderDot,
				{ opacity: 0.35 },
				{
					opacity: 0.95,
					duration: 0.65,
					repeat: 1,
					yoyo: true,
				},
				0.1
			);
		}
	}

	gsap.set([...headerTargets, ...heroItems], { autoAlpha: 0, y: prefersReducedMotion ? 0 : 34 });

	if (preloader) {
		intro.to(preloader, {
			autoAlpha: 0,
			duration: prefersReducedMotion ? 0.2 : 0.55,
			onComplete: () => {
				preloader.remove();
			},
		});
	}

	intro.to(
		headerTargets,
		{
			autoAlpha: 1,
			y: 0,
			duration: prefersReducedMotion ? 0.15 : 0.85,
			stagger: 0.1,
			ease: EASE_PREMIUM,
		},
		'>-0.1'
	);

	intro.to(
		heroItems,
		{
			autoAlpha: 1,
			y: 0,
			duration: prefersReducedMotion ? 0.15 : 0.9,
			stagger: 0.14,
			ease: EASE_PREMIUM,
		},
		prefersReducedMotion ? '>-0.05' : '>-0.25'
	);
}

function initPremiumScrollFlow(homeRoot) {
	const sections = gsap.utils.toArray('[data-home-section]:not([data-home-cta])');

	if (sections.length < 2) {
		return;
	}

	sections.forEach((section) => {
		gsap.set(section, { willChange: 'transform', force3D: true });
	});

	for (let i = 1; i < sections.length; i += 1) {
		const current = sections[i];

		// Keep upcoming sections near their natural position to avoid first-scroll visual gaps.
		gsap.set(current, { yPercent: 10 });

		ScrollTrigger.create({
			trigger: current,
			start: 'top 94%',
			markers: false,
			onEnter: () => {
				gsap.to(current, {
					yPercent: 0,
					duration: 0.95,
					ease: 'power2.out',
					overwrite: 'auto',
					clearProps: 'willChange',
				});
			},
			onEnterBack: () => {
				gsap.to(current, {
					yPercent: 0,
					duration: 0.7,
					ease: 'power2.out',
					overwrite: 'auto',
				});
			},
			onLeaveBack: () => {
				gsap.set(current, { yPercent: 10 });
			},
		});
	}
}

function initSectionReveals(homeRoot, prefersReducedMotion) {
	const sections = gsap.utils.toArray('[data-home-section]');

	sections.forEach((section) => {
		if (section.hasAttribute('data-home-hero')) {
			return;
		}

		const content = Array.from(section.querySelectorAll('.reveal')).slice(0, 16);

		if (content.length) {
			gsap.set(content, { autoAlpha: 0, y: prefersReducedMotion ? 0 : 28 });

			gsap.to(content, {
				autoAlpha: 1,
				y: 0,
				duration: prefersReducedMotion ? 0.2 : 0.8,
				stagger: 0.12,
				ease: EASE_SMOOTH,
				scrollTrigger: {
					trigger: section,
					start: 'top 74%',
					once: true,
				},
			});
		}

		const sectionImages = section.querySelectorAll('img');
		sectionImages.forEach((image) => {
			gsap.fromTo(
				image,
				{ scale: 1.05 },
				{
					scale: 1,
					ease: 'none',
					scrollTrigger: {
						trigger: image,
						start: 'top 88%',
						end: 'bottom 35%',
						scrub: 0.5,
					},
				}
			);
		});
	});
}

function initGlobe(homeRoot, prefersReducedMotion) {
	const globeSection = homeRoot.querySelector('[data-home-globe]');
	const canvas = document.getElementById('tc-globe-canvas');

	if (!globeSection || !canvas) {
		return;
	}

	const zoomButton = globeSection.querySelector('[data-globe-zoom]');
	const resetButton = globeSection.querySelector('[data-globe-reset]');
	const targetButton = globeSection.querySelector('[data-globe-target]');

	const state = {
		phi: 0,
		theta: 0.28,
		spin: prefersReducedMotion ? 0 : 0.0026,
	};

	const targetPhi = (ARUSHA.lng * Math.PI) / 180;
	const targetTheta = 0.43;

	const globe = createGlobe(canvas, {
		devicePixelRatio: Math.min(window.devicePixelRatio || 1, 2),
		width: canvas.offsetWidth * 2,
		height: canvas.offsetHeight * 2,
		phi: state.phi,
		theta: state.theta,
		dark: 0,
		diffuse: 0.9,
		mapSamples: 10000,
		mapBrightness: 1.15,
		baseColor: [0.94, 0.95, 0.98],
		markerColor: [0.976, 0.451, 0.086],
		glowColor: [1, 1, 1],
		markers: [{ location: [ARUSHA.lat, ARUSHA.lng], size: 0.12 }],
		onRender: (renderState) => {
			state.phi += state.spin;

			renderState.phi = state.phi;
			renderState.theta = state.theta;
			renderState.width = canvas.offsetWidth * 2;
			renderState.height = canvas.offsetHeight * 2;
		},
	});

	const zoomToArusha = () => {
		gsap.to(state, {
			phi: targetPhi,
			theta: targetTheta,
			spin: 0.00035,
			duration: prefersReducedMotion ? 0.15 : 1.1,
			ease: 'power2.inOut',
		});
	};

	const resetView = () => {
		gsap.to(state, {
			phi: 0,
			theta: 0.28,
			spin: prefersReducedMotion ? 0 : 0.0026,
			duration: prefersReducedMotion ? 0.15 : 1,
			ease: 'power2.inOut',
		});
	};

	[zoomButton, targetButton].filter(Boolean).forEach((el) => {
		el.addEventListener('click', zoomToArusha);
	});

	if (resetButton) {
		resetButton.addEventListener('click', resetView);
	}

	if (!prefersReducedMotion) {
		gsap.to('.tc-globe-target__dot', {
			boxShadow: '0 0 0 11px rgba(249, 115, 22, 0)',
			duration: 1.6,
			repeat: -1,
			ease: 'power1.out',
		});
	}

	ScrollTrigger.create({
		trigger: globeSection,
		start: 'top 80%',
		once: true,
		onEnter: () => {
			gsap.fromTo(canvas, { autoAlpha: 0, scale: 0.94 }, { autoAlpha: 1, scale: 1, duration: 1.2, ease: EASE_PREMIUM });
		},
	});

	window.addEventListener('beforeunload', () => {
		globe.destroy();
	});
}

function initCtaAndFooter(prefersReducedMotion) {
	const ctaSection = document.querySelector('[data-home-cta]');
	const ctaCard = ctaSection?.querySelector('.reveal');
	const footer = document.querySelector('[data-home-footer]');

	if (ctaCard) {
		gsap.fromTo(
			ctaCard,
			{ autoAlpha: 0, scale: 0.95, y: 24 },
			{
				autoAlpha: 1,
				scale: 1,
				y: 0,
				duration: prefersReducedMotion ? 0.3 : 0.9,
				ease: EASE_PREMIUM,
				scrollTrigger: {
					trigger: ctaSection,
					start: 'top 68%',
					once: true,
				},
			}
		);
	}

	if (footer) {
		footer.style.position = 'relative';
		footer.style.zIndex = '1';

		gsap.fromTo(
			footer,
			{ yPercent: 6, autoAlpha: 0.4 },
			{
				yPercent: 0,
				autoAlpha: 1,
				duration: prefersReducedMotion ? 0.3 : 1.0,
				ease: EASE_SMOOTH,
				scrollTrigger: {
					trigger: footer,
					start: 'top 80%',
					end: 'top 40%',
					scrub: prefersReducedMotion ? false : 0.5,
				},
			}
		);
	}
}

if (document.readyState === 'complete') {
	initHomeAnimations();
} else {
	window.addEventListener('load', initHomeAnimations, { once: true });
}
