"use strict";

var _typeof9 = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _typeof8 = "function" == typeof Symbol && "symbol" == _typeof9(Symbol.iterator) ? function (t) {
  return typeof t === "undefined" ? "undefined" : _typeof9(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t === "undefined" ? "undefined" : _typeof9(t);
},
    _typeof7 = "function" == typeof Symbol && "symbol" == _typeof8(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof8(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof8(t);
},
    _typeof6 = "function" == typeof Symbol && "symbol" === _typeof7(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof7(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof7(t);
},
    _typeof5 = "function" == typeof Symbol && "symbol" === _typeof6(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof6(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof6(t);
},
    _typeof4 = "function" == typeof Symbol && "symbol" === _typeof5(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof5(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof5(t);
},
    _typeof3 = "function" == typeof Symbol && "symbol" === _typeof4(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof4(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof4(t);
},
    _typeof2 = "function" == typeof Symbol && "symbol" === _typeof3(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof3(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof3(t);
},
    _typeof = "function" == typeof Symbol && "symbol" === _typeof2(Symbol.iterator) ? function (t) {
  return void 0 === t ? "undefined" : _typeof2(t);
} : function (t) {
  return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : void 0 === t ? "undefined" : _typeof2(t);
};!function (t) {
  function e(t) {
    for (var n = 0; n < t.length; n++) {
      "object" === _typeof(t[n].children) ? (e(t[n].children), void 0 !== t[n].component && (t[n].component = weForms.routeComponents[t[n].component])) : t[n].component = weForms.routeComponents[t[n].component];
    }
  }Vue.component("wpuf-table", { template: "#tmpl-wpuf-component-table", mixins: [weForms.mixins.Loading, weForms.mixins.Paginate, weForms.mixins.BulkAction], props: { has_export: String, action: String, delete: String, id: [String, Number], status: [String] }, data: function data() {
      return { loading: !1, columns: [], items: [], ajaxAction: this.action, nonce: weForms.nonce, index: "id", bulkDeleteAction: this.delete ? this.delete : "weforms_form_entry_trash_bulk" };
    }, created: function created() {
      this.fetchData();
    }, computed: { columnLength: function columnLength() {
        return Object.keys(this.columns).length;
      } }, methods: { fetchData: function fetchData() {
        var t = this;this.loading = !0, wp.ajax.send(t.action, { data: { id: t.id, page: t.currentPage, status: t.status, _wpnonce: weForms.nonce }, success: function success(e) {
            t.loading = !1, t.columns = e.columns, t.items = e.entries, t.form_title = e.form_title, t.totalItems = e.pagination.total, t.totalPage = e.pagination.pages, t.$emit("ajaxsuccess", e);
          }, error: function error(e) {
            t.loading = !1, alert(e);
          } });
      }, handleBulkAction: function handleBulkAction() {
        if ("-1" !== this.bulkAction) {
          if ("delete" === this.bulkAction) {
            if (!this.checkedItems.length) return void alert("Please select atleast one entry to delete.");confirm("Are you sure to delete the entries?") && this.deleteBulk();
          }if ("restore" === this.bulkAction) {
            if (!this.checkedItems.length) return void alert("Please select atleast one entry to restore.");this.restoreBulk();
          }
        } else alert("Please chose a bulk action to perform");
      }, restore: function restore(t) {
        var e = this;e.loading = !0, wp.ajax.send("weforms_form_entry_restore", { data: { entry_id: t, _wpnonce: weForms.nonce }, success: function success(t) {
            e.loading = !1, e.fetchData();
          }, error: function error(t) {
            e.loading = !1, alert(t);
          } });
      }, deletePermanently: function deletePermanently(t) {
        if (confirm("Are you sure to delete this entry?")) {
          var e = this;e.loading = !0, wp.ajax.send("weforms_form_entry_delete", { data: { entry_id: t, _wpnonce: weForms.nonce }, success: function success(t) {
              e.loading = !1, e.fetchData();
            }, error: function error(t) {
              e.loading = !1, alert(t);
            } });
        }
      } }, watch: { id: function id() {
        this.fetchData();
      }, status: function status() {
        this.currentPage = 1, this.bulkAction = -1, this.fetchData();
      } } }), weForms.routeComponents.Entries = { template: "#tmpl-wpuf-entries", data: function data() {
      return { selected: 0, forms: {}, form_title: "Loading...", status: "publish", total: 0, totalTrash: 0 };
    }, created: function created() {
      this.get_forms();
    }, methods: { get_forms: function get_forms() {
        var t = this;wp.ajax.send("weforms_form_list", { data: { _wpnonce: weForms.nonce, page: t.currentPage, posts_per_page: -1, filter: "entries" }, success: function success(e) {
            Object.keys(e.forms).length ? (t.forms = e.forms, t.selected = t.forms[Object.keys(t.forms)[0]].id) : t.form_title = "No entry found";
          }, error: function error(t) {
            alert(t);
          } });
      } } }, weForms.routeComponents.FormEditComponent = { template: "#tmpl-wpuf-form-builder", mixins: wpuf_form_builder_mixins(wpuf_mixins.root), data: function data() {
      return { is_form_saving: !1, is_form_saved: !1, is_form_switcher: !1, post_title_editing: !1, loading: !1, activeTab: "editor", activeSettingsTab: "form", activePaymentTab: "paypal" };
    }, watch: { loading: function loading(t) {
        t ? (NProgress.configure({ parent: "#wpadminbar" }), NProgress.start()) : NProgress.done();
      }, form_fields: { handler: function handler() {
          window.weFormsBuilderisDirty = !0;
        }, deep: !0 }, notifications: { handler: function handler() {
          window.weFormsBuilderisDirty = !0;
        }, deep: !0 }, integrations: { handler: function handler() {
          window.weFormsBuilderisDirty = !0;
        }, deep: !0 }, settings: { handler: function handler() {
          window.weFormsBuilderisDirty = !0;
        }, deep: !0 }, payment: { handler: function handler() {
          window.weFormsBuilderisDirty = !0;
        }, deep: !0 } }, created: function created() {
      this.set_current_panel("form-fields"), this.fetchForm(), this.$store.commit("panel_add_show_prop"), wpuf_form_builder.event_hub = new Vue();
    }, computed: { current_panel: function current_panel() {
        return this.$store.state.current_panel;
      }, post: function post() {
        return this.$store.state.post;
      }, form_fields_count: function form_fields_count() {
        return this.$store.state.form_fields.length;
      }, form_fields: function form_fields() {
        return this.$store.state.form_fields;
      }, notifications: function notifications() {
        return this.$store.state.notifications;
      }, integrations: function integrations() {
        return this.$store.state.integrations;
      }, settings: function settings() {
        return this.$store.state.settings;
      }, payment: function payment() {
        return this.$store.state.payment;
      } }, mounted: function mounted() {
      var e = new window.Clipboard(".form-id");t(".form-id").tooltip();var n = this;this.started = !0, e.on("success", function (e) {
        t(e.trigger).attr("data-original-title", "Copied!").tooltip("show"), setTimeout(function () {
          t(e.trigger).tooltip("hide").attr("data-original-title", n.i18n.copy_shortcode);
        }, 1e3), e.clearSelection();
      }), this.initSharingClipBoard(), setTimeout(function () {
        window.weFormsBuilderisDirty = !1;
      }, 500), window.onbeforeunload = function () {
        if (window.weFormsBuilderisDirty) return n.i18n.unsaved_changes;
      };
    }, methods: { makeActive: function makeActive(t) {
        this.activeTab = t;
      }, isActiveTab: function isActiveTab(t) {
        return this.activeTab === t;
      }, isActiveSettingsTab: function isActiveSettingsTab(t) {
        return this.activeSettingsTab === t;
      }, makeActiveSettingsTab: function makeActiveSettingsTab(t) {
        this.activeSettingsTab = t;
      }, isActivePaymentTab: function isActivePaymentTab(t) {
        return this.activePaymentTab === t;
      }, makeActivePaymentTab: function makeActivePaymentTab(t) {
        this.activePaymentTab = t;
      }, fetchForm: function fetchForm() {
        var t = this;t.loading = !0, wp.ajax.send("weforms_get_form", { data: { form_id: this.$route.params.id, _wpnonce: weForms.nonce }, success: function success(e) {
            t.$store.commit("set_form_post", e.post), t.$store.commit("set_form_fields", e.form_fields), t.$store.commit("set_form_notification", e.notifications), t.$store.commit("set_form_settings", e.settings), void 0 !== e.integrations.length ? t.$store.commit("set_form_integrations", {}) : t.$store.commit("set_form_integrations", e.integrations);
          }, error: function error(t) {
            alert(t);
          }, complete: function complete() {
            t.loading = !1;
          } });
      }, set_current_panel: function set_current_panel(t) {
        this.$store.commit("set_current_panel", t);
      }, save_form_builder: function save_form_builder() {
        var e = this;!_.isFunction(this.validate_form_before_submit) || this.validate_form_before_submit() ? (e.is_form_saving = !0, e.set_current_panel("form-fields"), wp.ajax.send("wpuf_form_builder_save_form", { data: { form_data: t("#wpuf-form-builder").serialize(), form_fields: JSON.stringify(e.form_fields), notifications: JSON.stringify(e.notifications), settings: JSON.stringify(e.settings), payment: JSON.stringify(e.payment), integrations: JSON.stringify(e.integrations) }, success: function success(t) {
            t.form_fields && e.$store.commit("set_form_fields", t.form_fields), e.is_form_saving = !1, e.is_form_saved = !0, setTimeout(function () {
              window.weFormsBuilderisDirty = !1;
            }, 500), toastr.success(e.i18n.saved_form_data);
          }, error: function error() {
            e.is_form_saving = !1;
          } })) : this.warn({ text: this.validation_error_msg });
      }, save_settings: function save_settings() {
        toastr.options.preventDuplicates = !0, this.save_form_builder();
      }, shareForm: function shareForm(t, e) {
        var n = this;if ("on" === n.settings.sharing_on) {
          var o = t + "?weforms=" + btoa(n.getSharingHash() + "_" + Math.floor(Date.now() / 1e3) + "_" + e.ID);swal({ title: n.i18n.shareYourForm, html: "<p>" + n.i18n.shareYourFormText + '</p> <p><input onClick="this.setSelectionRange(0, this.value.length)" type="text" class="regular-text" value="' + o + '"/> <button class="anonymous-share-btn button button-primary" title="Copy URL" data-clipboard-text="' + o + '"><i class="fa fa-clipboard" aria-hidden="true"></i></button></p>', showCloseButton: !0, showCancelButton: !0, confirmButtonClass: "btn btn-success", cancelButtonClass: "btn btn-danger", confirmButtonColor: "#d54e21", confirmButtonText: n.i18n.disableSharing, cancelButtonText: n.i18n.close, focusCancel: !0 }).then(function () {
            swal({ title: n.i18n.areYouSure, html: "<p>" + n.i18n.areYouSureDesc + "</p>", type: "info", confirmButtonColor: "#d54e21", showCancelButton: !0, confirmButtonText: n.i18n.disable, cancelButtonText: n.i18n.cancel }).then(function () {
              n.disableSharing();
            });
          });
        } else swal({ title: n.i18n.shareYourForm, html: n.i18n.shareYourFormDesc, type: "info", showCancelButton: !0, confirmButtonText: "Enable", cancelButtonText: "Cancel" }).then(function () {
          n.enableSharing(t, e);
        });
      }, enableSharing: function enableSharing(t, e) {
        this.settings.sharing_on = "on", this.save_settings(), this.shareForm(t, e);
      }, disableSharing: function disableSharing() {
        this.settings.sharing_on = !1, this.save_settings();
      }, getSharingHash: function getSharingHash() {
        return this.settings.sharing_hash || (this.settings.sharing_hash = this.makeRandomString(8), this.save_settings()), this.settings.sharing_hash;
      }, makeRandomString: function makeRandomString(t) {
        t = t || 8;for (var e = "", n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", o = 0; o < t; o++) {
          e += n.charAt(Math.floor(Math.random() * n.length));
        }return e;
      }, initSharingClipBoard: function initSharingClipBoard(e) {
        var n = new window.Clipboard(".anonymous-share-btn");t(".anonymous-share-btn").tooltip(), n.on("success", function (e) {
          t(e.trigger).attr("data-original-title", "Copied!").tooltip("show"), setTimeout(function () {
            t(e.trigger).tooltip("hide").attr("data-original-title", "Copy URL");
          }, 1e3), e.clearSelection();
        });
      } } }, weForms.routeComponents.FormEntries = { props: { id: [String, Number] }, template: "#tmpl-wpuf-form-entries", data: function data() {
      return { selected: 0, form_title: "Loading...", status: "publish", total: 0, totalTrash: 0 };
    } }, weForms.routeComponents.FormEntriesSingle = { template: "#tmpl-wpuf-form-entry-single", mixins: [weForms.mixins.Loading, weForms.mixins.Cookie], data: function data() {
      return { loading: !1, hideEmpty: !0, hasEmpty: !1, show_payment_data: !1, entry: { form_fields: {}, meta_data: {}, payment_data: {} } };
    }, created: function created() {
      this.hideEmpty = this.hideEmptyStatus(), this.fetchData();
    }, computed: { hasFormFields: function hasFormFields() {
        return Object.keys(this.entry.form_fields).length;
      } }, methods: { fetchData: function fetchData() {
        var t = this;this.loading = !0, wp.ajax.send("weforms_form_entry_details", { data: { entry_id: t.$route.params.entryid, form_id: t.$route.params.id, _wpnonce: weForms.nonce }, success: function success(e) {
            t.loading = !1, t.entry = e, t.hasEmpty = e.has_empty;
          }, error: function error(e) {
            t.loading = !1, alert(e);
          } });
      }, trashEntry: function trashEntry() {
        var t = this;confirm(weForms.confirm) && wp.ajax.send("weforms_form_entry_trash", { data: { entry_id: t.$route.params.entryid, _wpnonce: weForms.nonce }, success: function success() {
            t.loading = !1, t.$router.push({ name: "formEntries", params: { id: t.$route.params.id } });
          }, error: function error(e) {
            t.loading = !1, alert(e);
          } });
      }, hideEmptyStatus: function hideEmptyStatus() {
        return "false" !== this.getCookie("weFormsEntryHideEmpty");
      } }, watch: { hideEmpty: function hideEmpty(t) {
        this.setCookie("weFormsEntryHideEmpty", t, 356);
      } } }, Vue.component("form-list-table", { template: "#tmpl-wpuf-form-list-table", mixins: [weForms.mixins.Loading, weForms.mixins.Paginate, weForms.mixins.BulkAction], data: function data() {
      return { loading: !1, index: "ID", items: [], bulkDeleteAction: "weforms_form_delete_bulk" };
    }, created: function created() {
      this.fetchData();
    }, computed: { is_pro: function is_pro() {
        return "true" === weForms.is_pro;
      }, has_payment: function has_payment() {
        return "true" === weForms.has_payment;
      } }, methods: { fetchData: function fetchData() {
        var t = this;this.loading = !0, wp.ajax.send("weforms_form_list", { data: { _wpnonce: weForms.nonce, page: t.currentPage }, success: function success(e) {
            t.loading = !1, t.items = e.forms, t.totalItems = e.meta.total, t.totalPage = e.meta.pages;
          }, error: function error(e) {
            t.loading = !1, alert(e);
          } });
      }, deleteForm: function deleteForm(t) {
        var e = this;confirm("Are you sure?") && (e.loading = !0, wp.ajax.send("weforms_form_delete", { data: { form_id: this.items[t].id, _wpnonce: weForms.nonce }, success: function success(n) {
            e.items.splice(t, 1), e.loading = !1;
          }, error: function error(t) {
            alert(t), e.loading = !1;
          } }));
      }, duplicate: function duplicate(t, e) {
        var n = this;this.loading = !0, wp.ajax.send("weforms_form_duplicate", { data: { form_id: t, _wpnonce: weForms.nonce }, success: function success(t) {
            n.items.splice(0, 0, t), n.loading = !1;
          }, error: function error(t) {
            alert(t), n.loading = !1;
          } });
      }, handleBulkAction: function handleBulkAction() {
        if ("-1" !== this.bulkAction) {
          if ("delete" === this.bulkAction) {
            if (!this.checkedItems.length) return void alert("Please select atleast one form to delete.");confirm("Are you sure to delete the forms?") && this.deleteBulk();
          }
        } else alert("Please chose a bulk action to perform");
      } } }), weForms.routeComponents.FormPayments = { props: { id: [String, Number] }, template: "#tmpl-wpuf-form-payments", data: function data() {
      return { form_title: "Loading..." };
    } }, weForms.routeComponents.Home = { template: "#tmpl-wpuf-home-page", data: function data() {
      return { showTemplateModal: !1 };
    }, methods: { displayModal: function displayModal() {
        this.showTemplateModal = !0;
      }, closeModal: function closeModal() {
        this.showTemplateModal = !1;
      } } }, weForms.routeComponents.Tools = { template: "#tmpl-wpuf-tools", mixins: [weForms.mixins.Tabs, weForms.mixins.Loading], data: function data() {
      return { activeTab: "export", exportType: "all", loading: !1, forms: [], importButton: "Import", currentStatus: 0, responseMessage: "", logs: [], ximport: { current: "", title: "", action: "", message: "", type: "updated", refs: {} } };
    }, computed: { isInitial: function isInitial() {
        return 0 === this.currentStatus;
      }, isSaving: function isSaving() {
        return 1 === this.currentStatus;
      }, isSuccess: function isSuccess() {
        return 2 === this.currentStatus;
      }, isFailed: function isFailed() {
        return 3 === this.currentStatus;
      }, hasRefs: function hasRefs() {
        return Object.keys(this.ximport.refs).length;
      }, hasLogs: function hasLogs() {
        return Object.keys(this.logs).length;
      } }, created: function created() {
      this.fetchData(), this.fetchLogs();
    }, methods: { fetchLogs: function fetchLogs(t) {
        var e = this;e.startLoading(t), wp.ajax.send("weforms_read_logs", { data: { _wpnonce: weForms.nonce }, success: function success(n) {
            e.stopLoading(t), e.logs = n;
          }, error: function error() {
            e.stopLoading(t), e.logs = [];
          } });
      }, deleteLogs: function deleteLogs(t) {
        var e = this;confirm("Are you sure to clear the log file?") && (e.startLoading(t), wp.ajax.send("weforms_delete_logs", { data: { _wpnonce: weForms.nonce }, success: function success(n) {
            e.logs = [], e.stopLoading(t), e.fetchLogs();
          }, error: function error(n) {
            e.logs = [], e.stopLoading(t), e.fetchLogs();
          } }));
      }, stopLoading: function stopLoading(e) {
        (e = t(e)).is("button") ? e.removeClass("updating-message").find("span").show() : e.is("span") && e.show().parent().removeClass("updating-message");
      }, startLoading: function startLoading(e) {
        (e = t(e)).is("button") ? e.addClass("updating-message").find("span").hide() : e.is("span") && e.hide().parent().addClass("updating-message");
      }, fetchData: function fetchData() {
        var t = this;this.loading = !0, wp.ajax.send("weforms_form_names", { data: { _wpnonce: weForms.nonce }, success: function success(e) {
            t.loading = !1, t.forms = e;
          }, error: function error(e) {
            t.loading = !1, alert(e);
          } });
      }, importForm: function importForm(e, n, o) {
        if (n.length) {
          var i = new FormData(),
              s = this;i.append(e, n[0], n[0].name), i.append("action", "weforms_import_form"), i.append("_wpnonce", weForms.nonce), s.currentStatus = 1, t.ajax({ type: "POST", url: window.ajaxurl, data: i, processData: !1, contentType: !1, success: function success(e) {
              s.responseMessage = e.data, e.success ? s.currentStatus = 2 : s.currentStatus = 3, t(o.target).val("");
            }, error: function error(t) {
              console.log(t), s.currentStatus = 3;
            }, complete: function complete() {
              t(o.target).val("");
            } });
        }
      }, importx: function importx(e, n) {
        var o = t(e),
            i = this;i.ximport.current = n, o.addClass("updating-message").text(o.data("importing")), wp.ajax.send("weforms_import_xforms_" + n, { data: { _wpnonce: weForms.nonce }, success: function success(t) {
            i.ximport.title = t.title, i.ximport.message = t.message, i.ximport.action = t.action, i.ximport.refs = t.refs;
          }, error: function error(t) {
            alert(t.message);
          }, complete: function complete() {
            o.removeClass("updating-message").text(o.data("original"));
          } });
      }, replaceX: function replaceX(e, n) {
        var o = t(e),
            i = this;o.addClass("updating-message"), wp.ajax.send("weforms_import_xreplace_" + i.ximport.current, { data: { type: n, _wpnonce: weForms.nonce }, success: function success(t) {
            "replace" === o.data("type") && alert(t);
          }, error: function error(t) {
            alert(t);
          }, complete: function complete() {
            i.ximport.current = "", i.ximport.title = "";
          } });
      } } }, weForms.routeComponents.Transactions = { template: "#tmpl-wpuf-transactions", data: function data() {
      return { selected: 0, no_transactions: !1, forms: {}, form_title: "Loading..." };
    }, created: function created() {
      this.get_forms();
    }, methods: { get_forms: function get_forms() {
        var t = this;wp.ajax.send("weforms_form_list", { data: { _wpnonce: weForms.nonce, page: t.currentPage, filter: "transactions" }, success: function success(e) {
            Object.keys(e.forms).length ? (t.forms = e.forms, t.selected = t.forms[Object.keys(t.forms)[0]].id) : (t.form_title = "No transaction found", t.no_transactions = !0);
          }, error: function error(t) {
            alert(t);
          } });
      } } }, weForms.routeComponents.Help = { template: "#tmpl-wpuf-weforms-page-help" }, weForms.routeComponents.Premium = { template: "#tmpl-wpuf-weforms-premium" }, weForms.routeComponents.Settings = { template: "#tmpl-wpuf-weforms-settings", mixins: [weForms.mixins.Loading, weForms.mixins.Cookie], data: function data() {
      return { loading: !1, settings: { email_gateway: "wordpress", credit: !1, permission: "manage_options", gateways: { sendgrid: "", mailgun: "", sparkpost: "" }, recaptcha: { type: "v2", key: "", secret: "" } }, activeTab: "general" };
    }, computed: { is_pro: function is_pro() {
        return "true" === weForms.is_pro;
      } }, created: function created() {
      this.fetchSettings(), this.getCookie("weforms_settings_active_tab") && (this.activeTab = this.getCookie("weforms_settings_active_tab"));
    }, methods: { makeActive: function makeActive(t) {
        this.activeTab = t;
      }, isActiveTab: function isActiveTab(t) {
        return this.activeTab === t;
      }, fetchSettings: function fetchSettings() {
        var e = this;e.loading = !0, wp.ajax.send("weforms_get_settings", { data: { _wpnonce: weForms.nonce }, success: function success(n) {
            void 0 !== n && (t.each(e.settings, function (t, e) {
              void 0 === n[t] && (n[t] = e);
            }), e.settings = n);
          }, complete: function complete() {
            e.loading = !1;
          } });
      }, saveSettings: function saveSettings(e) {
        t(e).addClass("updating-message"), wp.ajax.send("weforms_save_settings", { data: { settings: JSON.stringify(this.settings), _wpnonce: weForms.nonce }, success: function success(t) {
            toastr.options.timeOut = 1e3, toastr.success("Settings has been updated");
          }, error: function error(t) {
            console.log(t);
          }, complete: function complete() {
            t(e).removeClass("updating-message");
          } });
      }, post: function post(t, e, n) {
        e = e || {}, n = n || function () {}, e._wpnonce = weForms.nonce, wp.ajax.send(t, { data: e, success: function success(t) {
            n(t);
          }, error: function error(t) {
            console.log(t);
          }, complete: function complete() {} });
      } }, watch: { activeTab: function activeTab(t) {
        this.setCookie("weforms_settings_active_tab", t, "365");
      } } }, Array.prototype.hasOwnProperty("swap") || (Array.prototype.swap = function (t, e) {
    this.splice(e, 0, this.splice(t, 1)[0]);
  }), Vue.component("datepicker", { template: '<input type="text" v-bind:value="value" />', props: ["value"], mounted: function mounted() {
      t(this.$el).datetimepicker({ dateFormat: "yy-mm-dd", timeFormat: "HH:mm:ss", onClose: this.onClose });
    }, methods: { onClose: function onClose(t) {
        this.$emit("input", t);
      } } }), Vue.component("weforms-colorpicker", { template: '<input type="text" v-bind:value="value" />', props: ["value"], mounted: function mounted() {
      t(this.$el).wpColorPicker({ change: this.onChange });
    }, methods: { onChange: function onChange(t, e) {
        this.$emit("input", e.color.toString());
      } } });var n = new Vuex.Store({ state: { post: {}, form_fields: [], panel_sections: wpuf_form_builder.panel_sections, field_settings: wpuf_form_builder.field_settings, notifications: [], settings: {}, integrations: {}, current_panel: "form-fields", editing_field_id: 0 }, mutations: { set_form_fields: function set_form_fields(t, e) {
        Vue.set(t, "form_fields", e);
      }, set_form_post: function set_form_post(t, e) {
        Vue.set(t, "post", e);
      }, set_form_notification: function set_form_notification(t, e) {
        Vue.set(t, "notifications", e);
      }, set_form_integrations: function set_form_integrations(t, e) {
        Vue.set(t, "integrations", e);
      }, set_form_settings: function set_form_settings(t, e) {
        Vue.set(t, "settings", e);
      }, set_current_panel: function set_current_panel(t, e) {
        "field-options" !== t.current_panel && "field-options" === e && t.form_fields.length && (t.editing_field_id = t.form_fields[0].id), t.current_panel = e, "form-fields" === e && (t.editing_field_id = 0);
      }, panel_add_show_prop: function panel_add_show_prop(t) {
        t.panel_sections.map(function (e, n) {
          e.hasOwnProperty("show") || Vue.set(t.panel_sections[n], "show", !0);
        });
      }, panel_toggle: function panel_toggle(t, e) {
        t.panel_sections[e].show = !t.panel_sections[e].show;
      }, open_field_settings: function open_field_settings(t, e) {
        var n = t.form_fields.filter(function (t) {
          return parseInt(e) === parseInt(t.id);
        });"field-options" === t.current_panel && n[0].id === t.editing_field_id || n.length && (t.editing_field_id = 0, t.current_panel = "field-options", setTimeout(function () {
          t.editing_field_id = n[0].id;
        }, 400));
      }, update_editing_form_field: function update_editing_form_field(t, e) {
        _.find(t.form_fields, function (t) {
          return parseInt(t.id) === parseInt(e.editing_field_id);
        })[e.field_name] = e.value;
      }, add_form_field_element: function add_form_field_element(e, n) {
        e.form_fields.splice(n.toIndex, 0, n.field), Vue.nextTick(function () {
          var e = t("#form-preview-stage .wpuf-form .field-items").eq(n.toIndex);e && !function (t) {
            "function" == typeof jQuery && t instanceof jQuery && (t = t[0]);var e = t.getBoundingClientRect();return e.top >= 0 && e.left >= 0 && e.bottom <= (window.innerHeight || document.documentElement.clientHeight) && e.right <= (window.innerWidth || document.documentElement.clientWidth);
          }(e.get(0)) && t("#builder-stage section").scrollTo(e, 800, { offset: -50 });
        });
      }, swap_form_field_elements: function swap_form_field_elements(t, e) {
        t.form_fields.swap(e.fromIndex, e.toIndex);
      }, clone_form_field_element: function clone_form_field_element(e, n) {
        var o = _.find(e.form_fields, function (t) {
          return parseInt(t.id) === parseInt(n.field_id);
        }),
            i = t.extend(!0, {}, o),
            s = parseInt(n.index) + 1;i.id = n.new_id, i.name = i.name + "_copy", i.is_new = !0, e.form_fields.splice(s, 0, i);
      }, delete_form_field_element: function delete_form_field_element(t, e) {
        t.current_panel = "form-fields", t.form_fields.splice(e, 1);
      }, set_panel_section_fields: function set_panel_section_fields(t, e) {
        _.find(t.panel_sections, function (t) {
          return t.id === e.id;
        }).fields = e.fields;
      }, addNotification: function addNotification(t, e) {
        t.notifications.push(_.clone(e));
      }, deleteNotification: function deleteNotification(t, e) {
        t.notifications.splice(e, 1);
      }, cloneNotification: function cloneNotification(e, n) {
        var o = t.extend(!0, {}, e.notifications[n]);n = parseInt(n) + 1, e.notifications.splice(n, 0, o);
      }, updateNotificationProperty: function updateNotificationProperty(t, e) {
        t.notifications[e.index][e.property] = e.value;
      }, updateNotification: function updateNotification(t, e) {
        t.notifications[e.index] = e.value;
      }, updateIntegration: function updateIntegration(t, e) {
        Vue.set(t.integrations, e.index, e.value);
      } } });weForms.routeComponents.FormHome = { template: '<div><router-view class="child"></router-view></div>' }, weForms.routeComponents.SingleForm = { template: "#tmpl-wpuf-form-editor" }, weForms.routeComponents.FormEntriesHome = { template: '<div><router-view class="grand-child"></router-view></div>' }, e(weForms.routes);var o = new VueRouter({ routes: weForms.routes, scrollBehavior: function scrollBehavior(t, e, n) {
      return n || { x: 0, y: 0 };
    } });window.weFormsBuilderisDirty = !1, o.beforeEach(function (t, e, n) {
    if (window.weFormsBuilderisDirty) {
      if (!confirm(wpuf_form_builder.i18n.unsaved_changes + " " + wpuf_form_builder.i18n.areYouSureToLeave)) return n(e.path), !1;window.weFormsBuilderisDirty = !1;
    }n();
  }), new Vue({ router: o, store: n }).$mount("#wpuf-contact-form-app");var i = t("#toplevel_page_weforms");i.on("click", "a", function () {
    var e = t(this);t("ul.wp-submenu li", i).removeClass("current"), e.hasClass("wp-has-submenu") ? t("li.wp-first-item", i).addClass("current") : e.parents("li").addClass("current");
  }), t(function () {
    var e = window.location.href,
        n = e.substr(e.indexOf("admin.php"));t("ul.wp-submenu a", i).each(function (e, o) {
      t(o).attr("href") !== n || t(o).parent().addClass("current");
    });
  });
}(jQuery);
