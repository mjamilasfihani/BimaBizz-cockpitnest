export default {
  name: "InvoiceManager",
  data() {
    return {
      invoices: [],
      loading: false,
    };
  },
  methods: {
    loadInvoices() {
      this.loading = true;
      fetch('./addons/Mitrans/invoices.json')
        .then(response => response.json())
        .then(invoices => {
          this.invoices = invoices;
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
    }
  },
  mounted() {
    this.loadInvoices();
  },
  template: /*html*/ `
  <div class="kiss-margin-large" v-if="!loading">
    <p class="kiss-size-large kiss-margin-top">Invoices</p>
    <div class="table-scroll kiss-margin" ref="tblContainer" v-show="!loading">
      <table class="kiss-table animated fadeIn">
        <thead>
          <tr>
            <th class="kiss-align-center">Invoice ID</th>
            <th class="kiss-align-center">Order ID</th>
            <th class="kiss-align-center">Amount</th>
            <th class="kiss-align-center">Customer</th>
            <th class="kiss-align-center">Status</th>
            <th class="kiss-align-center">Created</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in invoices" :key="invoice.invoice_id">
            <td class="kiss-align-center">{{ invoice.invoice_id }}</td>
            <td class="kiss-align-center">{{ invoice.order_id }}</td>
            <td class="kiss-align-center">{{ invoice.amount }}</td>
            <td class="kiss-align-center">{{ invoice.customer }}</td>
            <td class="kiss-align-center">{{ invoice.status }}</td>
            <td class="kiss-align-center">{{ new Date(invoice.created * 1000).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  `,
};