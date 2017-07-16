<div class="contact-form-list">
    <h1 class="wp-heading-inline"><?php _e( 'Contact Forms', 'best-contact-form' ); ?></h1>
    <a class="page-title-action add-form" herf="#" v-on:click.prevent="displayModal()"><?php _e( 'Add Form', 'best-contact-form' ); ?></a>

    <wpuf-template-modal :show.sync="showTemplateModal" :onClose="closeModal"></wpuf-template-modal>

    <form-list-table></form-list-table>
</div>