"use strict";
weForms.mixins.BulkAction = { data: function data() {
    return { bulkAction: "-1", checkedItems: [] };
  }, computed: { selectAll: { get: function get() {
        return !!this.items && this.checkedItems.length == this.items.length;
      }, set: function set(t) {
        var e = [],
            n = this;t && this.items.forEach(function (t) {
          void 0 !== t[n.index] ? e.push(t[n.index]) : e.push(t.id);
        }), this.checkedItems = e;
      } } }, methods: { deleteBulk: function deleteBulk() {
      var t = this;t.loading = !0, wp.ajax.send(t.bulkDeleteAction, { data: { permanent: "trash" == this.status ? 1 : 0, ids: this.checkedItems, _wpnonce: weForms.nonce }, success: function success(e) {
          t.checkedItems = [], t.fetchData();
        }, error: function error(t) {
          alert(t);
        }, complete: function complete(e) {
          t.loading = !1;
        } });
    }, restoreBulk: function restoreBulk() {
      var t = this;t.loading = !0, wp.ajax.send("weforms_form_entry_restore_bulk", { data: { ids: this.checkedItems, _wpnonce: weForms.nonce }, success: function success(e) {
          t.checkedItems = [], t.fetchData();
        }, error: function error(t) {
          alert(t);
        }, complete: function complete(e) {
          t.loading = !1;
        } });
    } } }, weForms.mixins.Cookie = { methods: { setCookie: function setCookie(t, e, n) {
      var i = new Date();i.setTime(i.getTime() + 24 * n * 60 * 60 * 1e3);var s = "expires=" + i.toUTCString();document.cookie = t + "=" + e + ";" + s + ";path=/";
    }, getCookie: function getCookie(t) {
      for (var e = t + "=", n = decodeURIComponent(document.cookie).split(";"), i = 0; i < n.length; i++) {
        for (var s = n[i]; " " == s.charAt(0);) {
          s = s.substring(1);
        }if (0 == s.indexOf(e)) return s.substring(e.length, s.length);
      }return "";
    } } }, weForms.mixins.Loading = { watch: { loading: function loading(t) {
      t ? (NProgress.configure({ parent: "#wpadminbar" }), NProgress.start()) : NProgress.done();
    } } }, weForms.mixins.Paginate = { data: function data() {
    return { totalItems: 0, totalPage: 1, currentPage: 1, pageNumberInput: 1 };
  }, methods: { isFirstPage: function isFirstPage() {
      return 1 == this.currentPage;
    }, isLastPage: function isLastPage() {
      return this.currentPage == this.totalPage;
    }, goFirstPage: function goFirstPage() {
      this.currentPage = 1, this.pageNumberInput = this.currentPage, this.goToPage();
    }, goLastPage: function goLastPage() {
      this.currentPage = this.totalPage, this.pageNumberInput = this.currentPage, this.goToPage();
    }, goToPage: function goToPage(t) {
      "prev" == t ? this.currentPage-- : "next" == t ? this.currentPage++ : !isNaN(t) && t <= this.totalPage && (this.currentPage = t), this.pageNumberInput = this.currentPage, this.fetchData();
    } } }, weForms.mixins.Tabs = { methods: { makeActive: function makeActive(t) {
      this.activeTab = t;
    }, isActiveTab: function isActiveTab(t) {
      return this.activeTab === t;
    } } };
