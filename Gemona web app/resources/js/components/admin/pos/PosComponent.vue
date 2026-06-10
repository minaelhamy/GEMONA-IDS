<template>
  <LoadingComponent :props="loading" />
  <PoscustomerComponent v-on:onCustomverCreate="onCustomverCreate" />
  <div class="md:w-[calc(100%-340px)] lg:w-[calc(100%-320px)] xl:w-[calc(100%-377px)]">
    <form class="w-full mb-4" @submit.prevent="search">
      <div class="form-row">
        <div class="form-col-12 sm:form-col-6"
          :class="checkoutProps.form.category || checkoutProps.form.brand ? 'xl:form-col-4' : 'xl:form-col-4'">
          <div class="w-full flex items-center h-10 px-3 rounded-md border border-[#EFF0F6] bg-white">
            <button type="submit" class="lab-line-search ltr:mr-2 rtl:ml-2"></button>
            <input type="search" v-model="props.search.name" :placeholder="$t('label.search_here')" class="w-full">
            <button @click="resetName" type="button" v-if="props.search.name"
              class="text-sm text-red-500 fa-regular fa-circle-xmark"></button>
          </div>
        </div>

        <div class="form-col-12 sm:form-col-6"
          :class="checkoutProps.form.category || checkoutProps.form.brand ? 'xl:form-col-3' : 'xl:form-col-4'">
          <div class="db-field w-full">
            <vue-select v-model="checkoutProps.form.category"
              class="db-field-control appearance-none cursor-pointer f-b-custom-select" id="customer"
              :options="categories" label-by="option" value-by="id" :closeOnSelect="true" :searchable="true"
              :clearOnClose="true" :placeholder="$t('label.select_category')"
              :search-placeholder="$t('label.search_category')" @update:modelValue="setCategory($event)" />
          </div>
        </div>

        <div class="form-col-12 sm:form-col-6"
          :class="checkoutProps.form.category || checkoutProps.form.brand ? 'xl:form-col-3' : 'xl:form-col-4'">
          <div class="db-field w-full">
            <vue-select v-model="checkoutProps.form.brand" class="db-field-control appearance-none cursor-pointer"
              id="customer" :options="brands" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
              :clearOnClose="true" :placeholder="$t('label.select_brand')"
              :search-placeholder="$t('label.search_brand')" @update:modelValue="setBrand($event)" />
          </div>
        </div>

        <div class="form-col-12 sm:form-col-6 xl:form-col-2"
          v-if="checkoutProps.form.category || checkoutProps.form.brand">
          <button class="db-btn-outline h-[38px] w-full flex-shrink-0 !text-[#FB4E4E] !bg-white !border-[#FB4E4E]"
            @click="reset">
            <i class="lab lab-line-reset"></i>
            <span>{{ $t("button.reset") }}</span>
          </button>
        </div>
      </div>

    </form>
    <ProductListComponent v-if="products.length > 0" :products="products" />
  </div>

  <div id="pos-cart"
    class="db-pos-cartDiv fixed top-0 ltr:right-0 rtl:left-0 w-full h-dvh rounded-none z-50 md:z-10 md:top-[85px] ltr:md:right-5 rtl:md:left-5 md:w-[322px] lg:w-[305px] xl:w-[360px] md:h-[calc(100vh-85px)] md:rounded-lg overflow-y-auto thin-scrolling bg-white">
    <div class="p-4">
      <div class="md:hidden text-right mb-3">
        <button class="db-pos-cartCls" @click="closeCanvas('pos-cart')">
          <i class="lab-line-circle-cross text-lg text-[#E93C3C]"></i>
        </button>
      </div>
      <div class="db-field mb-3">
        <BarcodeProductComponent />
      </div>
      <div class="flex gap-2 mb-3">
        <vue-select
          class="db-field-control  w-full flex-auto text-sm rounded-lg appearance-none cursor-pointer text-heading border-[#D9DBE9]"
          id="customer" v-model="checkoutProps.form.customer_id" :options="customers" label-by="name" value-by="id"
          :closeOnSelect="true" :searchable="true" :clearOnClose="true" :placeholder="$t('label.select_customer')"
          :search-placeholder="$t('label.search_customer')" />

        <button @click="addCustomer" type="button"
          class="flex items-center justify-center gap-1.5 px-3 h-10 rounded-lg text-white bg-primary">
          <i class="lab lab-add-circle-line"></i>
          <span class="capitalize text-sm font-bold">{{ $t('button.add') }}</span>
        </button>
      </div>
    </div>

    <div v-if="carts.length === 0" class="flex items-center justify-center">
      <img class="w-52" :src="setting.image_cart" alt="empty">
    </div>

    <ul v-if="carts.length > 0" class="p-4">
      <li v-for="(cart, index) in carts"
        class="flex items-start gap-3 pb-4 mb-4 border-b last:mb-0 last:pb-0 last:border-none border-gray-100">
        <img :src="cart.image" alt="products" class="w-28 rounded-lg flex-shrink-0" />
        <div class="relative w-full overflow-hidden">
          <h4 class="font-semibold capitalize whitespace-nowrap overflow-hidden text-ellipsis mb-1">
            {{ cart.name }}
          </h4>
          <div v-if="cart.variation_id > 0" class="flex flex-wrap mb-2">
            <span class="text-xs capitalize inline-flex items-center">{{ cart.variation_names }}</span>
          </div>
          <div class="flex flex-wrap gap-3 mb-3">
            <span class="font-semibold font-sans">
              {{
                currencyFormat(cart.price, setting.site_digit_after_decimal_point,
                  setting.site_default_currency_symbol, setting.site_currency_position)
              }}
            </span>
            <del v-if="cart.discount > 0" class="font-semibold font-sans text-[#FF6262]">
              {{
                currencyFormat(cart.old_price, setting.site_digit_after_decimal_point,
                  setting.site_default_currency_symbol, setting.site_currency_position)
              }}
            </del>
          </div>
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-1 w-20 p-1 rounded-full bg-[#F7F7FC]">
              <button @click.prevent="quantityDecrement(index, cart)" type="button"
                :class="cart.quantity === 1 ? 'cursor-not-allowed' : ''"
                class="lab-fill-circle-minus text-lg leading-none transition-all duration-300 hover:text-primary"></button>
              <input v-on:keypress="onlyNumber($event)" v-on:keyup="quantityUp(index, cart, $event)" type="number"
                v-model="cart.quantity" class="text-center w-full h-5 text-sm font-medium">
              <button :class="cart.quantity >= cart.stock ? 'cursor-not-allowed' : ''"
                @click.prevent="quantityIncrement(index, cart)" type="button"
                class="lab-fill-circle-plus text-lg leading-none transition-all duration-300 hover:text-primary"></button>
            </div>
            <button @click.prevent="removeProduct(index)"
              class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#FFF4F4] text-[#E93C3C] transition-all duration-300 hover:bg-[#E93C3C] hover:text-white">
              <i class="lab-line-trash text-sm"></i>
              <span class="text-xs font-medium capitalize hidden sm:block">{{ $t('button.remove') }}</span>
            </button>
          </div>
        </div>
      </li>
    </ul>

    <div class="p-4">
      <div class="flex h-[38px]" v-if="carts.length > 0">
        <div class="db-field-down-arrow">
          <select v-model="discountType"
            class="w-[120px] h-full cursor-pointer text-sm font-client ltr:rounded-tl ltr:rounded-bl rtl:rounded-tr rtl:rounded-br appearance-none border ltr:pl-3 rtl:pr-3 text-heading border-[#EFF0F6]">
            <option :value="discountTypeEnum.PERCENTAGE">{{ $t("label.percentage") }}</option>
            <option :value="discountTypeEnum.FIXED">{{ $t("label.fixed") }}</option>
          </select>
        </div>
        <input v-model="discount" type="text" v-on:keypress="floatNumber($event)"
          :placeholder="$t('label.add_discount')" class="w-full h-full border-t border-b px-3 border-[#EFF0F6]">
        <button @click.prevent="applyDiscount" type="submit"
          class="flex-shrink-0 w-16 h-full text-sm font-medium font-client capitalize ltr:rounded-tr ltr:rounded-br rtl:rounded-tl rtl:rounded-bl text-white bg-[#008BBA]">
          {{ $t('button.apply') }}
        </button>
      </div>
      <div class="text-xs db-field-alert m-0 mt-1" v-if="discountErrorMessage">
        <span>{{ discountErrorMessage }}</span>
      </div>

      <ul class="flex flex-col gap-1.5 mt-4 mb-4">
        <li class="flex items-center justify-between">
          <span class="text-sm font-client capitalize leading-6 text-[#2E2F38]">
            {{ $t("label.sub_total") }}
          </span>
          <span class="text-sm font-client capitalize leading-6 text-[#2E2F38]">
            {{
              currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                setting.site_default_currency_symbol, setting.site_currency_position)
            }}
          </span>
        </li>
        <li class="flex items-center justify-between">
          <span class="text-sm font-client capitalize leading-6">{{ $t('label.tax') }}</span>
          <span class="text-sm font-client capitalize leading-6">
            {{
              currencyFormat(totalTax, setting.site_digit_after_decimal_point,
                setting.site_default_currency_symbol, setting.site_currency_position)
            }}
          </span>
        </li>
        <li class="flex items-center justify-between">
          <span class="text-sm font-client capitalize leading-6">{{ $t("label.discount") }}</span>
          <span class="text-sm font-client capitalize leading-6">
            {{
              currencyFormat(posDiscount,
                setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
                setting.site_currency_position)
            }}
          </span>
        </li>

        <li class="flex items-center justify-between">
          <span class="text-sm font-medium font-client capitalize leading-6 text-[#2E2F38]">
            {{ $t("label.total") }}
          </span>
          <span class="text-sm font-medium font-client capitalize leading-6 text-[#2E2F38]">
            {{
              currencyFormat((subtotal + totalTax) - posDiscount,
                setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
                setting.site_currency_position)
            }}
          </span>
        </li>
      </ul>
      <div class="flex items-center justify-center gap-6" v-if="carts.length > 0">
        <button @click.prevent="resetCart"
          class="capitalize text-sm font-medium leading-6 font-client w-full text-center rounded-3xl py-2 text-white bg-[#FB4E4E]">
          {{ $t('button.cancel') }}
        </button>
        <button @click.prevent="orderPayment"
          class="capitalize text-sm font-medium leading-6 font-client w-full text-center rounded-3xl py-2 text-white bg-[#1AB759]">
          {{ $t('button.order') }}
        </button>
      </div>
    </div>
  </div>

  <button @click="openCanvas('pos-cart')" type="button"
    class="db-pos-cartBtn fixed md:hidden bottom-0 left-0 z-10 w-full h-14 py-4 text-center flex items-center justify-center shadow-xl-top gap-3 bg-primary text-white">
    <i class="lab-fill-bag text-xl"></i>
    <span class="text-base font-medium"> {{ totalProducts() }} {{ $t('label.products') }} - {{
      currencyFormat((subtotal + totalTax) - posDiscount,
        setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
        setting.site_currency_position)
    }}</span>
  </button>


  <ReceiptComponent :order="order" />
  <PaymentComponent v-on:orderSubmit="orderSubmit"
    :total="currencyFormat((subtotal + totalTax) - posDiscount, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)" />
