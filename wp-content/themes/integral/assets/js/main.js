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

    drawer.querySelectorAll("a, [data-drawer-close]").forEach(function (link) {
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

    var kind = tab.getAttribute("data-kind") || "kpis";
    if (kpis) {
      if (kind === "cards") {
        kpis.className = "int-console__kpis int-console__kpis--cards";
        kpis.innerHTML = panel
          .map(function (item) {
            var name = typeof item === "string" ? item : item && item.name ? item.name : "";
            var hint = typeof item === "string" ? "" : item && item.hint ? item.hint : "";
            return (
              '<div class="int-console__chip">' +
              "<strong>" +
              name +
              "</strong>" +
              (hint ? "<span>" + hint + "</span>" : "") +
              "</div>"
            );
          })
          .join("");
      } else {
        kpis.className = "int-console__kpis";
        kpis.innerHTML = panel
          .map(function (label) {
            var text = typeof label === "string" ? label : label && label.name ? label.name : "";
            return (
              '<div class="int-console__kpi"><strong>' +
              randVal() +
              "</strong><small>" +
              text +
              "</small></div>"
            );
          })
          .join("");
      }
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

  function clearQuoteContactFacilityFields() {
    var nameField = document.querySelector("[data-demo-facility-name]");
    var frField = document.querySelector("[data-demo-fr-code]");
    var typeField = document.querySelector("[data-demo-facility-type-field]");
    var levelField = document.querySelector("[data-demo-facility-level]");
    var snapshotField = document.querySelector("[data-demo-facility-snapshot]");
    var emailField = document.querySelector("[data-demo-email]");
    var phoneField = document.querySelector("[data-demo-phone]");
    if (nameField) nameField.value = "";
    if (frField) frField.value = "";
    if (typeField) typeField.value = "";
    if (levelField) levelField.value = "";
    if (snapshotField) snapshotField.value = "";
    if (emailField) emailField.value = "";
    if (phoneField) phoneField.value = "";
  }

  function renderFacility(box, data) {
    var fields = [
      ["FR code", data.frCode],
      ["FID", data.fidCode],
      ["Type", data.facilityType],
      ["Level", data.level],
      ["Ownership", data.ownership],
      ["County", data.county],
      ["Sub-county", data.subCounty],
      ["Town", data.town],
      ["Phone", data.phone],
      ["Email", data.email],
      ["SHA status", data.shaStatus],
      ["Licence", data.licenseStatus],
      ["Matched as", data.matchedType],
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

    // Prefill quote form (same as HMIS institution.js auto-fill)
    var nameField = document.querySelector("[data-demo-facility-name]");
    var frField = document.querySelector("[data-demo-fr-code]");
    var emailField = document.querySelector("[data-demo-email]");
    var phoneField = document.querySelector("[data-demo-phone]");
    var levelField = document.querySelector("[data-demo-facility-level]");
    var snapshotField = document.querySelector("[data-demo-facility-snapshot]");
    if (nameField && data.name) nameField.value = data.name;
    if (frField && data.frCode) frField.value = data.frCode;
    if (emailField && data.email) emailField.value = String(data.email).trim();
    if (phoneField && data.phone) phoneField.value = String(data.phone).trim();

    if (snapshotField) {
      snapshotField.value = JSON.stringify({
        name: data.name || "",
        frCode: data.frCode || "",
        fidCode: data.fidCode || "",
        facilityType: data.facilityType || "",
        level: data.level || "",
        ownership: data.ownership || "",
        county: data.county || "",
        subCounty: data.subCounty || "",
        town: data.town || "",
        phone: data.phone || "",
        email: data.email || "",
        shaStatus: data.shaStatus || "",
        licenseStatus: data.licenseStatus || "",
        matchedType: data.matchedType || "",
      });
    }

    if (data.level) {
      var levelHint = String(data.level).toLowerCase();
      var levelNum = "";
      var m = levelHint.match(/(?:level\s*)?([2-6])/);
      if (m) levelNum = m[1];
      if (levelField && levelNum) levelField.value = levelNum;

      var field = document.querySelector("[data-demo-facility-type-field]");
      if (field) {
        var label = "";
        if (levelHint.indexOf("6") >= 0) label = "Level 6 — National / teaching referral";
        else if (levelHint.indexOf("5") >= 0) label = "Level 5 — County referral hospital";
        else if (levelHint.indexOf("4") >= 0) label = "Level 4 — Sub-county / Primary hospital";
        else if (levelHint.indexOf("3") >= 0) label = "Level 3 — Health Centre";
        else if (levelHint.indexOf("2") >= 0) label = "Level 2 — Dispensary / Clinic";
        if (label) field.value = label;
      }
    }
  }

  function runFacilityLookup(form) {
    var code = (form.querySelector('[name="code"]') || {}).value || "";
    var type = (form.querySelector('[name="type"]') || {}).value || "auto";
    var status = form.querySelector("[data-facility-status]");
    var result = form.querySelector("[data-facility-result]");
    var terminal = form.querySelector("[data-facility-terminal]");
    var submitBtn = form.querySelector('button[type="submit"]');
    var continueBtn = form.querySelector("[data-demo-next], [data-facility-continue]");
    code = String(code).trim();
    var requestCode = code;

    if (!code) {
      resetFacilityHint(form);
      if (result) {
        result.hidden = true;
        result.innerHTML = "";
      }
      if (terminal) terminal.value = "";
      return Promise.resolve({ ok: false, empty: true });
    }

    if (status) {
      resetFacilityHint(form);
    }
    if (result) {
      result.hidden = true;
      result.innerHTML = "";
    }
    if (terminal) terminal.value = "";
    clearQuoteContactFacilityFields();
    if (continueBtn) continueBtn.disabled = true;
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.classList.add("is-loading");
      var label = submitBtn.querySelector("span:not(.int-btn__spinner)");
      if (label) label.textContent = "Searching…";
      else submitBtn.textContent = "Searching…";
    }

    var url =
      cfg.ajaxUrl +
      "?action=integral_facility_lookup&code=" +
      encodeURIComponent(code) +
      "&type=" +
      encodeURIComponent(type);

    function currentCode() {
      var el = form.querySelector('[name="code"]');
      return el ? String(el.value || "").trim() : "";
    }

    function isStale() {
      return currentCode() !== requestCode;
    }

    return fetch(url, { credentials: "same-origin" })
      .then(function (res) {
        return res.json().then(function (json) {
          return { ok: res.ok, json: json };
        });
      })
      .then(function (payload) {
        if (isStale()) {
          if (!currentCode()) resetFacilityHint(form);
          return { ok: true, stale: true };
        }
        if (payload.json && payload.json.success && payload.json.data) {
          var data = payload.json.data;
          if (status) {
            status.className = "int-facility__hint is-ok";
            status.textContent = "Facility found...";
          }
          if (terminal) {
            terminal.value = data.registryText || ("No facility found for " + code);
          }
          if (result) renderFacility(result, data);
          return { ok: true, found: true };
        }
        var msg =
          (payload.json && payload.json.data && payload.json.data.message) ||
          "No facility found for that identifier.";
        if (status) {
          status.className = "int-facility__hint is-error";
          status.textContent = msg;
        }
        if (terminal) {
          terminal.value = "No facility found for " + code + "\n" + msg;
        }
        return { ok: true, found: false };
      })
      .catch(function () {
        if (isStale()) {
          if (!currentCode()) resetFacilityHint(form);
          return { ok: false, stale: true };
        }
        if (status) {
          status.className = "int-facility__hint is-error";
          status.textContent = "Lookup failed. Try again shortly.";
        }
        if (terminal) terminal.value = "Lookup failed. Try again shortly.";
        return { ok: false, found: false };
      })
      .then(function (outcome) {
        if (continueBtn) continueBtn.disabled = false;
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.classList.remove("is-loading");
          var doneLabel = submitBtn.querySelector("span:not(.int-btn__spinner)");
          if (doneLabel) doneLabel.textContent = "Lookup";
          else submitBtn.textContent = "Lookup";
        }
        if (outcome && outcome.stale && !currentCode()) {
          resetFacilityHint(form);
        }
        return outcome;
      });
  }

  function facilityLookupIfNeeded(form) {
    if (!form) return Promise.resolve({ ok: true, skipped: true });
    var codeEl = form.querySelector('[name="code"]');
    var code = codeEl ? String(codeEl.value || "").trim() : "";
    if (!code) {
      resetFacilityHint(form);
      return Promise.resolve({ ok: true, skipped: true, empty: true });
    }
    // Already have a successful result for this search — skip re-lookup.
    var status = form.querySelector("[data-facility-status]");
    var result = form.querySelector("[data-facility-result]");
    var alreadyFound =
      status &&
      status.classList.contains("is-ok") &&
      result &&
      !result.hidden &&
      result.innerHTML.trim() !== "";
    if (alreadyFound) return Promise.resolve({ ok: true, skipped: true, found: true });
    return runFacilityLookup(form);
  }

  function resetFacilityHint(form) {
    var status = form.querySelector("[data-facility-status]");
    if (!status) return;
    status.className = "int-facility__hint";
    status.textContent = "Lookup by FR Code, FID, Registration Number";
  }

  function setupFacilityForms() {
    document.querySelectorAll("[data-facility-form]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        runFacilityLookup(form);
      });

      var codeInput = form.querySelector('[name="code"]');
      if (codeInput) {
        codeInput.addEventListener("input", function () {
          var value = String(codeInput.value || "").trim();
          var status = form.querySelector("[data-facility-status]");
          var result = form.querySelector("[data-facility-result]");
          var terminal = form.querySelector("[data-facility-terminal]");
          var shouldReset =
            !value ||
            (status &&
              (status.classList.contains("is-ok") ||
                status.classList.contains("is-error") ||
                status.classList.contains("is-loading")));

          if (shouldReset) {
            resetFacilityHint(form);
          }
          if (!value) {
            if (result) {
              result.hidden = true;
              result.innerHTML = "";
            }
            if (terminal) terminal.value = "";
            clearQuoteContactFacilityFields();
          }
        });
      }
    });

    document.querySelectorAll("[data-facility-continue]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var form = btn.closest("[data-facility-form]");
        var codeEl = form ? form.querySelector('[name="code"]') : null;
        var code = codeEl ? String(codeEl.value || "").trim() : "";
        if (form && !code) {
          resetFacilityHint(form);
        }
        facilityLookupIfNeeded(form).then(function () {
          if (window.IntegralDemo && typeof window.IntegralDemo.openContact === "function") {
            window.IntegralDemo.openContact();
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

  function setupCareers() {
    var root = document.querySelector("[data-careers]");
    if (!root) return;

    function setAlert(el, message, isError) {
      if (!el) return;
      el.hidden = !message;
      el.textContent = message || "";
      el.classList.toggle("is-error", !!isError);
      el.classList.toggle("is-ok", !!message && !isError);
    }

    function closePanel(card) {
      var panel = card.querySelector("[data-career-panel]");
      var toggle = card.querySelector("[data-career-toggle]");
      if (panel) panel.hidden = true;
      if (toggle) toggle.setAttribute("aria-expanded", "false");
    }

    root.querySelectorAll("[data-career-toggle]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var card = btn.closest("[data-career-role]");
        if (!card) return;
        var panel = card.querySelector("[data-career-panel]");
        if (!panel) return;
        var open = panel.hidden;
        root.querySelectorAll("[data-career-role]").forEach(closePanel);
        if (open) {
          panel.hidden = false;
          btn.setAttribute("aria-expanded", "true");
          var first = panel.querySelector("input, textarea");
          if (first) first.focus();
        }
      });
    });

    root.querySelectorAll("[data-career-cancel]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var card = btn.closest("[data-career-role]");
        if (card) closePanel(card);
      });
    });

    root.querySelectorAll("[data-career-form]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var alertEl = form.querySelector("[data-career-alert]");
        var submitBtn = form.querySelector("[data-career-submit]");
        var ajaxUrl =
          (window.IntegralTheme && IntegralTheme.ajaxUrl) || "/wp-admin/admin-ajax.php";
        var data = new FormData(form);
        data.append("action", "integral_send_career");

        setAlert(alertEl, "");
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = "Sending…";
        }

        fetch(ajaxUrl, { method: "POST", body: data, credentials: "same-origin" })
          .then(function (res) {
            return res.json().then(function (json) {
              return { ok: res.ok, json: json };
            });
          })
          .then(function (result) {
            var json = result.json || {};
            var msg =
              (json.data && json.data.message) ||
              json.message ||
              (result.ok && json.success
                ? "Application sent."
                : "Could not send application.");
            if (result.ok && json.success) {
              setAlert(alertEl, msg, false);
              form.reset();
            } else {
              setAlert(alertEl, msg, true);
            }
          })
          .catch(function () {
            setAlert(alertEl, "Network error. Please try again.", true);
          })
          .finally(function () {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = "Send application";
            }
          });
      });
    });
  }

  function setupThemeToggle() {
    var root = document.documentElement;
    var buttons = document.querySelectorAll("[data-theme-toggle]");
    if (!buttons.length) return;

    function isLight() {
      return root.classList.contains("int-theme-light");
    }

    function syncButtons() {
      var light = isLight();
      buttons.forEach(function (btn) {
        btn.setAttribute("aria-pressed", light ? "true" : "false");
        btn.setAttribute(
          "aria-label",
          light ? "Switch to dark mode" : "Switch to white mode"
        );
        btn.setAttribute("title", light ? "Dark mode" : "White mode");
        btn.classList.toggle("is-light", light);
      });
    }

    function setTheme(light) {
      root.classList.toggle("int-theme-light", !!light);
      try {
        localStorage.setItem("integral-theme", light ? "light" : "dark");
      } catch (e) {}
      syncButtons();
    }

    buttons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        setTheme(!isLight());
      });
    });

    syncButtons();
  }

  function setupToast() {
    var toast = document.querySelector("[data-toast]");
    if (!toast) {
      return {
        show: function () {},
        hide: function () {},
      };
    }
    var msgEl = toast.querySelector("[data-toast-message]");
    var closeBtn = toast.querySelector("[data-toast-close]");
    var hideTimer = null;
    var leaveTimer = null;

    function hideToast() {
      if (hideTimer) {
        window.clearTimeout(hideTimer);
        hideTimer = null;
      }
      if (leaveTimer) {
        window.clearTimeout(leaveTimer);
        leaveTimer = null;
      }
      toast.classList.remove("is-visible");
      toast.classList.add("is-leaving");
      leaveTimer = window.setTimeout(function () {
        toast.classList.remove("is-leaving");
        toast.hidden = true;
        leaveTimer = null;
      }, 550);
    }

    function showToast(message) {
      if (hideTimer) window.clearTimeout(hideTimer);
      if (leaveTimer) window.clearTimeout(leaveTimer);
      if (msgEl) msgEl.textContent = message || "Done.";
      toast.hidden = false;
      toast.classList.remove("is-leaving");
      requestAnimationFrame(function () {
        toast.classList.add("is-visible");
      });
      hideTimer = window.setTimeout(hideToast, 6500);
    }

    if (closeBtn) closeBtn.addEventListener("click", hideToast);

    return { show: showToast, hide: hideToast };
  }

  function setupDemoModal() {
    var modal = document.querySelector("[data-demo-modal]");
    if (!modal) return;

    var toastApi = setupToast();
    var step = 1;
    var facilityType = "";
    var activeQuote = null;

    function showStep(n) {
      step = n;
      modal.querySelectorAll("[data-demo-step]").forEach(function (el) {
        el.classList.toggle("is-active", Number(el.getAttribute("data-demo-step")) === n);
      });
      var dots = modal.querySelectorAll("[data-demo-progress] span");
      dots.forEach(function (el, i) {
        el.classList.toggle("is-on", i < n);
      });
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

    function openDemo(e, quote, startStep) {
      if (e) e.preventDefault();
      var alertEl = modal.querySelector("[data-demo-alert]");
      if (alertEl) {
        alertEl.hidden = true;
        alertEl.textContent = "";
        alertEl.className = "int-demo__alert";
      }
      if (quote) {
        applyQuote(quote);
        var typeField = modal.querySelector("[data-demo-facility-type-field]");
        if (typeField) typeField.value = quote.modeLabel || quote.title || "";
        // Pricing plan flow skips facility lookup → open on contact (step 2)
        showStep(2);
      } else {
        clearPlan();
        showStep(Number(startStep) === 2 ? 2 : 1);
      }
      modal.hidden = false;
      requestAnimationFrame(function () {
        modal.classList.add("is-open");
      });
      lockScroll();
    }

    function closeDemo() {
      modal.classList.remove("is-open");
      window.setTimeout(function () {
        if (!modal.classList.contains("is-open")) {
          modal.hidden = true;
        }
      }, 400);
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

    modal.querySelectorAll("[data-demo-next]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (step !== 1) return;
        var lookupForm = modal.querySelector("[data-demo-facility-form], [data-facility-form]");
        facilityLookupIfNeeded(lookupForm).then(function () {
          showStep(2);
        });
      });
    });

    modal.querySelectorAll("[data-demo-back]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        // From contact, go back to facility lookup (or stay on contact if opened from pricing)
        if (activeQuote) {
          closeDemo();
          return;
        }
        showStep(1);
      });
    });

    var form = modal.querySelector("[data-demo-form]");
    if (form) {
      var formAlert = form.querySelector("[data-demo-alert]");

      function setFormAlert(type, message) {
        if (!formAlert) return;
        if (!message) {
          formAlert.hidden = true;
          formAlert.textContent = "";
          formAlert.className = "int-demo__alert";
          return;
        }
        formAlert.hidden = false;
        formAlert.className = "int-demo__alert is-" + type;
        formAlert.textContent = message;
      }

      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var data = new FormData(form);
        var submitBtn = form.querySelector("[data-demo-submit]") || form.querySelector('[type="submit"]');

        var payload = new FormData();
        payload.set("action", "integral_send_quotation");
        payload.set("facility", data.get("facility") || "");
        payload.set("fr_code", data.get("fr_code") || "");
        payload.set("facility_type", data.get("facility_type") || "");
        payload.set("facility_level", data.get("facility_level") || "");
        payload.set("facility_snapshot", data.get("facility_snapshot") || "");
        payload.set("email", data.get("email") || "");
        payload.set("phone", data.get("phone") || "");
        payload.set("message", data.get("message") || "");

        setFormAlert("", "");
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.classList.add("is-loading");
          var label = submitBtn.querySelector("span:not(.int-btn__spinner)");
          if (label) label.textContent = "Sending…";
        }

        fetch(cfg.ajaxUrl, { method: "POST", body: payload, credentials: "same-origin" })
          .then(function (res) {
            return res.json().then(function (json) {
              return { ok: res.ok, json: json };
            });
          })
          .then(function (result) {
            if (result.json && result.json.success) {
              var okMsg =
                (result.json.data && result.json.data.message) ||
                "Quotation sent successfully.";
              closeDemo();
              if (toastApi && toastApi.show) toastApi.show(okMsg);
              return;
            }
            var msg =
              (result.json && result.json.data && result.json.data.message) ||
              "Could not send quotation. Check mail/SMTP configuration.";
            setFormAlert("error", msg);
          })
          .catch(function () {
            setFormAlert("error", "Could not send quotation. Network or mail server error.");
          })
          .then(function () {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.classList.remove("is-loading");
              var doneLabel = submitBtn.querySelector("span:not(.int-btn__spinner)");
              if (doneLabel) doneLabel.textContent = "Submit";
            }
          });
      });
    }

    window.IntegralDemo = {
      open: function (quote) {
        openDemo(null, quote || null);
      },
      openContact: function () {
        openDemo(null, null, 2);
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
    setupCareers();
    setupThemeToggle();
  });

  window.addEventListener("scroll", onScroll, { passive: true });
})();
