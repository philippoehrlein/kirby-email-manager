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
      "email-manager": '<path fill-rule="evenodd" clip-rule="evenodd" d="M12 0L0 6V18L12 24L24 18V6L12 0ZM12.0002 2.73176L2.73187 7.3659V16.6342L12.0002 21.2683L21.2685 16.6342V7.3659L12.0002 2.73176ZM12.0002 4.80421L5.94995 7.82932L12.0002 10.8544L18.0504 7.82932L12.0002 4.80421ZM19.4148 15.4886V9.21956L12.0002 12.9269L4.58553 9.21956V15.4886L12.0002 19.1959L19.4148 15.4886Z" fill="currentColor"/>'
    },
    blocks: {
      "email-manager": EmailManagerBlock
    }
  });
})();
