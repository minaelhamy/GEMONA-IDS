<template>
  <LoadingComponent :props="loading" />
  <div class="db-card db-tab-div active">
    <div class="db-card-header border-none">
      <h3 class="db-card-title">{{ $t("label.address") }}</h3>
      <div class="db-card-filter">
        <TableLimitComponent :method="list" :search="address.search" :page="paginationPage" />
        <CustomerAddressCreateComponent :props="address" />
      </div>
    </div>

    <div class="db-table-responsive">
      <table class="db-table stripe">
        <thead class="db-table-head">
          <tr class="db-table-head-tr">
            <th class="db-table-head-th">
              {{ $t("label.name") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.email") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.phone") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.address") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.country") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.state") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.city") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.zip_code") }}
            </th>
            <th class="db-table-head-th">
              {{ $t("label.action") }}
            </th>
          </tr>
        </thead>
        <tbody class="db-table-body" v-if="addresses.length > 0">
          <tr class="db-table-body-tr" v-for="address in addresses" :key="address">
            <td class="db-table-body-td">
              {{ address.full_name }}
            </td>
            <td class="db-table-body-td">
              {{ address.email }}
            </td>
            <td class="db-table-body-td">
              {{ address.country_code }} {{ address.phone }}
            </td>
            <td class="db-table-body-td">
              {{ address.address }}
            </td>
            <td class="db-table-body-td">
              {{ address.country }}
            </td>
            <td class="db-table-body-td">
              {{ address.state }}
            </td>
            <td class="db-table-body-td">
              {{ address.city }}
            </td>
            <td class="db-table-body-td">
              {{ address.zip_code }}
            </td>
            <td class="db-table-body-td">
              <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                <SmIconSidebarModalEditComponent @click="edit(address)" />
                <SmIconDeleteComponent @click="destroy(address.id)" />
              </div>
            </td>
          </tr>
        </tbody>
        <tbody class="db-table-body" v-else>
            <tr class="db-table-body-tr">
                <td class="db-table-body-td text-center" colspan="9">
                    <div class="p-4">
                        <div class="max-w-[300px] mx-auto mt-2">
                            <img class="w-full h-full" :src="ENV.API_URL+'/images/default/not-found/not_found.png'" alt="Not Found">
                        </div>
                        <span class="d-block mt-3 text-lg">{{ $t('message.no_data_found') }}</span>
                    </div>
                </td>
            </tr>
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="addresses.length > 0">
      <PaginationSMBox :pagination="pagination" :method="list" />
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <PaginationTextComponent :props="{ page: paginationPage }" />
        <PaginationBox :pagination="pagination" :method="list" />
      </div>
    </div>
  </div>
</template>

<script>
import alertService from "../../../../services/alertService";
import LoadingComponent from "../../components/LoadingComponent";
import TableLimitComponent from "../../components/TableLimitComponent";
import CustomerAddressCreateComponent from "./CustomerAddressCreateComponent";
import appService from "../../../../services/appService";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent";
import SmIconSidebarModalEditComponent from "../../components/buttons/SmIconSidebarModalEditComponent";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import ENV from "../../../../config/env";

export default {
  name: "CustomerAddressList",
  components: {
    LoadingComponent,
    CustomerAddressCreateComponent,
    TableLimitComponent,
    SmIconDeleteComponent,
    SmIconSidebarModalEditComponent,
    PaginationSMBox,
    PaginationBox,
    PaginationTextComponent,
  },
  props: ["props"],
  data() {
    return {
      loading: {
        isActive: false,
      },
      address: {
        form: {
          full_name: "",
          email: "",
          country_code: null,
          phone: "",
          country: null,
          state: null,
          city: null,
          zip_code: "",
          address: "",
        },
        search: {
          paginate: 1,
          page: 1,
          per_page: 10,
          order_column: "id",
          order_type: "desc",
        },
        status: false,
        isMap: false,
        flag: "",
        states: [],
        cities: []
      },
      ENV: ENV,
    }
  },
  mounted() {
    this.list();
  },
  computed: {
    addresses: function () {
      return this.$store.getters["customerAddress/lists"];
    },
    pagination: function () {
      return this.$store.getters["customerAddress/pagination"];
    },
    paginationPage: function () {
      return this.$store.getters["customerAddress/page"];
    },
  },
  methods: {
    list: function (page = 1) {
      this.loading.isActive = true;
      this.address.search.page = page;
      this.$store.dispatch("customerAddress/lists", {
        id: this.props,
        search: this.address.search
      }).then((res) => {
        this.loading.isActive = false;
      }).catch((err) => {
        this.loading.isActive = false;
      });
    },
    edit: function (address) {
      appService.modalShow();
      this.loading.isActive = true;
      this.$store.dispatch("customerAddress/edit", address.id).then(async (res) => {
        this.loading.isActive = false;
        this.address.isMap = true;

        if (address.state !== "") {
          await this.$store.dispatch('state/statesByCountry', address.country)
            .then((res) => {
              this.address.states = res.data.data;
            })
          await this.$store.dispatch('city/citiesByState', address.state)
            .then((res) => {
              this.address.cities = res.data.data;
            })
          if (address.city === "") {
            this.address.form.city = null;
          }
        } else {
          await this.$store.dispatch('state/statesByCountry', address.country)
            .then((res) => {
              this.address.states = res.data.data;
            })

          this.address.form.state = null;
          this.address.form.city = null;
        }

        setTimeout(() => {
          this.address.form = {
            full_name: address.full_name,
            email: address.email,
            country_code: address.country_code,
            phone: address.phone,
            country: address.country,
            state: address.state,
            city: address.city,
            zip_code: address.zip_code,
            address: address.address,
          };

          if (address.state === "") {
            this.address.form.state = null;
          }

          if (address.city === "") {
            this.address.form.city = null;
          }

        }, 200);

        this.$store.dispatch('countryCode/callingCode', address.country_code).then(res => {
          this.address.flag = res.data.data.flag_emoji;
          this.loading.isActive = false;
        }).catch((err) => {
          this.loading.isActive = false;
        });
      }).catch((err) => {
        alertService.error(err.response.data.message);
      });
    },
    destroy: function (addressId) {
      appService.destroyConfirmation().then((res) => {
        try {
          this.loading.isActive = true;
          this.$store.dispatch("customerAddress/destroy", {
            id: this.props,
            addressId: addressId,
            search: this.address.search
          }).then((res) => {
            this.loading.isActive = false;
            alertService.successFlip(null, this.$t("label.address"));
          }).catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response.data.message);
          });
        } catch (err) {
          this.loading.isActive = false;
          alertService.error(err.response.data.message);
        }
      }).catch((err) => {
        this.loading.isActive = false;
      });
    },
  }

}
</script>
