(function () {
  "use strict";

  var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var cfg = window.IntegralTheme || { ajaxUrl: "/wp-admin/admin-ajax.php", demoUrl: "/service-request-inquiry/" };

  function unlockScroll() {
    document.documentElement.style.overflow = "";
    document.body.style.overflow = "";
    document.body.style.position = "";
    document.body.style.width = "";
  }

  function lockScroll() {
    document.body.style.overflow = "hidden";
  }

  function onScroll() {
    var header = document.querySelector(".int-header");
    if (!header) return;
    header.classList.toggle(
      "is-scrolled",
      (window.pageYOffset || document.documentElement.scrollTop || 0) > 20
    );
  }

  function setupDrawer() {
    var toggle = document.querySelector(".int-menu-toggle");
    var drawer = document.querySelector(".int-drawer");
    if (!toggle || !drawer) return;

    toggle.addEventListener("click", function () {
      var open = drawer.classList.toggle("is-open");
      toggle.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      if (open) lockScroll();
      else unlockScroll();
    });

    drawer.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        drawer.classList.remove("is-open");
        toggle.classList.remove("is-open");
        unlockScroll();
      });
    });
  }

  function setupReveal() {
    var nodes = document.querySelectorAll(".int-reveal");
    if (!nodes.length) return;
    if (reduce || !("IntersectionObserver" in window)) {
      nodes.forEach(function (n) {
        n.classList.add("is-in");
      });
      return;
    }
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-in");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -6% 0px" }
    );
    nodes.forEach(function (n) {
      io.observe(n);
    });
  }

  function randVal() {
    return String(Math.floor(12 + Math.random() * 86));
  }

  function activateModule(tab) {
    if (!tab) return;
    var consoleEl = document.querySelector("[data-console]");
    if (!consoleEl) return;

    consoleEl.querySelectorAll(".int-console__tab").forEach(function (t) {
      t.classList.toggle("is-active", t === tab);
      t.setAttribute("aria-selected", t === tab ? "true" : "false");
    });

    var title = consoleEl.querySelector("[data-console-title]");
    var blurb = consoleEl.querySelector("[data-console-blurb]");
    var kpis = consoleEl.querySelector("[data-console-kpis]");
    if (title) title.textContent = tab.getAttribute("data-title") || "";
    if (blurb) blurb.textContent = tab.getAttribute("data-blurb") || "";

    var panel = [];
    try {
      panel = JSON.parse(tab.getAttribute("data-panel") || "[]");
    } catch (e) {
      panel = [];
    }

    if (kpis) {
      kpis.innerHTML = panel
        .map(function (label) {
          return (
            '<div class="int-console__kpi"><strong>' +
            randVal() +
            "</strong><small>" +
            label +
            "</small></div>"
          );
        })
        .join("");
    }
  }

  function setupConsole() {
    var consoleEl = document.querySelector("[data-console]");
    if (!consoleEl) return;

    consoleEl.querySelectorAll(".int-console__tab").forEach(function (tab) {
      tab.addEventListener("click", function () {
        activateModule(tab);
      });
    });

    document.querySelectorAll("[data-jump-module]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var id = btn.getAttribute("data-jump-module");
        var tab = consoleEl.querySelector('.int-console__tab[data-module="' + id + '"]');
        if (tab) {
          activateModule(tab);
          consoleEl.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "center" });
        }
      });
    });

    // Gentle KPI pulse
    if (!reduce) {
      setInterval(function () {
        consoleEl.querySelectorAll(".int-console__kpi strong").forEach(function (el, i) {
          if (i % 2 === 0) el.textContent = randVal();
        });
      }, 3200);
    }
  }

  function renderFacility(box, data) {
    var fields = [
      ["FR code", data.frCode],
      ["Level", data.level],
      ["Type", data.facilityType],
      ["County", data.county],
      ["Sub-county", data.subCounty],
      ["Beds", data.totalBeds],
      ["SHA status", data.shaStatus],
      ["Ownership", data.ownership],
      ["Phone", data.phone],
      ["Email", data.email],
    ].filter(function (row) {
      return row[1] !== undefined && row[1] !== null && String(row[1]).trim() !== "";
    });

    box.hidden = false;
    box.innerHTML =
      "<h3>" +
      (data.name || "Facility") +
      "</h3>" +
      '<div class="int-facility__meta">' +
      fields
        .map(function (row) {
          return (
            "<div><strong>" +
            row[0] +
            "</strong><span>" +
            String(row[1]) +
            "</span></div>"
          );
        })
        .join("") +
      "</div>";

    // Prefill demo form if present
    var nameField = document.querySelector("[data-demo-facility-name]");
    var frField = document.querySelector("[data-demo-fr-code]");
    if (nameField && data.name) nameField.value = data.name;
    if (frField && data.frCode) frField.value = data.frCode;

    // If facility type matches a button, select it
    if (data.level) {
      var levelHint = String(data.level).toLowerCase();
      document.querySelectorAll("[data-demo-facility-type]").forEach(function (btn) {
        var label = (btn.getAttribute("data-demo-facility-type") || "").toLowerCase();
        var match =
          (levelHint.indexOf("6") >= 0 && label.indexOf("level 6") >= 0) ||
          (levelHint.indexOf("5") >= 0 && label.indexOf("level 5") >= 0) ||
          (levelHint.indexOf("4") >= 0 && label.indexOf("level 4") >= 0) ||
          (levelHint.indexOf("3") >= 0 && label.indexOf("level 3") >= 0) ||
          (levelHint.indexOf("2") >= 0 && label.indexOf("level 2") >= 0);
        if (match) {
          btn.classList.add("is-on");
          var field = document.querySelector("[data-demo-facility-type-field]");
          if (field) field.value = btn.getAttribute("data-demo-facility-type") || "";
        }
      });
    }
  }

  function setupFacilityForms() {
    document.querySelectorAll("[data-facility-form]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var code = (form.querySelector('[name="code"]') || {}).value || "";
        var type = (form.querySelector('[name="type"]') || {}).value || "auto";
        var status = form.querySelector("[data-facility-status]");
        var result = form.querySelector("[data-facility-result]");
        code = String(code).trim();
        if (!code) return;

        if (status) {
          status.className = "int-facility__hint";
          status.textContent = "Searching facility registry…";
        }
        if (result) {
          result.hidden = true;
          result.innerHTML = "";
        }

        var url =
          cfg.ajaxUrl +
          "?action=integral_facility_lookup&code=" +
          encodeURIComponent(code) +
          "&type=" +
          encodeURIComponent(type);

        fetch(url, { credentials: "same-origin" })
          .then(function (res) {
            return res.json().then(function (json) {
              return { ok: res.ok, json: json };
            });
          })
          .then(function (payload) {
            if (payload.json && payload.json.success && payload.json.data) {
              if (status) {
                status.className = "int-facility__hint is-ok";
                var sourceLabel = "";
                if (payload.json.data.source === "sha-portal") sourceLabel = " via SHA portal";
                else if (payload.json.data.source === "sha-hie") sourceLabel = " via SHA HIE (same as HMIS institution setup)";
                else if (payload.json.data.source === "dha-fhir") sourceLabel = " via live DHA registry";
                else if (payload.json.data.source === "sha") sourceLabel = " via SHA HIE";
                else if (payload.json.data.source === "hmis") sourceLabel = " via Integral HMIS";
                else if (payload.json.data.source === "fr-cache") sourceLabel = " via facility registry";
                else if (payload.json.data.source) sourceLabel = " via " + payload.json.data.source.toUpperCase();
                status.textContent =
                  "Facility found" + sourceLabel + ".";
              }
              if (result) renderFacility(result, payload.json.data);
              return;
            }
            var msg =
              (payload.json && payload.json.data && payload.json.data.message) ||
              "No facility found for that FR code or registration number.";
            if (status) {
              status.className = "int-facility__hint is-error";
              status.textContent = msg;
            }
          })
          .catch(function () {
            if (status) {
              status.className = "int-facility__hint is-error";
              status.textContent = "Lookup failed. Try again shortly.";
            }
          });
      });
    });
  }

  function setupContactIntents() {
    var wrap = document.querySelector("[data-contact-intents]");
    if (!wrap) return;

    var label = document.querySelector("[data-contact-intent-label]");
    var hint = document.querySelector("[data-contact-hint]");

    function setSubject(value) {
      var subject =
        document.querySelector('.int-contact-composer input[name="your-subject"]') ||
        document.querySelector(".int-contact-composer [data-contact-subject]") ||
        document.querySelector('input[name="your-subject"]');
      if (subject) {
        subject.value = value;
        subject.dispatchEvent(new Event("input", { bubbles: true }));
      }
    }

    wrap.querySelectorAll("[data-intent]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        wrap.querySelectorAll("[data-intent]").forEach(function (b) {
          b.classList.toggle("is-on", b === btn);
        });
        var intent = btn.getAttribute("data-intent") || "";
        var tip = btn.getAttribute("data-hint") || "";
        if (label) label.textContent = intent;
        if (hint) hint.textContent = tip;
        setSubject(intent);
      });
    });

    // Default subject on load
    var active = wrap.querySelector(".int-contact-intent.is-on");
    if (active) setSubject(active.getAttribute("data-intent") || "HMIS Demo");
  }

  function setupDemoModal() {
    var modal = document.querySelector("[data-demo-modal]");
    if (!modal) return;

    var step = 1;
    var facilityType = "";
    var activeQuote = null;

    function showStep(n) {
      step = n;
      modal.querySelectorAll("[data-demo-step]").forEach(function (el) {
        el.classList.toggle("is-active", Number(el.getAttribute("data-demo-step")) === n);
      });
      var dots = modal.querySelectorAll("[data-demo-progress] span");
      if (activeQuote) {
        // Plan flow uses steps 2 and 3 only → map to 2 visible dots
        dots.forEach(function (el, i) {
          if (i === 0) {
            el.classList.remove("is-on");
            return;
          }
          var local = n - 1; // 2→1, 3→2
          el.classList.toggle("is-on", i <= local);
        });
      } else {
        dots.forEach(function (el, i) {
          el.classList.toggle("is-on", i < n);
        });
      }
    }

    function clearPlan() {
      activeQuote = null;
      var plan = modal.querySelector("[data-demo-plan]");
      if (plan) plan.hidden = true;
      ["mode", "level", "billing", "setup-ksh", "licence-ksh", "yearone-ksh", "summary"].forEach(function (key) {
        var el = modal.querySelector("[data-demo-plan-" + key + "]");
        if (el && el.tagName === "INPUT") el.value = "";
      });
    }

    function applyQuote(quote) {
      activeQuote = quote || null;
      var plan = modal.querySelector("[data-demo-plan]");
      if (!plan) return;
      if (!quote) {
        plan.hidden = true;
        return;
      }
      plan.hidden = false;
      var title = plan.querySelector("[data-demo-plan-title]");
      var setup = plan.querySelector("[data-demo-plan-setup]");
      var licence = plan.querySelector("[data-demo-plan-licence]");
      var total = plan.querySelector("[data-demo-plan-total]");
      var billLabel = plan.querySelector("[data-demo-plan-bill-label]");
      var fxLine = plan.querySelector("[data-demo-plan-fx]");
      if (title) title.textContent = quote.title;
      if (setup) setup.textContent = quote.setupKshLabel;
      if (licence) licence.textContent = quote.licenceKshLabel;
      if (total) total.textContent = quote.yearOneKshLabel;
      if (billLabel) billLabel.textContent = quote.billLabel;
      if (fxLine) {
        fxLine.hidden = true;
        fxLine.textContent = "";
      }

      var map = {
        mode: quote.modeLabel,
        level: String(quote.level),
        billing: quote.billing,
        "setup-ksh": String(quote.setupKsh),
        "licence-ksh": String(quote.licenceKsh),
        "yearone-ksh": String(quote.yearOneKsh),
        summary: quote.summary,
      };
      Object.keys(map).forEach(function (key) {
        var el = modal.querySelector("[data-demo-plan-" + key + "]");
        if (el) el.value = map[key];
      });
    }

    function syncPlanProgress() {
      var dots = modal.querySelectorAll("[data-demo-progress] span");
      if (!dots.length) return;
      if (activeQuote) {
        dots[0].style.display = "none";
      } else {
        dots[0].style.display = "";
      }
    }

    function openDemo(e, quote) {
      if (e) e.preventDefault();
      if (quote) {
        applyQuote(quote);
        var typeField = modal.querySelector("[data-demo-facility-type-field]");
        if (typeField) typeField.value = quote.modeLabel || quote.title || "";
      } else {
        clearPlan();
      }
      modal.hidden = false;
      syncPlanProgress();
      showStep(activeQuote ? 2 : 1);
      // Relabel steps when plan flow skips facility type
      var step2 = modal.querySelector('[data-demo-step="2"] .int-eyebrow');
      var step3 = modal.querySelector('[data-demo-step="3"] .int-eyebrow');
      if (activeQuote) {
        if (step2) step2.textContent = "Step 1";
        if (step3) step3.textContent = "Step 2";
      } else {
        if (step2) step2.textContent = "Step 2";
        if (step3) step3.textContent = "Step 3";
      }
      lockScroll();
    }

    function closeDemo() {
      modal.hidden = true;
      unlockScroll();
    }

    document.querySelectorAll("[data-demo-open]").forEach(function (el) {
      el.addEventListener("click", function (e) {
        openDemo(e, null);
      });
    });

    modal.querySelectorAll("[data-demo-close]").forEach(function (el) {
      el.addEventListener("click", closeDemo);
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !modal.hidden) closeDemo();
    });

    modal.querySelectorAll("[data-demo-facility-type]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        facilityType = btn.getAttribute("data-demo-facility-type") || "";
        modal.querySelectorAll("[data-demo-facility-type]").forEach(function (b) {
          b.classList.toggle("is-on", b === btn);
        });
        var field = modal.querySelector("[data-demo-facility-type-field]");
        if (field) field.value = facilityType;
      });
    });

    modal.querySelectorAll("[data-demo-next]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (step === 1) {
          var field = modal.querySelector("[data-demo-facility-type-field]");
          if (field && !field.value && facilityType) field.value = facilityType;
          showStep(2);
          return;
        }
        if (step === 2) {
          var checked = modal.querySelectorAll(".int-demo__mod input:checked");
          var mods = [];
          checked.forEach(function (c) {
            mods.push(c.value);
          });
          var modsField = modal.querySelector("[data-demo-modules-field]");
          if (modsField) modsField.value = mods.join(", ");
          showStep(3);
        }
      });
    });

    modal.querySelectorAll("[data-demo-back]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var min = activeQuote ? 2 : 1;
        showStep(Math.max(min, step - 1));
      });
    });

    var form = modal.querySelector("[data-demo-form]");
    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var data = new FormData(form);
        var submitBtn = form.querySelector('[type="submit"]');
        var subject = activeQuote
          ? "HMIS Plan — " + activeQuote.title + " — " + (data.get("facility") || "Facility")
          : "HMIS Demo — " + (data.get("facility") || "Facility") + " (" + (data.get("facility_type") || "Facility type") + ")";
        var lines = [
          "Facility type: " + (data.get("facility_type") || ""),
          "Facility: " + (data.get("facility") || ""),
          "FR code: " + (data.get("fr_code") || ""),
          "Modules: " + (data.get("modules") || ""),
          "Name: " + (data.get("name") || ""),
          "Email: " + (data.get("email") || ""),
          "Phone: " + (data.get("phone") || ""),
        ];
        if (activeQuote) {
          lines.unshift(
            "Plan: " + activeQuote.summary,
            "Setup: " + activeQuote.setupKshLabel + " | " + activeQuote.setupUsdLabel + " | " + activeQuote.setupEurLabel,
            "Licence (" + activeQuote.billing + "): " + activeQuote.licenceKshLabel + " | " + activeQuote.licenceUsdLabel + " | " + activeQuote.licenceEurLabel,
            "Year-one: " + activeQuote.yearOneKshLabel + " | " + activeQuote.yearOneUsdLabel + " | " + activeQuote.yearOneEurLabel,
            ""
          );
        }

        var payload = new FormData();
        payload.set("action", "integral_send_inquiry");
        payload.set("name", data.get("name") || "");
        payload.set("email", data.get("email") || "");
        payload.set("phone", data.get("phone") || "");
        payload.set("subject", subject);
        payload.set("message", lines.join("\n"));

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = "Sending…";
        }

        fetch(cfg.ajaxUrl, { method: "POST", body: payload, credentials: "same-origin" })
          .then(function (res) {
            return res.json().then(function (json) {
              return { ok: res.ok, json: json };
            });
          })
          .then(function (payload) {
            if (payload.json && payload.json.success) {
              form.reset();
              closeDemo();
              alert((payload.json.data && payload.json.data.message) || "Sent. We will get back to you shortly.");
              return;
            }
            var msg =
              (payload.json && payload.json.data && payload.json.data.message) ||
              "Could not send. Check mail/SMTP configuration.";
            alert(msg);
          })
          .catch(function () {
            alert("Could not send. Network or mail server error.");
          })
          .then(function () {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = "Book my demo";
            }
          });
      });
    }

    window.IntegralDemo = {
      open: function (quote) {
        openDemo(null, quote || null);
      },
      clearPlan: clearPlan,
    };
  }

  function setupMetrics() {
    var root = document.querySelector("[data-metrics]");
    if (!root) return;

    var cards = root.querySelectorAll("[data-metric]");
    cards.forEach(function (card) {
      card.addEventListener("mouseenter", function () {
        cards.forEach(function (c) {
          c.classList.toggle("is-active", c === card);
        });
      });
      card.addEventListener("focus", function () {
        cards.forEach(function (c) {
          c.classList.toggle("is-active", c === card);
        });
      });
    });
    root.addEventListener("mouseleave", function () {
      cards.forEach(function (c) {
        c.classList.remove("is-active");
      });
    });

    var counters = root.querySelectorAll("[data-count-to]");
    if (!counters.length) return;

    function animateCount(el, delay) {
      if (el.getAttribute("data-counted") === "1") return;
      el.setAttribute("data-counted", "1");
      var target = parseInt(el.getAttribute("data-count-to") || "0", 10);
      var suffix = el.getAttribute("data-count-suffix") || "";
      var card = el.closest("[data-metric]");
      var run = function () {
        if (reduce) {
          el.textContent = String(target) + suffix;
          if (card) card.classList.add("is-lit");
          return;
        }
        var start = null;
        var duration = 1300;
        function frame(ts) {
          if (!start) start = ts;
          var p = Math.min(1, (ts - start) / duration);
          var eased = 1 - Math.pow(1 - p, 3);
          el.textContent = String(Math.round(target * eased)) + suffix;
          if (p < 1) {
            requestAnimationFrame(frame);
          } else if (card) {
            card.classList.add("is-lit");
          }
        }
        requestAnimationFrame(frame);
      };
      if (delay) {
        setTimeout(run, delay);
      } else {
        run();
      }
    }

    function ignite() {
      counters.forEach(function (el, i) {
        animateCount(el, reduce ? 0 : i * 160);
      });
    }

    if (!("IntersectionObserver" in window)) {
      ignite();
      return;
    }

    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          ignite();
          io.unobserve(entry.target);
        });
      },
      { threshold: 0.3 }
    );
    io.observe(root);
  }

  function setupPricing() {
    var root = document.querySelector("[data-pricing]");
    if (!root) return;

    var mode = "cloud";
    var bill = "quarterly";
    var fx = "kes";
    var level = 2;
    var data = { modes: {}, rates: { usd: 130, eur: 148 } };
    var dataNode = root.querySelector("[data-pricing-data]");
    if (dataNode) {
      try {
        data = JSON.parse(dataNode.textContent || "{}");
      } catch (e) {}
    }
    var usdRate = Number((data.rates && data.rates.usd) || 130);
    var eurRate = Number((data.rates && data.rates.eur) || 148);

    var summary = root.querySelector("[data-pricing-summary]");

    function kesAmount(thousands) {
      return Math.round(Number(thousands) * 1000);
    }

    function formatKes(amount) {
      return Math.round(amount).toLocaleString("en-KE");
    }

    function formatFx(amount) {
      return amount.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }

    function money(thousands, currency) {
      var kes = kesAmount(thousands);
      if (currency === "usd") {
        return { code: "USD", text: formatFx(kes / usdRate), kes: kes };
      }
      if (currency === "eur") {
        return { code: "EUR", text: formatFx(kes / eurRate), kes: kes };
      }
      return { code: "KSh", text: formatKes(kes), kes: kes };
    }

    function altLine(thousands) {
      var kes = kesAmount(thousands);
      return "≈ USD " + formatFx(kes / usdRate) + " · EUR " + formatFx(kes / eurRate);
    }

    function labelFor(thousands, currency) {
      var m = money(thousands, currency);
      return m.code + " " + m.text;
    }

    function tierFor(currentMode, currentLevel) {
      var pack = (data.modes && data.modes[currentMode]) || null;
      if (!pack || !pack.levels) return null;
      for (var i = 0; i < pack.levels.length; i++) {
        if (Number(pack.levels[i].level) === Number(currentLevel)) {
          return { pack: pack, tier: pack.levels[i] };
        }
      }
      return pack.levels.length ? { pack: pack, tier: pack.levels[0] } : null;
    }

    function flashSummary() {
      if (!summary || reduce) return;
      summary.classList.remove("is-flash");
      void summary.offsetWidth;
      summary.classList.add("is-flash");
    }

    function currentQuote() {
      var found = tierFor(mode, level);
      if (!found) return null;
      var pack = found.pack;
      var tier = found.tier;
      var setupT = Number(tier.setup) || 0;
      var licenceT = Number(tier[bill]) || 0;
      var yearOneT = setupT + (bill === "yearly" ? licenceT : licenceT * 4);
      var saveT = Number(tier.quarterly) * 4 - Number(tier.yearly);
      var billLabel = bill === "yearly" ? "Licence / year" : "Licence / quarter";
      return {
        mode: mode,
        modeLabel: pack.label || "",
        level: Number(tier.level),
        billing: bill,
        billLabel: billLabel,
        title: (pack.label || "Plan") + " · Level " + tier.level,
        setupKsh: kesAmount(setupT),
        licenceKsh: kesAmount(licenceT),
        yearOneKsh: kesAmount(yearOneT),
        saveKsh: kesAmount(saveT),
        setupKshLabel: labelFor(setupT, "kes"),
        licenceKshLabel: labelFor(licenceT, "kes"),
        yearOneKshLabel: labelFor(yearOneT, "kes"),
        setupUsdLabel: labelFor(setupT, "usd"),
        licenceUsdLabel: labelFor(licenceT, "usd"),
        yearOneUsdLabel: labelFor(yearOneT, "usd"),
        setupEurLabel: labelFor(setupT, "eur"),
        licenceEurLabel: labelFor(licenceT, "eur"),
        yearOneEurLabel: labelFor(yearOneT, "eur"),
        fxLine: "",
        summary:
          (pack.label || "Plan") +
          " · Level " +
          tier.level +
          " · " +
          (bill === "yearly" ? "Yearly" : "Quarterly") +
          " · Setup " +
          labelFor(setupT, "kes") +
          " · Licence " +
          labelFor(licenceT, "kes") +
          " · Year-one " +
          labelFor(yearOneT, "kes"),
        usdRate: usdRate,
        eurRate: eurRate,
        display: fx,
      };
    }

    function setLevel(next) {
      level = Number(next) || 2;
      root.querySelectorAll("[data-pricing-level]").forEach(function (btn) {
        var on = Number(btn.getAttribute("data-pricing-level")) === level;
        btn.classList.toggle("is-on", on);
        btn.setAttribute("aria-selected", on ? "true" : "false");
      });
      render(true);
    }

    function render(doFlash) {
      var found = tierFor(mode, level);
      if (!found) return;
      var pack = found.pack;
      var tier = found.tier;
      var setupT = Number(tier.setup) || 0;
      var licenceT = Number(tier[bill]) || 0;
      var saveT = Number(tier.quarterly) * 4 - Number(tier.yearly);
      var yearOneT = setupT + (bill === "yearly" ? licenceT : licenceT * 4);
      var setupM = money(setupT, fx);
      var licenceM = money(licenceT, fx);
      var yearOneM = money(yearOneT, fx);

      var modeEl = root.querySelector("[data-summary-mode]");
      var levelEl = root.querySelector("[data-summary-level]");
      var setupSum = root.querySelector("[data-summary-setup]");
      var licenceSum = root.querySelector("[data-summary-licence]");
      var billLabel = root.querySelector("[data-summary-bill-label]");
      var totalSum = root.querySelector("[data-summary-total]");
      var saveSum = root.querySelector("[data-summary-save]");
      var thumb = root.querySelector("[data-billing-thumb]");

      root.querySelectorAll("[data-summary-code]").forEach(function (el) {
        el.textContent = setupM.code;
      });

      if (modeEl) modeEl.textContent = pack.label || "";
      if (levelEl) levelEl.textContent = "Level " + tier.level;
      if (setupSum) setupSum.textContent = setupM.text;
      if (licenceSum) licenceSum.textContent = licenceM.text;
      if (billLabel) billLabel.textContent = bill === "yearly" ? "Licence / year" : "Licence / quarter";
      if (totalSum) totalSum.textContent = yearOneM.text;
      if (thumb) thumb.classList.toggle("is-yearly", bill === "yearly");

      if (saveSum) {
        if (bill === "yearly" && saveT > 0) {
          saveSum.hidden = false;
          saveSum.textContent = "Yearly saves " + labelFor(saveT, fx) + " vs paying quarterly";
        } else {
          saveSum.hidden = true;
        }
      }
      if (doFlash) flashSummary();
    }

    root.querySelectorAll("[data-pricing-mode]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        mode = btn.getAttribute("data-pricing-mode") || "cloud";
        root.querySelectorAll("[data-pricing-mode]").forEach(function (b) {
          var on = b === btn;
          b.classList.toggle("is-on", on);
          b.setAttribute("aria-selected", on ? "true" : "false");
        });
        render(true);
      });
    });

    root.querySelectorAll("[data-pricing-bill]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        bill = btn.getAttribute("data-pricing-bill") || "quarterly";
        root.querySelectorAll("[data-pricing-bill]").forEach(function (b) {
          b.classList.toggle("is-on", b === btn);
        });
        var wrap = root.querySelector(".int-pricing__billing");
        if (wrap) wrap.classList.toggle("is-yearly", bill === "yearly");
        render(true);
      });
    });

    root.querySelectorAll("[data-pricing-fx]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        fx = btn.getAttribute("data-pricing-fx") || "kes";
        root.querySelectorAll("[data-pricing-fx]").forEach(function (b) {
          b.classList.toggle("is-on", b === btn);
        });
        render(true);
      });
    });

    root.querySelectorAll("[data-pricing-level]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        setLevel(btn.getAttribute("data-pricing-level") || "2");
      });
    });

    var cta = root.querySelector("[data-pricing-cta]");
    if (cta) {
      cta.addEventListener("click", function (e) {
        e.preventDefault();
        var quote = currentQuote();
        if (window.IntegralDemo && typeof window.IntegralDemo.open === "function") {
          window.IntegralDemo.open(quote);
        }
      });
    }

    render(false);
  }

  document.addEventListener("DOMContentLoaded", function () {
    unlockScroll();
    onScroll();
    setupDrawer();
    setupReveal();
    setupConsole();
    setupFacilityForms();
    setupMetrics();
    setupDemoModal();
    setupPricing();
    setupContactIntents();
  });

  window.addEventListener("scroll", onScroll, { passive: true });
})();
