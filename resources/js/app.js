import './bootstrap';

import Alpine from 'alpinejs';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import createGlobe from 'cobe';

document.documentElement.classList.add('js');

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

	const dpr = Math.min(window.devicePixelRatio || 1, 2);
	const size = {
		width: 640,
		height: 640,
	};

	const syncCanvasSize = () => {
		const rect = canvas.getBoundingClientRect();
		const nextWidth = Math.max(320, Math.round(rect.width * dpr));
		const nextHeight = Math.max(320, Math.round(rect.height * dpr));

		size.width = nextWidth;
		size.height = nextHeight;

		if (canvas.width !== nextWidth || canvas.height !== nextHeight) {
			canvas.width = nextWidth;
			canvas.height = nextHeight;
		}
	};

	const supportsWebgl = (() => {
		try {
			const gl = canvas.getContext('webgl', { antialias: true, alpha: true }) || canvas.getContext('experimental-webgl');
			return Boolean(gl);
		} catch (error) {
			return false;
		}
	})();

	const drawFallbackGlobe = () => {
		syncCanvasSize();
		const ctx = canvas.getContext('2d');
		if (!ctx) {
			return;
		}

		const w = canvas.width;
		const h = canvas.height;
		const cx = w / 2;
		const cy = h / 2;
		const r = Math.min(w, h) * 0.42;

		ctx.clearRect(0, 0, w, h);

		const bg = ctx.createRadialGradient(cx - r * 0.12, cy - r * 0.18, r * 0.22, cx, cy, r * 1.08);
		bg.addColorStop(0, '#2a3b55');
		bg.addColorStop(0.58, '#192738');
		bg.addColorStop(1, '#101b29');

		ctx.fillStyle = bg;
		ctx.beginPath();
		ctx.arc(cx, cy, r, 0, Math.PI * 2);
		ctx.fill();

		const shine = ctx.createRadialGradient(cx - r * 0.36, cy - r * 0.45, r * 0.05, cx - r * 0.1, cy - r * 0.1, r * 0.9);
		shine.addColorStop(0, 'rgba(182, 205, 236, 0.34)');
		shine.addColorStop(1, 'rgba(182, 205, 236, 0)');
		ctx.fillStyle = shine;
		ctx.beginPath();
		ctx.arc(cx, cy, r, 0, Math.PI * 2);
		ctx.fill();

		const continents = [
			[
				[-0.44, -0.2],
				[-0.25, -0.3],
				[-0.08, -0.16],
				[-0.1, 0.03],
				[-0.3, 0.12],
				[-0.48, -0.03],
			],
			[
				[0.02, -0.38],
				[0.24, -0.27],
				[0.27, -0.04],
				[0.1, 0.12],
				[-0.02, -0.04],
			],
			[
				[0.16, 0.02],
				[0.37, 0.1],
				[0.33, 0.35],
				[0.13, 0.28],
			],
		];

		ctx.fillStyle = 'rgba(243, 249, 255, 0.3)';
		continents.forEach((polygon) => {
			ctx.beginPath();
			polygon.forEach(([px, py], idx) => {
				const x = cx + px * r;
				const y = cy + py * r;
				if (idx === 0) {
					ctx.moveTo(x, y);
				} else {
					ctx.lineTo(x, y);
				}
			});
			ctx.closePath();
			ctx.fill();
		});

		ctx.strokeStyle = 'rgba(195, 219, 248, 0.42)';
		ctx.lineWidth = Math.max(1, r * 0.01);
		for (let i = -2; i <= 2; i += 1) {
			ctx.beginPath();
			ctx.ellipse(cx, cy, r, r * (1 - Math.abs(i) * 0.17), 0, 0, Math.PI * 2);
			ctx.stroke();
		}

		for (let i = -2; i <= 2; i += 1) {
			ctx.beginPath();
			ctx.ellipse(cx, cy, r * (1 - Math.abs(i) * 0.12), r, 0, 0, Math.PI * 2);
			ctx.stroke();
		}

		ctx.fillStyle = '#FFB162';
		ctx.beginPath();
		ctx.arc(cx + r * 0.2, cy + r * 0.08, Math.max(4, r * 0.038), 0, Math.PI * 2);
		ctx.fill();

		ctx.strokeStyle = 'rgba(255, 177, 98, 0.48)';
		ctx.lineWidth = Math.max(1, r * 0.008);
		ctx.beginPath();
		ctx.arc(cx + r * 0.2, cy + r * 0.08, Math.max(6, r * 0.088), 0, Math.PI * 2);
		ctx.stroke();
	};

	const targetPhi = (ARUSHA.lng * Math.PI) / 180;
	const targetTheta = 0.43;

	syncCanvasSize();

	let globe = null;
	if (!supportsWebgl) {
		drawFallbackGlobe();
	} else {

		try {
			globe = createGlobe(canvas, {
				devicePixelRatio: dpr,
				width: size.width,
				height: size.height,
				phi: state.phi,
				theta: state.theta,
				dark: 0,
				diffuse: 1.35,
				mapSamples: 14000,
				mapBrightness: 2.25,
				baseColor: [0.19, 0.31, 0.5],
				markerColor: [0.976, 0.451, 0.086],
				glowColor: [0.2, 0.31, 0.45],
				markers: [{ location: [ARUSHA.lat, ARUSHA.lng], size: 0.13 }],
				onRender: (renderState) => {
					state.phi += state.spin;

					renderState.phi = state.phi;
					renderState.theta = state.theta;
					renderState.width = size.width;
					renderState.height = size.height;
				},
			});
		} catch (error) {
			drawFallbackGlobe();
		}
	}

	const resizeObserver = new ResizeObserver(() => {
		syncCanvasSize();
		if (!globe) {
			drawFallbackGlobe();
		}
	});
	resizeObserver.observe(canvas);

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
		resizeObserver.disconnect();
		globe?.destroy();
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
