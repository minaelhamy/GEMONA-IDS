<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.product_brands') }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12 sm:col-5">
                    <img class="db-image" alt="brand" :src="productBrand.cover">
                </div>
                <div class="col-12 sm:col-7 md:pl-8">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ productBrand.name }}</h3>
                    <label class="db-badge mb-3" :class="statusClass(productBrand.status)">
                        {{ enums.statusEnumArray[productBrand.status] }}
                    </label>
                    <p class="db-light-text">
                        {{ productBrand.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import appService from "../../../../services/appService";

export default {
    name: "ProductBrandShowComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            }
        }
    },
    computed: {
        productBrand: function () {
            return this.$store.getters['productBrand/show'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('productBrand/show', this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        }
    }
}
</script>