</template>
<script>
import LoadingComponent from "../components/LoadingComponent";
import ProductListComponent from "./ProductListComponent";
import sourceEnum from "../../../enums/modules/sourceEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum";
import roleEnum from "../../../enums/modules/roleEnum";
import appService from "../../../services/appService";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum";
import alertService from "../../../services/alertService";
import targetService from "../../../services/targetService";
import ReceiptComponent from "./ReceiptComponent";
import PaymentComponent from "./PaymentComponent";
import PoscustomerComponent from './PosCustomerComponent';
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum";
import BarcodeProductComponent from "./BarcodeProductComponent.vue";

export default {
  name: "PosComponent",
  components: {
    ReceiptComponent,
    LoadingComponent,
    ProductListComponent,
    PoscustomerComponent,
    BarcodeProductComponent,
    PaymentComponent
  },
  data() {
    return {
      loading: {
        isActive: false,
      },
      order: {},
      discount: null,
      checkoutProps: {
        form: {
          customer_id: null,
          category: null,
          brand: null,
          discount: 0,
          total: 0,
          pos_payment_method: posPaymentMethodEnum.CASH,
          pos_payment_note: '',
          pos_payment_receive_amount: 0
        },
      },
      props: {
        search: {
          paginate: 0,
          order_column: "id",
          order_type: "asc",
          name: "",
          product_category_id: "",
          product_brand_id: "",
          status: statusEnum.ACTIVE
        },
      },
      searchProps: {
        paginate: 0,
        order_column: "id",
        order_type: "asc",
        status: statusEnum.ACTIVE
      },
      settings: {
        itemsToShow: 6.2,
        wrapAround: false,
        snapAlign: "start"
      },
      statusEnum: statusEnum,
      discountTypeEnum: discountTypeEnum,
      posPaymentMethodEnum: posPaymentMethodEnum,
      discountType: discountTypeEnum.PERCENTAGE,
      discountErrorMessage: "",
      form: {},
    }
  },
  computed: {
    setting: function () {
      return this.$store.getters['frontendSetting/lists'];
    },
    categories: function () {
      return this.$store.getters["productCategory/depthTrees"];
    },
    brands: function () {
      return this.$store.getters["productBrand/lists"];
    },
    products: function () {
      return this.$store.getters["product/lists"];
    },
    customers: function () {
      return this.$store.getters['user/lists'];
    },
    carts: function () {
      return this.$store.getters['posCart/lists'];
    },
    subtotal: function () {
      return this.$store.getters['posCart/subtotal'];
    },
    total: function () {
      return this.$store.getters['posCart/total'];
    },
    posCartDiscount: function () {
      return this.$store.getters['posCart/discount'];
    },
    totalTax: function () {
      return this.$store.getters['posCart/totalTax'];
    },
    posCartProducts: function () {
      return this.$store.getters['posCart/lists'];
    },
    posDiscount: function () {
      return this.$store.getters['posCart/discount'];
    }
  },
  mounted() {
    this.productCategories();
    this.productBrands();
    this.productList();
    try {
      this.customerList();

      this.loading.isActive = true;
      this.$store.dispatch("company/lists").then((res) => {
        this.company.name = res.data.data.company_name;
        this.company.email = res.data.data.company_email;
        this.company.phone = res.data.data.company_phone;
        this.company.address = res.data.data.company_address;
        this.loading.isActive = false;
      }).catch((err) => {
        this.loading.isActive = false;
      });
    } catch (err) {
      this.loading.isActive = false;
    }
  },
  methods: {
    hideTarget: function (id, cClass) {
      targetService.hideTarget(id, cClass);
    },
    openCanvas: function (id) {
      return appService.openCanvas(id);
    },
    closeCanvas: function (id) {
      return appService.closeCanvas(id);
    },
    onlyNumber: function (e) {
      return appService.onlyNumber(e);
    },
    floatNumber: function (e) {
      return appService.floatNumber(e);
    },
    currencyFormat(amount, decimal, currency, position) {
      return appService.currencyFormat(amount, decimal, currency, position);
    },
    floatFormat(amount, decimal) {
      return appService.floatFormat(amount, decimal);
    },
    reset: function () {
      this.props.search.name = "";
      this.checkoutProps.form.category = null;
      this.props.search.product_category_id = "";
      this.checkoutProps.form.brand = null;
      this.props.search.product_brand_id = "";
      this.productList();
    },
    search: function () {
      this.productList();
    },
    addCustomer: function () {
      appService.modalShow("#customerModal");
    },
    productCategories: function (page = 1) {
      this.loading.isActive = true;
      this.props.search.page = page;
      this.$store.dispatch("productCategory/depthTrees", this.searchProps).then((res) => {
        this.loading.isActive = false;
      }).catch((err) => {
        this.loading.isActive = false;
      });
    },
    productBrands: function (page = 1) {
      this.loading.isActive = true;
      this.props.search.page = page;
      this.$store.dispatch("productBrand/lists", this.searchProps).then((res) => {
        this.loading.isActive = false;
      }).catch((err) => {
        this.loading.isActive = false;
      });
    },
    customerList: function (id = null) {
      this.loading.isActive = true;
      this.$store.dispatch('user/lists', {
        order_column: 'id',
        order_type: 'asc',
        status: statusEnum.ACTIVE,
        role_id: roleEnum.CUSTOMER
      }).then((res) => {
        this.checkoutProps.form.customer_id = id === null ? res.data.data[0].id : id;
        this.loading.isActive = false;
      }).catch((err) => {
        this.loading.isActive = false;
      });
    },
    productList: function (page = 1) {
      this.loading.isActive = true;
      this.props.search.page = page;
      this.$store.dispatch("product/lists", this.props.search).then((res) => {
        this.loading.isActive = false;
      }).catch((err) => {
        this.loading.isActive = false;
      });
    },
    setCategory: function (id) {
      this.props.search.product_category_id = id;
      this.productList();
    },
    setBrand: function (id) {
      this.props.search.product_brand_id = id;
      this.productList();
    },
    quantityUp: function (id, product, e) {
      let quantity = e.target.value;

      if (quantity === 0 || quantity < 0 || quantity === "0") {
        quantity = 1;
      }
      if (quantity > product.stock) {
        quantity = product.stock
      }
      this.$store.dispatch('posCart/quantity', { id: id, status: quantity }).then().catch();
      this.checkoutProps.form.discount = 0;
      this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
    },
    quantityIncrement: function (id, product) {
      let quantity = product.quantity;
      quantity++;
      if (quantity <= 0) {
        quantity = 1;
      }

      if (quantity > product.stock) {
        quantity--;
      }
      this.$store.dispatch('posCart/quantity', { id: id, status: quantity }).then().catch();
      this.checkoutProps.form.discount = 0;
      this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
    },
    quantityDecrement: function (id, product) {
      let quantity = product.quantity;
      quantity--;
      if (quantity <= 0) {
        quantity = 1;
      }
      this.$store.dispatch('posCart/quantity', { id: id, status: quantity }).then().catch();
      this.checkoutProps.form.discount = 0;
      this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
    },
    removeProduct: function (id) {
      this.$store.dispatch('posCart/remove', { id: id }).then().catch();
      this.checkoutProps.form.discount = 0;
      this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
      if (this.carts.length === 0) {
        this.checkoutProps.form.pos_payment_method = posPaymentMethodEnum.CASH;
        this.checkoutProps.form.pos_payment_note = "";
        this.discountErrorMessage = "";

      }
    },
    applyDiscount: function () {
      this.discountErrorMessage = "";
      if (this.discountType === discountTypeEnum.FIXED) {
        if (this.subtotal < this.discount) {
          this.discountErrorMessage = this.$t('message.discount_fixed_error_message');
          this.checkoutProps.form.discount = 0;
          this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
        } else {
          this.discountErrorMessage = "";
          this.checkoutProps.form.discount = parseFloat(+this.discount).toFixed(this.setting.site_digit_after_decimal_point);
          this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
        }
      } else {
        if (this.discount > 100) {
          this.discountErrorMessage = this.$t('message.discount_error_message');
          this.checkoutProps.form.discount = 0;
          this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();
        } else {
          this.discountErrorMessage = "";
          this.checkoutProps.form.discount = parseFloat((this.subtotal * this.discount) / 100).toFixed(this.setting.site_digit_after_decimal_point);
          this.$store.dispatch('posCart/discount', this.checkoutProps.form.discount).then().catch();

        }
      }
    },
    resetCart: function () {
      this.$store.dispatch('posCart/resetCart').then(res => {
      }).catch();
      if (this.carts.length === 0) {
        this.checkoutProps.form.pos_payment_method = posPaymentMethodEnum.CASH;
        this.checkoutProps.form.pos_payment_note = "";
        this.discountErrorMessage = "";

      }
    },
    orderSubmit: function (data) {
      this.loading.isActive = true;
      this.form = {
        customer_id: this.checkoutProps.form.customer_id,
        subtotal: this.subtotal,
        discount: parseFloat(this.posCartDiscount),
        tax: this.totalTax,
        total: this.total,
        order_type: orderTypeEnum.POS,
        source: sourceEnum.POS,
        payment_method: paymentTypeEnum.CASH_ON_DELIVERY,
        pos_payment_method: data.pos_payment_method,
        pos_payment_note: data.pos_payment_method === posPaymentMethodEnum.CASH ? "" : data.pos_payment_note,
        pos_received_amount: data.pos_received_amount,
        products: JSON.stringify(this.posCartProducts)
      }
      this.$store.dispatch('posOrder/save', this.form).then(orderResponse => {
        this.$store.dispatch('posCart/resetCart').then(res => {
          this.checkoutProps.form.pos_payment_method = posPaymentMethodEnum.CASH;
          this.checkoutProps.form.pos_payment_note = "";
          this.discount = null;
          this.discountErrorMessage = "";
          this.loading.isActive = false;
        }).catch();
        alertService.success(this.$t('message.pos_order'));
        this.$store.dispatch('posOrder/show', orderResponse.data.data.id).then(res => {
          this.order = res.data.data;
          this.loading.isActive = false;
        }).catch((error) => {
          this.loading.isActive = false;
          alertService.error(error.response.data.message);
        });
        appService.modalHide('#orderPayment');
        appService.modalShow('#posReceiptModal');
      }).catch((err) => {
        this.loading.isActive = false;
        if (typeof err.response.data.errors === 'object') {
          _.forEach(err.response.data.errors, (error) => {
            alertService.error(error[0]);
          });
        }
      });
    },
    orderPayment: function () {
      appService.modalShow('#orderPayment');
    },
    totalProducts: function () {
      if (this.carts.length > 0) {
        let totalProduct = 0;
        this.carts.forEach(cart => {
          totalProduct += cart.quantity;
        });
        return totalProduct;
      }
    },
    onCustomverCreate: function (customerId) {
      appService.modalHide();
      this.customerList(customerId);
    },
    resetName: function () {
      this.props.search.name = "";
    }
  }
}
</script>