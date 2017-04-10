<div class="wrap">
    <div id="wpuf-contact-form-app">
        <router-view></router-view>
    </div>
</div>

<style type="text/css">
    th.col-form-name { width: 25%; }
    th.col-form-shortcode { width: 25%; }

    th.col-form-entries,
    th.col-form-views,
    th.col-form-conversion { width: 10%; }

    th.col-entry-id {
        width: 5%;
    }
    th.col-entry-details {
        width: 10%;
    }

    .wpuf-contact-form-entry-wrap {
        margin-top: 20px;
        width: 100%;
        overflow: hidden;
    }

    .wpuf-contact-form-entry-wrap .wpuf-contact-form-entry-left {
        float: left;
        width: 70%;
    }

    .wpuf-contact-form-entry-wrap .wpuf-contact-form-entry-right {
        float: right;
        width: 28%;
    }

    .wpuf-contact-form-entry-wrap h2.hndle {
        padding: 8px 12px;
        margin: 0;
        font-size: 14px;
    }

    .wpuf-contact-form-entry-wrap table.widefat {
        border: none
    }
</style>
