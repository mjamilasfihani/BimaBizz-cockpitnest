export default {
  name: "Mitrans",
  data() {
    return {
      transactions: [],
      loading: false,
    };
  },
  methods: {
    load() {
      this.loading = true;
      fetch('./addons/Mitrans/transactions.json')
        .then(response => response.json())
        .then(transactions => {
          this.transactions = transactions;
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
    },
    checkStatus(orderId) {
      this.loading = true;
      fetch(`api/mitrans/checkStatus?order_id=${orderId}`)
        .then(response => response.json())
        .then(updatedTransaction => {
          const index = this.transactions.findIndex(t => t.order_id === orderId);
          if (index !== -1) {
            this.transactions[index] = updatedTransaction;
            this.saveTransactions();
          }
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
    },
    saveTransactions() {
      fetch('./addons/Mitrans/transactions.json', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(this.transactions)
      });
    },
    generateInvoice(orderId) {
      this.loading = true;
      fetch(`api/mitrans/generateInvoice?order_id=${orderId}`)
        .then(response => response.json())
        .then(result => {
          this.loading = false;
          alert(result.message);
        })
        .catch(() => {
          this.loading = false;
        });
    },
    processRefund(orderId) {
      this.loading = true;
      fetch(`api/mitrans/processRefund?order_id=${orderId}`)
        .then(response => response.json())
        .then(result => {
          this.loading = false;
          alert(result.message);
        })
        .catch(() => {
          this.loading = false;
        });
    }
  },
  mounted() {
    this.load();
  },
  template: /*html*/ `
  <app-loader class="kiss-margin-large" v-if="loading"></app-loader>

  <div class="kiss-margin-large" v-if="!loading">
    <p class="kiss-size-large kiss-margin-top">Transactions</p>
    <div class="table-scroll kiss-margin" ref="tblContainer" v-show="!loading">
      <table class="kiss-table animated fadeIn">
        <thead>
          <tr>
            <th class="kiss-align-center">Order ID</th>
            <th class="kiss-align-center">Amount</th>
            <th class="kiss-align-center">Customer</th>
            <th class="kiss-align-center">Status</th>
            <th class="kiss-align-center">Shipping Address</th>
            <th class="kiss-align-center">Payment Type</th>
            <th class="kiss-align-center">Created</th>
            <th class="kiss-align-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="transaction in transactions" :key="transaction.order_id">
            <td class="kiss-align-center">
              <a :href="'content/collection/item/order/' + transaction.order_id">{{ transaction.order_id }}</a>
             </td>
            <td class="kiss-align-center">{{ transaction.amount }}</td>
            <td class="kiss-align-center">{{ transaction.customer }}</td>
            <td class="kiss-align-center">{{ transaction.status }}</td>
            <td class="kiss-align-center">{{ transaction.shipping_address.address }}, {{ transaction.shipping_address.city }}, {{ transaction.shipping_address.postal_code }}, {{ transaction.shipping_address.country_code }}</td>
            <td class="kiss-align-center">{{ transaction.payment_type }}</td>
            <td class="kiss-align-center">{{ new Date(transaction.created * 1000).toLocaleString() }}</td>
            <td class="kiss-align-center">
              <button class="kiss-button kiss-button-primary" @click="checkStatus(transaction.order_id)">Check Status</button>
              <button class="kiss-button kiss-button-secondary" @click="generateInvoice(transaction.order_id)">Generate Invoice</button>
              <button class="kiss-button kiss-button-danger" @click="processRefund(transaction.order_id)">Process Refund</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  `,
};