(function () {
	'use strict';

	const desktop = window.matchMedia('(min-width: 68.75rem)');

	document.querySelectorAll('[data-scs-navigation]').forEach(function (header) {
		const nav = header.querySelector('[data-scs-links]');
		const toggle = header.querySelector('[data-scs-mobile]');
		const search = header.querySelector('[data-scs-search]');
		const panels = Array.from(header.querySelectorAll('[data-scs-panel], [data-scs-search]'));
		let active = null;
		let hovered = null;
		let hoverExit;

		function closePanel() {
			window.clearTimeout(hoverExit);
			if (active) active.open = false;
			active = null;
			hovered = null;
		}

		function openPanel(panel, byHover) {
			closePanel();
			panel.open = true;
			active = panel;
			hovered = byHover ? panel : null;
		}

		function setMobileMenu(open) {
			if (!nav || !toggle) return;
			toggle.setAttribute('aria-expanded', String(open));
			nav.hidden = !desktop.matches && !open;
		}

		function mobileMenuIsOpen() {
			return toggle && toggle.getAttribute('aria-expanded') === 'true';
		}

		panels.forEach(function (panel) {
			const summary = panel.querySelector('summary');
			summary.addEventListener('click', function (event) {
				event.preventDefault();
				const fromHover = hovered === panel;
				window.clearTimeout(hoverExit);
				if (panel.open && !fromHover) {
					closePanel();
					return;
				}
				if (panel === search) setMobileMenu(false);
				openPanel(panel, false);
				if (panel === search) panel.querySelector('input').focus();
			});

			if (panel === search) return;
			panel.addEventListener('pointerenter', function (event) {
				if (!desktop.matches || event.pointerType !== 'mouse') return;
				window.clearTimeout(hoverExit);
				if (active === panel || (active && active.contains(document.activeElement))) return;
				openPanel(panel, true);
			});
			panel.addEventListener('pointerleave', function () {
				if (hovered !== panel) return;
				hoverExit = window.setTimeout(function () {
					if (hovered === panel && !panel.contains(document.activeElement)) closePanel();
				}, 180);
			});
		});

		if (toggle) {
			toggle.hidden = false;
			toggle.addEventListener('click', function () {
				const open = !mobileMenuIsOpen();
				closePanel();
				setMobileMenu(open);
				if (open) nav.querySelector('summary, a').focus();
			});
		}

		function syncLayout() {
			closePanel();
			setMobileMenu(false);
		}

		document.addEventListener('focusin', function (event) {
			if (active && !active.contains(event.target) && (desktop.matches || active === search)) closePanel();
			if (mobileMenuIsOpen() && !header.contains(event.target)) {
				closePanel();
				setMobileMenu(false);
			}
		});
		document.addEventListener('click', function (event) {
			if (active && !active.contains(event.target)) closePanel();
			if (mobileMenuIsOpen() && !header.contains(event.target)) setMobileMenu(false);
		});
		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape' || event.defaultPrevented) return;
			if (active) {
				const summary = active.querySelector('summary');
				const focused = active.contains(document.activeElement);
				closePanel();
				if (focused) summary.focus();
			} else if (mobileMenuIsOpen()) {
				setMobileMenu(false);
				toggle.focus();
			} else {
				return;
			}
			event.preventDefault();
		});

		syncLayout();
		header.setAttribute('data-enhanced', '');
		desktop.addEventListener('change', syncLayout);
	});
})();
