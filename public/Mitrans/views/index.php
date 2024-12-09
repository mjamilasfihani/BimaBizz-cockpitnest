<kiss-container class="kiss-margin-small">

  <ul class="kiss-breadcrumbs">
    <li><a href="<?php echo $this->route('/mitrans') ?>"><?php echo t('Mitrans') ?></a></li>
  </ul>

  <vue-view>

    <template>
        <mitrans></mitrans>
        <invoice-manager></invoice-manager>
        <refund-manager></refund-manager>
    </template>

    <script type="module">

        export default {

            components: {
                mitrans: 'mitrans:assets/vue-components/mitrans.js',
                invoiceManager: 'mitrans:assets/vue-components/invoice-manager.js',
                refundManager: 'mitrans:assets/vue-components/refund-manager.js'
            }
        }

    </script>

</vue-view>

</kiss-container>