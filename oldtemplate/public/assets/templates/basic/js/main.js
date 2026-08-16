"user strict";

// Preloader
(function () {
	var hidePreloader = function () {
		var preLoader = $(".preloader");
		if (!preLoader.length || preLoader.hasClass("is-hiding")) {
			return;
		}
		preLoader.addClass("is-hiding");
		setTimeout(function () {
			preLoader.remove();
		}, 220);
	};

	$(function () {
		setTimeout(hidePreloader, 180);
	});

	setTimeout(hidePreloader, 900);
})();

//Menu Dropdown
$("ul>li>.sub-menu").parent("li").addClass("has-sub-menu");

$(".menu li a").on("click", function () {
	var element = $(this).parent("li");
	if (element.hasClass("open")) {
		element.removeClass("open");
		element.find("li").removeClass("open");
		element.find("ul").slideUp(300, "swing");
	} else {
		element.addClass("open");
		element.children("ul").slideDown(300, "swing");
		element.siblings("li").children("ul").slideUp(300, "swing");
		element.siblings("li").removeClass("open");
		element.siblings("li").find("li").removeClass("open");
		element.siblings("li").find("ul").slideUp(300, "swing");
	}
});

// Responsive Menu
$('.header-trigger').on('click', function () {
	$('.menu').toggleClass('active');
	$(".overlay").toggleClass("active");
	$(this).toggleClass('change-icon')
});
$('.overlay').on('click', function () {
	$('.menu').removeClass('active');
	$(".overlay").removeClass("active");
	$('.header-trigger').removeClass('change-icon')
});

// Responsive Menu
$('.show-sidebar-bar').on('click', function () {
	$('.collapable-sidebar').toggleClass('show');
	$(".overlay").toggleClass("show-overlay");
	$(this).toggleClass('change-icon')
});

$('.overlay, .collapable-sidebar__close').on('click', function () {
	$('.collapable-sidebar').removeClass('show');
	$(".overlay").removeClass("show-overlay");
	$('.show-sidebar-bar').removeClass('change-icon');
});

// Overlay Event
var over = $(".overlay");
over.on("click", function () {
	$(".overlay").removeClass("active");
});

// Sticky Menu
var header = document.querySelector(".header");
if (header) {
	window.addEventListener("scroll", function () {
		header.classList.toggle("sticky", window.scrollY > 0);
	});
}

$(".sidebar-menu li a").on("click", function (e) {
	$(".sidebar-submenu").removeClass("active");
	$(this).siblings(".sidebar-submenu").toggleClass("active");
});

$(".sidebar-submenu").parent().addClass("has-submenu");

let elem = document.documentElement;
/* View in fullscreen */
function openFullscreen() {
	if (elem.requestFullscreen) {
		elem.requestFullscreen();
	} else if (elem.mozRequestFullScreen) {
		/* Firefox */
		elem.mozRequestFullScreen();
	} else if (elem.webkitRequestFullscreen) {
		/* Chrome, Safari and Opera */
		elem.webkitRequestFullscreen();
	} else if (elem.msRequestFullscreen) {
		/* IE/Edge */
		elem.msRequestFullscreen();
	}
}

/* Close fullscreen */
function closeFullscreen() {
	if (document.exitFullscreen) {
		document.exitFullscreen();
	} else if (document.mozCancelFullScreen) {
		/* Firefox */
		document.mozCancelFullScreen();
	} else if (document.webkitExitFullscreen) {
		/* Chrome, Safari and Opera */
		document.webkitExitFullscreen();
	} else if (document.msExitFullscreen) {
		/* IE/Edge */
		document.msExitFullscreen();
	}
}

function copyText() {
	var copyText = document.getElementById("referralURL");
	var copyText = document.getElementById("ref-url");
	copyText.select();
	copyText.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(copyText.value);
}

//Faq
$(".faq-item__title").on("click", function (e) {
	var element = $(this).parent(".faq-item");
	if (element.hasClass("open")) {
		element.removeClass("open");
		element.find(".faq-item__content").removeClass("open");
		element.find(".faq-item__content").slideUp(300, "swing");
	} else {
		element.addClass("open");
		element.children(".faq-item__content").slideDown(300, "swing");
		element.siblings(".faq-item").children(".faq-item__content").slideUp(300, "swing");
		element.siblings(".faq-item").removeClass("open");
		element.siblings(".faq-item").find(".faq-item__content").slideUp(300, "swing");
	}
});

// Subscribe box Js Start
$('.subscribe-btn').on('click', function () {
	$('.subscribe-box').toggleClass('show')
});

$('.subscribe__close').on('click', function () {
	$('.subscribe-box').removeClass('show')
});

// Subscribe box Js End

var inputElements = $('input,select');
$.each(inputElements, function (index, element) {
	element = $(element);

	if (!element.hasClass('exclude')) {
		if (!element.closest('.form-group').find('label').attr('for')) {
			element.closest('.form-group').find('label').attr('for', element.attr('name'));
			element.attr('id', element.attr('name'))
		}
	}
});

var inputElements = $('[type=text],select,textarea');
$.each(inputElements, function (index, element) {
	element = $(element);

	if (!element.hasClass('exclude')) {
		element.closest('.form-group').find('label').attr('for', element.attr('name'));
		element.attr('id', element.attr('name'))
	}
});

$.each($('input, select, textarea'), function (i, element) {
	var elementType = $(element);
	if (elementType.attr('type') != 'checkbox') {
		if (element.hasAttribute('required')) {
			$(element).closest('.form-group').find('label').addClass('required');
		}
	}
});

Array.from(document.querySelectorAll('table')).forEach(table => {
	let heading = table.querySelectorAll('thead tr th');
	Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
		Array.from(row.querySelectorAll('td')).forEach((colum, i) => {

			try {
				if (colum.hasAttribute('colspan') && i == 0) {
					return false;
				}

				colum.setAttribute('data-label', heading[i].innerText)
			} catch (message) { }

		});
	});
});
