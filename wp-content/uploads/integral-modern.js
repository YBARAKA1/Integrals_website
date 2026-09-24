/**
 * Integral Modern UI — motion & interactions
 */
(function ($) {
  "use strict";

  var REDUCE = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function onScrollHeader() {
    var scrolled = window.pageYOffset || document.documentElement.scrollTop || 0;
    document.body.classList.toggle("int-scrolled", scrolled > 24);
  }

  function setupReveal() {
    if (REDUCE || !("IntersectionObserver" in window)) {
      $(".int-reveal").addClass("int-in");
      return;
    }

    var targets = [
      ".gdlr-core-column-service-item",
      ".gdlr-core-title-item",
      ".gdlr-core-image-frame-item",
      ".gdlr-core-blog-grid",
      ".gdlr-core-text-box-item",
      ".gdlr-core-button-item",
      ".gdlr-core-call-to-action-item",
      ".gdlr-core-counter-item",
      ".gdlr-core-icon-list-item",
      ".realfactory-footer-column",
      ".ninjaborder"
    ].join(",");

    var $nodes = $(targets).not(".int-reveal");
    $nodes.each(function (i) {
      var $el = $(this);
      $el.addClass("int-reveal");
      var delay = (i % 4) + 1;
      $el.addClass("int-reveal-delay-" + delay);
    });

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("int-in");
            observer.unobserve(entry.target);
          }
        });
      },
      { rootMargin: "0px 0px -8% 0px", threshold: 0.12 }
    );

    $nodes.each(function () {
      observer.observe(this);
    });
  }

  function smoothAnchors() {
    $(document).on("click", 'a[href^="#"]:not([href="#"])', function (e) {
      var id = this.getAttribute("href");
      if (!id || id.length < 2) return;
      var $target = $(id);
      if (!$target.length) return;
      e.preventDefault();
      $("html, body").animate(
        { scrollTop: $target.offset().top - 80 },
        REDUCE ? 0 : 650,
        "swing"
      );
    });
  }

  function enhanceButtons() {
    // Subtle ripple-less press feedback via class
    $(document).on(
      "mousedown touchstart",
      ".gdlr-core-button, .ninjabt, .realfactory-header-right-button, .ninjabtinq a",
      function () {
        $(this).css("transform", "translateY(0) scale(0.98)");
      }
    );
    $(document).on(
      "mouseup mouseleave touchend",
      ".gdlr-core-button, .ninjabt, .realfactory-header-right-button, .ninjabtinq a",
      function () {
        $(this).css("transform", "");
      }
    );
  }

  $(function () {
    document.documentElement.classList.remove("no-js");
    document.documentElement.classList.add("int-modern");
    onScrollHeader();
    setupReveal();
    smoothAnchors();
    enhanceButtons();
  });

  $(window).on("scroll", onScrollHeader);
  $(window).on("load", function () {
    // Catch late-rendered builder items
    setupReveal();
    onScrollHeader();
  });
})(jQuery);
