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

	/* --- به‌روزرسانی لحظه‌ای قیمت طلا --- */
	if (window.zarrinLive && window.zarrinLive.ajax && window.zarrinLive.enabled && document.querySelector('[data-live-item]')) {
		var refreshPrices = function () {
			if (!window.fetch) return;
			fetch(window.zarrinLive.ajax + '?action=zarrin_live_prices', { cache: 'no-store' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!res || !res.success || !res.data || !res.data.items) return;
					var items = res.data.items;
					Object.keys(items).forEach(function (key) {
						document.querySelectorAll('[data-live-item="' + key + '"]').forEach(function (card) {
							var val = card.querySelector('.live-value');
							if (val && items[key].formatted) {
								val.textContent = items[key].formatted;
								card.classList.add('flash');
								window.setTimeout(function () { card.classList.remove('flash'); }, 1300);
							}
							var ch = card.querySelector('.live-change');
							if (ch) ch.innerHTML = items[key].badge || '';
						});
					});
					var updated = document.querySelector('.live-updated');
					if (updated && res.data.updated) updated.textContent = res.data.updated;
					/* ارسال نرخ‌ها برای محاسبه‌گر طلا */
					try {
						var detail = { p18: 0, p24: 0, updated: res.data.updated || '' };
						if (items.p18 && items.p18.raw) { detail.p18 = parseFloat(items.p18.raw); }
						if (items.p24 && items.p24.raw) { detail.p24 = parseFloat(items.p24.raw); }
						document.dispatchEvent(new CustomEvent('zarrin:prices', { detail: detail }));
					} catch (err) { /* نادیده */ }
				})
				.catch(function () { /* خطا: قیمت فعلی حفظ می‌شود */ });
		};
		window.setInterval(refreshPrices, Math.max(30, parseInt(window.zarrinLive.interval, 10) || 90) * 1000);
	}

	/* --- بستن منو با کلیک روی لینک‌ها (در موبایل) --- */
	document.querySelectorAll('.main-nav a').forEach(function (link) {
		link.addEventListener('click', function () {
			if (window.innerWidth <= 768) {
				closeNav();
			}
		});
	});

	/* =========================================================
	 * اسلایدر چنداسلایدی (هیرو و نظرات مشتریان)
	 * ======================================================= */
	var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function initSlider(root) {
		var slides = [].slice.call(root.querySelectorAll('.zslide'));
		if (slides.length < 2) {
			root.classList.add('is-static');
			return;
		}
		var dots = root.querySelector('.zslider-dots');
		var idx = 0;
		var timer = null;
		var autoplay = parseInt(root.getAttribute('data-autoplay'), 10) || 0;

		function render(i) {
			idx = (i + slides.length) % slides.length;
			slides.forEach(function (s, j) {
				var on = (j === idx);
				s.classList.toggle('is-active', on);
				s.setAttribute('aria-hidden', on ? 'false' : 'true');
				if (!on) { s.setAttribute('inert', ''); } else { s.removeAttribute('inert'); }
			});
			if (dots) {
				[].slice.call(dots.children).forEach(function (d, j) {
					d.classList.toggle('is-active', j === idx);
					d.setAttribute('aria-selected', j === idx ? 'true' : 'false');
					d.setAttribute('tabindex', j === idx ? '0' : '-1');
				});
			}
			root.dispatchEvent(new CustomEvent('zslider:change', { detail: { index: idx } }));
		}

		if (dots && !dots.children.length) {
			slides.forEach(function (s, j) {
				var b = document.createElement('button');
				b.type = 'button';
				b.className = 'zslider-dot';
				b.setAttribute('role', 'tab');
				b.setAttribute('aria-label', 'اسلاید ' + (j + 1));
				b.addEventListener('click', function () { render(j); restart(); });
				dots.appendChild(b);
			});
		}

		function next() { render(idx + 1); }
		function prev() { render(idx - 1); }
		function start() { if (autoplay > 0) { timer = window.setInterval(next, autoplay); } }
		function stop() { if (timer) { window.clearInterval(timer); timer = null; } }
		function restart() { stop(); start(); }

		root.querySelectorAll('.znext').forEach(function (b) {
			b.addEventListener('click', function () { next(); restart(); });
		});
		root.querySelectorAll('.zprev').forEach(function (b) {
			b.addEventListener('click', function () { prev(); restart(); });
		});

		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		root.addEventListener('focusin', stop);
		root.addEventListener('focusout', start);
		document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { next(); restart(); }
			if (e.key === 'ArrowRight') { prev(); restart(); }
		});

		/* کشیدن انگشتی */
		var startX = 0, dragging = false;
		root.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; dragging = true; stop(); }, { passive: true });
		root.addEventListener('touchend', function (e) {
			if (!dragging) { return; }
			dragging = false;
			var dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 40) { dx > 0 ? prev() : next(); }
			start();
		});

		render(0);
		if (!reducedMotion) { start(); }
	}

	document.querySelectorAll('[data-zslider]').forEach(initSlider);

	/* =========================================================
	 * نوار متحرک نرخ طلا (تیکر)
	 * ======================================================= */
	document.querySelectorAll('.zmarquee').forEach(function (m) {
		var inner = m.querySelector('.zmarquee-inner');
		if (inner && !inner.getAttribute('data-cloned')) {
			inner.innerHTML += inner.innerHTML;
			inner.setAttribute('data-cloned', '1');
		}
	});

	/* =========================================================
	 * منوی همبرگری: آکاردئون زیرمنو، قفل اسکرول، بستن با لینک
	 * ======================================================= */
	var mainNav = document.getElementById('mainNav');

	function lockBody(on) {
		document.body.classList.toggle('zarrin-drawer-open', !!on);
	}

	if (navToggle && header) {
		var observer = new MutationObserver(function () {
			lockBody(header.classList.contains('nav-open'));
		});
		observer.observe(header, { attributes: true, attributeFilter: ['class'] });
	}

	if (mainNav) {
		mainNav.querySelectorAll('.menu-item-has-children').forEach(function (li) {
			var link = li.querySelector(':scope > a');
			var sub = li.querySelector(':scope > .sub-menu');
			if (!sub || !link) { return; }
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'nav-acc-btn';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', 'نمایش زیرمنو');
			btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>';
			link.parentNode.insertBefore(btn, link.nextSibling);
			btn.addEventListener('click', function () {
				var open = li.classList.toggle('acc-open');
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
				sub.hidden = !open;
			});
		});
	}

	/* =========================================================
	 * گام‌شمار تعداد در صفحه محصول
	 * ======================================================= */
	document.querySelectorAll('.woocommerce div.product form.cart .quantity').forEach(function (q) {
		if (q.querySelector('.zqty')) { return; }
		var input = q.querySelector('input.qty');
		if (!input) { return; }
		var wrap = document.createElement('div');
		wrap.className = 'zqty';
		var minus = document.createElement('button');
		minus.type = 'button'; minus.textContent = '−'; minus.setAttribute('aria-label', 'کاهش تعداد');
		var plus = document.createElement('button');
		plus.type = 'button'; plus.textContent = '+'; plus.setAttribute('aria-label', 'افزایش تعداد');
		q.insertBefore(wrap, input);
		wrap.appendChild(minus); wrap.appendChild(input); wrap.appendChild(plus);
		function step(d) {
			var v = parseInt(input.value, 10) || 1;
			var min = parseInt(input.getAttribute('min'), 10) || 1;
			var max = parseInt(input.getAttribute('max'), 10) || 9999;
			v = Math.min(max, Math.max(min, v + d));
			input.value = v;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		}
		minus.addEventListener('click', function () { step(-1); });
		plus.addEventListener('click', function () { step(1); });
	});

	/* =========================================================
	 * تب‌های ورود/ثبت‌نام
	 * ======================================================= */
	document.querySelectorAll('.zauth').forEach(function (box) {
		var tabs = box.querySelectorAll('[data-zauth-tab]');
		var panels = box.querySelectorAll('[data-zauth-panel]');
		tabs.forEach(function (t) {
			t.addEventListener('click', function () {
				var name = t.getAttribute('data-zauth-tab');
				tabs.forEach(function (x) {
					var on = x === t;
					x.classList.toggle('is-active', on);
					x.setAttribute('aria-selected', on ? 'true' : 'false');
				});
				panels.forEach(function (p) { p.hidden = p.getAttribute('data-zauth-panel') !== name; });
			});
		});
		var pass = box.querySelector('[data-zpass]');
		var meter = box.querySelector('[data-zstrength]');
		if (pass && meter) {
			pass.addEventListener('input', function () {
				var v = pass.value, score = 0;
				if (v.length >= 8) { score++; }
				if (/[A-Za-z]/.test(v) && /[0-9]/.test(v)) { score++; }
				if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) { score++; }
				meter.setAttribute('data-level', String(score));
				meter.firstElementChild.style.width = (score * 33.34) + '%';
			});
		}
	});

	/* =========================================================
	 * محاسبه‌گر قیمت روز طلا
	 * ======================================================= */
	function toEnglishDigits(str) {
		return String(str || '').replace(/[۰-۹]/g, function (d) { return String('۰۱۲۳۴۵۶۷۸۹'.indexOf(d)); })
			.replace(/[٠-٩]/g, function (d) { return String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)); })
			.replace(/[^\d.]/g, '');
	}

	document.querySelectorAll('[data-zcalc]').forEach(function (box) {
		var get = function (sel) { return box.querySelector(sel); };
		var wEl = get('[data-zcalc-weight]'), kEl = get('[data-zcalc-karat]'),
			gEl = get('[data-zcalc-wage]'), pEl = get('[data-zcalc-profit]'),
			out = get('[data-zcalc-result]');
		var p18 = parseFloat(box.getAttribute('data-p18')) || 0;
		var p24 = parseFloat(box.getAttribute('data-p24')) || 0;
		if (!out) { return; }

		function unitPrice(karat) {
			var k = parseFloat(karat) || 18;
			if (k >= 24 && p24 > 0) { return p24; }
			if (p18 > 0) { return p18 * (k / 18); }
			return 0;
		}
		function calc() {
			var w = parseFloat(toEnglishDigits(wEl && wEl.value)) || 0;
			var k = kEl ? kEl.value : '18';
			var wage = parseFloat(toEnglishDigits(gEl && gEl.value)) || 0;
			var profit = parseFloat(toEnglishDigits(pEl && pEl.value)) || 0;
			var unit = unitPrice(k);
			var total = w * unit * (1 + (wage + profit) / 100);
			out.textContent = total > 0 ? toFaDigits(Math.round(total).toLocaleString('en-US').replace(/,/g, '٬')) : '—';
		}
		[wEl, gEl, pEl].forEach(function (el) { if (el) { el.addEventListener('input', calc); } });
		if (kEl) { kEl.addEventListener('change', calc); }
		calc();

		/* به‌روزرسانی نرخ‌ها با تیک قیمت */
		document.addEventListener('zarrin:prices', function (e) {
			var d = e.detail || {};
			if (d.p18) { p18 = d.p18; }
			if (d.p24) { p24 = d.p24; }
			var upd = box.querySelector('[data-zcalc-updated]');
			if (upd && d.updated) { upd.textContent = d.updated; }
			calc();
		});
	});

	/* =========================================================
	 * نوار چسبان «افزودن به سبد» در موبایل
	 * ======================================================= */
	var singleBtn = document.querySelector('.single_add_to_cart_button');
	if (singleBtn && window.innerWidth <= 768 && !document.body.classList.contains('zarrin-no-sticky-atc')) {
		var bar = document.createElement('div');
		bar.className = 'zsticky-atc';
		var priceEl = document.querySelector('.woocommerce div.product p.price');
		bar.innerHTML = '<div class="zsticky-atc-inner">'
			+ '<span class="zsticky-price">' + (priceEl ? priceEl.innerHTML : '') + '</span>'
			+ '</div>';
		document.body.appendChild(bar);
		var inner = bar.querySelector('.zsticky-atc-inner');
		var btn = singleBtn.cloneNode(true);
		btn.classList.remove('single_add_to_cart_button');
		btn.addEventListener('click', function () { singleBtn.click(); });
		inner.appendChild(btn);
		btn.style.cssText = 'flex:1;background:var(--grad-gold);color:var(--gold-ink);border:0;border-radius:12px;padding:12px;font-weight:900;text-align:center;';

		var barObs = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				bar.classList.toggle('is-visible', !en.isIntersecting && en.boundingClientRect.top < 0);
			});
		}, { threshold: 0 });
		barObs.observe(singleBtn);
	}

	/* =========================================================
	 * کد تخفیف جمع‌شو در سبد خرید
	 * ======================================================= */
	var coupon = document.querySelector('.woocommerce-cart-form .coupon');
	if (coupon) {
		var toggle = document.createElement('button');
		toggle.type = 'button';
		toggle.className = 'button';
		toggle.style.cssText = 'margin-bottom:12px';
		toggle.textContent = 'دارید کد تخفیف؟';
		coupon.parentNode.insertBefore(toggle, coupon);
		coupon.style.display = 'none';
		toggle.addEventListener('click', function () {
			var open = coupon.style.display === 'none';
			coupon.style.display = open ? 'flex' : 'none';
			toggle.textContent = open ? 'بستن کد تخفیف' : 'دارید کد تخفیف؟';
		});
	}


	/* =========================================================
	 * سوالات متداول: فقط یک پاسخ باز بماند (آکاردئون)
	 * ======================================================= */
	document.querySelectorAll('.zfaq').forEach(function (faq) {
		var items = [].slice.call(faq.querySelectorAll('details.zfaq-item'));
		items.forEach(function (d) {
			d.addEventListener('toggle', function () {
				if (!d.open) { return; }
				items.forEach(function (o) { if (o !== d) { o.open = false; } });
			});
		});
	});

})();
