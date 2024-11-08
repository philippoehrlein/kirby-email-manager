(function() {
  "use strict";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    {
      options._scopeId = "data-v-" + scopeId;
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main = {
    computed: {
      allRequiredFieldsSet() {
        const is_single_email = !this.content.send_to_more;
        let all_fields_set = false;
        if (is_single_email) {
          all_fields_set = this.content.email_templates && this.content.send_to && this.content.send_to_success_title && this.content.send_to_success_text;
        } else {
          all_fields_set = this.content.email_templates && this.content.send_to_structure && this.content.send_to_structure.length > 0;
        }
        if (this.content.gdpr_checkbox && !this.content.gdpr_text) {
          all_fields_set = false;
        }
        return all_fields_set;
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", [_c("header", { staticClass: "email-manager-header" }, [_c("h1", [_c("svg", { staticClass: "k-icon", attrs: { "aria-hidden": "true", "data-type": "email-manager" } }, [_c("use", { attrs: { "xlink:href": "#icon-email-manager" } })]), _vm._v("Email Manager")])]), _vm.allRequiredFieldsSet ? _c("div", { staticClass: "email-status-list" }, [_c("k-box", { attrs: { "theme": "positive", "icon": "check" } }, [_c("p", [_vm._v(_vm._s(_vm.$t("panel.status.all_fields_set")))])])], 1) : _c("div", { staticClass: "email-status-list" }, [_c("k-box", { attrs: { "theme": "error", "icon": "alert" } }, [_c("p", [_vm._v(_vm._s(_vm.$t("panel.status.not_all_fields_set")))])])], 1)]);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    "e4a2bcbe"
  );
  __component__.options.__file = "/Users/philipp/Documents/02_Offen/Kirby Plugins/kirby-email-manager/site/plugins/kirby-email-manager/src/Components/EmailManagerBlock.vue";
  const EmailManagerBlock = __component__.exports;
  window.panel.plugin("philippoehrlein/kirby-email-manager", {
    icons: {
      "email-manager": '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 7.23792L12.0718 14.338L4 7.21594V19H14V21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H21C21.5523 3 22 3.44772 22 4V13H20V7.23792ZM19.501 5H4.51146L12.0619 11.662L19.501 5ZM17.05 19.5485C17.0172 19.3706 17 19.1873 17 19C17 18.8127 17.0172 18.6294 17.05 18.4515L16.0359 17.866L17.0359 16.134L18.0505 16.7197C18.3278 16.4824 18.6489 16.2948 19 16.1707V15H21V16.1707C21.3511 16.2948 21.6722 16.4824 21.9495 16.7197L22.9641 16.134L23.9641 17.866L22.95 18.4515C22.9828 18.6294 23 18.8127 23 19C23 19.1873 22.9828 19.3706 22.95 19.5485L23.9641 20.134L22.9641 21.866L21.9495 21.2803C21.6722 21.5176 21.3511 21.7052 21 21.8293V23H19V21.8293C18.6489 21.7052 18.3278 21.5176 18.0505 21.2803L17.0359 21.866L16.0359 20.134L17.05 19.5485ZM20 20C20.5523 20 21 19.5523 21 19C21 18.4477 20.5523 18 20 18C19.4477 18 19 18.4477 19 19C19 19.5523 19.4477 20 20 20Z"></path></svg>'
    },
    blocks: {
      "email-manager": EmailManagerBlock
    }
  });
})();
