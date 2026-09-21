/**
 * زرین — اسکریپت اصلی قالب
 */
(function () {
	'use strict';

	// کلاس js برای فعال‌سازی انیمیشن‌ها.
	document.documentElement.classList.add('js');

	var header = document.getElementById('siteHeader');
	var toTop = document.getElementById('toTop');
	var navToggle = document.getElementById('navToggle');
	var navOverlay = document.getElementById('navOverlay');
	var searchToggle = document.querySelector('.search-toggle');
	var searchBar = document.getElementById('searchBar');

	/* --- هدر چسبان و دکمه بازگشت به بالا --- */
	function onScroll() {
		var y = window.scrollY || 0;
		if (header) {
			header.classList.toggle('scrolled', y > 10);
		}
		if (toTop) {
			toTop.classList.toggle('show', y > 420);
		}
	}
	document.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	/* --- منوی موبایل --- */
	function closeNav() {
		if (header) {
			header.classList.remove('nav-open');
		}
		if (navToggle) {
			navToggle.setAttribute('aria-expanded', 'false');
		}
	}
	if (navToggle && header) {
		navToggle.addEventListener('click', function () {
			var open = header.classList.toggle('nav-open');
			navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}
	if (navOverlay) {
		navOverlay.addEventListener('click', closeNav);
	}
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			closeNav();
			if (searchBar) {
				searchBar.classList.remove('open');
			}
		}
	});

	/* --- نوار جستجو --- */
	if (searchToggle && searchBar) {
		searchToggle.addEventListener('click', function (e) {
			e.preventDefault();
			var open = searchBar.classList.toggle('open');
			searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			var input = searchBar.querySelector('input[type=search]');
			if (open && input) {
				input.focus();
			}
		});
	}

	/* --- بازگشت به بالا --- */
	if (toTop) {
		toTop.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}

	/* --- تبدیل ارقام به فارسی --- */
	function toFaDigits(str) {
		return String(str).replace(/[0-9]/g, function (d) {
			return '۰۱۲۳۴۵۶۷۸۹'[d];
		});
	}
	document.querySelectorAll('[data-fa-num]').forEach(function (el) {
		el.textContent = toFaDigits(el.textContent);
	});

	/* --- انیمیشن ورود عناصر --- */
	var revealEls = document.querySelectorAll('.reveal');
	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('in-view');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12 }
		);
		revealEls.forEach(function (el) {
			io.observe(el);
		});
	} else {
		revealEls.forEach(function (el) {
			el.classList.add('in-view');
		});
	}

	/* --- بستن منو با کلیک روی لینک‌ها (در موبایل) --- */
	document.querySelectorAll('.main-nav a').forEach(function (link) {
		link.addEventListener('click', function () {
			if (window.innerWidth <= 768) {
				closeNav();
			}
		});
	});
})();
