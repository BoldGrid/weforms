"use strict";
!function (t) {
  Vue.component("field-dynamic-field", { mixins: [wpuf_mixins.option_field_mixin], template: "#tmpl-wpuf-dynamic-field", data: function data() {
      return { dynamic: { status: !1, param_name: "" } };
    }, computed: { dynamic: function dynamic() {
        return this.editing_form_field.dynamic;
      }, editing_field: function editing_field() {
        return this.editing_form_field;
      } }, created: function created() {
      this.dynamic = t.extend(!1, this.dynamic, this.editing_form_field.dynamic);
    }, methods: {}, watch: { dynamic: function dynamic() {
        this.update_value("dynamic", this.dynamic);
      } } }), Vue.component("field-name", { template: "#tmpl-wpuf-field-name", mixins: [wpuf_mixins.option_field_mixin], computed: { value: { get: function get() {
          return this.editing_form_field[this.option_field.name];
        }, set: function set(t) {
          this.update_value(this.option_field.name, t);
        } } }, methods: { on_focusout: function on_focusout(t) {
        wpuf_form_builder.event_hub.$emit("field-text-focusout", t, this);
      }, on_keyup: function on_keyup(t) {
        wpuf_form_builder.event_hub.$emit("field-text-keyup", t, this);
      }, insertValue: function insertValue(t, e, i) {
        var n = void 0 !== e ? "{" + t + ":" + e + "}" : "{" + t + "}";this.editing_form_field[i.name][i.type] = n;
      } } }), Vue.component("field-text-with-tag", { template: "#tmpl-wpuf-field-text-with-tag", mixins: [wpuf_mixins.option_field_mixin], computed: { value: { get: function get() {
          return this.editing_form_field[this.option_field.name];
        }, set: function set(t) {
          this.update_value(this.option_field.name, t);
        } } }, methods: { on_focusout: function on_focusout(t) {
        wpuf_form_builder.event_hub.$emit("field-text-focusout", t, this);
      }, on_keyup: function on_keyup(t) {
        wpuf_form_builder.event_hub.$emit("field-text-keyup", t, this);
      }, insertValue: function insertValue(t, e, i) {
        var n = void 0 !== e ? "{" + t + ":" + e + "}" : "{" + t + "}";this.value = n;
      } } }), Vue.component("form-date_field", { template: "#tmpl-wpuf-form-date_field", mixins: [wpuf_mixins.form_field_mixin] }), Vue.component("form-name_field", { template: "#tmpl-wpuf-form-name_field", mixins: [wpuf_mixins.form_field_mixin] }), Vue.component("wpuf-cf-form-notification", { template: "#tmpl-wpuf-form-notification", mixins: [weForms.mixins.Loading], data: function data() {
      return { editing: !1, editingIndex: 0 };
    }, computed: { is_pro: function is_pro() {
        return "true" === weForms.is_pro;
      }, has_sms: function has_sms() {
        return "true" === weForms.has_sms;
      }, pro_link: function pro_link() {
        return wpuf_form_builder.pro_link;
      }, notifications: function notifications() {
        return this.$store.state.notifications;
      }, hasNotifications: function hasNotifications() {
        return Object.keys(this.$store.state.notifications).length;
      } }, methods: { addNew: function addNew() {
        this.$store.commit("addNotification", wpuf_form_builder.defaultNotification);
      }, editItem: function editItem(t) {
        this.editing = !0, this.editingIndex = t;
      }, editDone: function editDone() {
        this.editing = !1, this.$store.commit("updateNotification", { index: this.editingIndex, value: this.notifications[this.editingIndex] }), jQuery(".advanced-field-wrap").slideUp("fast");
      }, deleteItem: function deleteItem(t) {
        confirm("Are you sure") && (this.editing = !1, this.$store.commit("deleteNotification", t), this.$emit("deleteNotification", t));
      }, toggelNotification: function toggelNotification(t) {
        this.$store.commit("updateNotificationProperty", { index: t, property: "active", value: !this.notifications[t].active });
      }, duplicate: function duplicate(t) {
        this.$store.commit("cloneNotification", t);
      }, toggleAdvanced: function toggleAdvanced() {
        jQuery(".advanced-field-wrap").slideToggle("fast");
      }, insertValue: function insertValue(t, e, i) {
        var n = this.notifications[this.editingIndex],
            o = void 0 !== e ? "{" + t + ":" + e + "}" : "{" + t + "}";n[i] = n[i] + o;
      }, insertValueEditor: function insertValueEditor(t, e, i) {
        var n = void 0 !== e ? "{" + t + ":" + e + "}" : "{" + t + "}";this.$emit("insertValueEditor", n);
      } } }), Vue.component("wpuf-integration", { template: "#tmpl-wpuf-integration", computed: { integrations: function integrations() {
        return wpuf_form_builder.integrations;
      }, hasIntegrations: function hasIntegrations() {
        return Object.keys(this.integrations).length;
      }, store: function store() {
        return this.$store.state.integrations;
      }, pro_link: function pro_link() {
        return wpuf_form_builder.pro_link;
      } }, methods: { getIntegration: function getIntegration(t) {
        return this.integrations[t];
      }, getIntegrationSettings: function getIntegrationSettings(t) {
        return this.store[t] || this.getIntegration(t).settings;
      }, isActive: function isActive(t) {
        return !!this.isAvailable(t) && !0 === this.getIntegrationSettings(t).enabled;
      }, isAvailable: function isAvailable(t) {
        return !this.integrations[t] || !this.integrations[t].pro;
      }, toggleState: function toggleState(e, i) {
        if (this.isAvailable(e)) {
          var n = this.getIntegrationSettings(e);n.enabled = !this.isActive(e), this.$store.commit("updateIntegration", { index: e, value: n }), t(i).toggleClass("checked");
        } else this.alert_pro_feature(e);
      }, alert_pro_feature: function alert_pro_feature(t) {
        var e = this.getIntegration(t).title;swal({ title: '<i class="fa fa-lock"></i> ' + e + " <br>" + this.i18n.is_a_pro_feature, text: this.i18n.pro_feature_msg, type: "", showCancelButton: !0, cancelButtonText: this.i18n.close, confirmButtonColor: "#46b450", confirmButtonText: this.i18n.upgrade_to_pro }).then(function (t) {
          t && window.open(wpuf_form_builder.pro_link, "_blank");
        }, function () {});
      }, showHide: function showHide(e) {
        t(e).closest(".wpuf-integration").toggleClass("collapsed");
      } } }), Vue.component("wpuf-integration-erp", { template: "#tmpl-wpuf-integration-erp", mixins: [wpuf_mixins.integration_mixin], methods: { insertValue: function insertValue(t, e, i) {
        var n = void 0 !== e ? "{" + t + ":" + e + "}" : "{" + t + "}";this.settings.fields[i] = n;
      } } }), Vue.component("wpuf-integration-slack", { template: "#tmpl-wpuf-integration-slack", mixins: [wpuf_mixins.integration_mixin] }), Vue.component("wpuf-merge-tags", { template: "#tmpl-wpuf-merge-tags", props: { field: [String, Number, Object], filter: { type: String, default: null } }, data: function data() {
      return { type: null };
    }, mounted: function mounted() {
      t("body").on("click", function (e) {
        t(e.target).closest(".wpuf-merge-tag-wrap").length || t(".wpuf-merge-tags").hide();
      });
    }, computed: { form_fields: function form_fields() {
        var t = this.filter,
            e = this.$store.state.form_fields;return null !== t ? e.filter(function (e) {
          return e.template === t;
        }) : e.filter(function (t) {
          return !_.contains(["action_hook", "custom_hidden_field"], t.template);
        });
      } }, methods: { toggleFields: function toggleFields(e) {
        t(e.target).parent().siblings(".wpuf-merge-tags").toggle("fast");
      }, insertField: function insertField(t, e) {
        this.$emit("insert", t, e, this.field);
      } } }), Vue.component("wpuf-modal", { template: "#tmpl-wpuf-modal", props: { show: Boolean, onClose: Function }, mounted: function mounted() {
      var e = this;t("body").on("keydown", function (t) {
        e.show && 27 === t.keyCode && e.closeModal();
      });
    }, methods: { closeModal: function closeModal() {
        void 0 !== this.onClose ? this.onClose() : this.$emit("hideModal");
      } } }), Vue.component("wpuf-template-modal", { template: "#tmpl-wpuf-template-modal", props: { show: Boolean, onClose: Function }, data: function data() {
      return { loading: !1, category: "all" };
    }, methods: { blankForm: function blankForm(t) {
        this.createForm("blank", t);
      }, createForm: function createForm(e, i) {
        var n = this;n.loading || (n.loading = !0, t(i).addClass("updating-message"), wp.ajax.send("weforms_contact_form_template", { data: { template: e, _wpnonce: weForms.nonce }, success: function success(t) {
            n.$router.push({ name: "edit", params: { id: t.id } });
          }, error: function error(t) {}, complete: function complete() {
            n.loading = !1, t(i).removeClass("updating-message");
          } }));
      } } }), Vue.component("weforms-text-editor", { template: "#tmpl-wpuf-weforms-text-editor", props: { value: { type: String, required: !0 }, i18n: { type: Object, required: !0 }, editingIndex: { type: Number, required: !0 } }, data: function data() {
      return { editorId: _.clone(Date.now()), fileFrame: null, shortcodes: weForms.shortcodes };
    }, mounted: function mounted() {
      var t = this;this.setupEditor(), this.$parent.$on("deleteNotification", function () {
        setTimeout(function () {
          t.editor && t.editor.setContent(t.value);
        }, 500);
      });
    }, beforeDestroy: function beforeDestroy() {
      this.$parent.$off("insertValueEditor"), this.$parent.$off("deleteNotification");
    }, methods: { setupEditor: function setupEditor() {
        var t = this;window.tinymce.init({ selector: "#wefroms-tinymce-" + this.editorId, branding: !1, height: 150, menubar: !1, convert_urls: !1, theme: "modern", skin: "lightgray", content_css: weForms.assetsURL + "/css/customizer.css", fontsize_formats: "10px 11px 13px 14px 16px 18px 22px 25px 30px 36px 40px 45px 50px 60px 65px 70px 75px 80px", font_formats: "Arial=arial,helvetica,sans-serif;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Lucida=Lucida Sans Unicode, Lucida Grande, sans-serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;", plugins: "textcolor colorpicker wplink wordpress code hr wpeditimage", toolbar: ["shortcodes bold italic underline bullist numlist alignleft aligncenter alignjustify alignright link image", "formatselect forecolor backcolor blockquote hr code", "fontselect fontsizeselect removeformat undo redo"], setup: function setup(e) {
            t.editor = e;var i = [];_.forEach(t.shortcodes, function (t, n) {
              i.push({ text: t.title, classes: "menu-section-title" }), _.forEach(t.codes, function (t, o) {
                i.push({ text: t.title, onclick: function onclick() {
                    var i = "[" + n + ":" + o + "]";t.default && (i = "[" + n + ":" + o + ' default="' + t.default + '"]'), t.text && (i = "[" + n + ":" + o + ' text="' + t.text + '"]'), t.plainText && (i = t.text), e.insertContent(i);
                  } });
              });
            }), e.on("change keyup NodeChange", function () {
              t.$emit("input", e.getContent());
            }), t.$parent.$on("insertValueEditor", function (t) {
              e.insertContent(t);
            });
          } });
      }, browseImage: function browseImage(t) {
        var e = this,
            i = { id: 0, url: "", type: "" };if (e.fileFrame) e.fileFrame.open();else {
          var n = [new wp.media.controller.Library({ library: wp.media.query(), multiple: !1, title: e.i18n.selectAnImage, priority: 20, filterable: "uploaded" })];e.fileFrame = wp.media({ title: e.i18n.selectAnImage, library: { type: "" }, button: { text: e.i18n.selectAnImage }, multiple: !1, states: n }), e.fileFrame.on("select", function () {
            e.fileFrame.state().get("selection").map(function (n) {
              return (n = n.toJSON()).id && (i.id = n.id), n.url && (i.url = n.url), n.type && (i.type = n.type), e.insertImage(t, i), null;
            });
          }), e.fileFrame.on("ready", function () {
            e.fileFrame.uploader.options.uploader.params = { type: "wefroms-image-uploader" };
          }), e.fileFrame.open();
        }
      }, insertImage: function insertImage(t, e) {
        if (e.id && "image" === e.type) {
          var i = '<img src="' + e.url + '" alt="' + e.alt + '" title="' + e.title + '" style="max-width: 100%; height: auto;">';t.insertContent(i);
        } else this.alert({ type: "error", text: this.i18n.pleaseSelectAnImage });
      } }, watch: { editingIndex: function editingIndex(t, e) {
        this.editor ? this.editor.setContent(this.value) : this.setupEditor();
      } } });
}(jQuery);
