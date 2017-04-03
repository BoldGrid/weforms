<div class="form-create-page">
    <h1 class="wp-heading-inline"><?php _e( 'Contact Forms', 'wpuf-contact-form' ); ?></h1>

    <form>
        <div class="form-group">
            <label for="exampleInputEmail1">Title</label>
            <input type="email" class="form-control" v-model="title" id="exampleInputEmail1" placeholder="Your form title">
        </div>

        <p><input type="submit" class="button button-primary" v-on:click.prevent="insertForm" value="Create Form" /></p>
    </form>
</div>