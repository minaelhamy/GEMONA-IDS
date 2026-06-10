<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div id="productSection" class="db-tab-div active">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5">
                <button @click.prevent="multiTargets($event, 'tab-action', 'tab-content', 'productSectionInformation')"
                    class="tab-action active w-full flex items-center gap-3 h-10 px-4 rounded-lg bg-white hover:text-primary hover:bg-primary/5">
                    <i class="lab lab-fill-info lab-font-size-16"></i>
                    {{ $t("label.information") }}
                </button>

                <button type="button"
                    @click.prevent="multiTargets($event, 'tab-action', 'tab-content', 'productSectionProduct')"
                    class="tab-action w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5">
                    <i class="lab lab-fill-products lab-font-size-16"></i>
                    {{ $t('label.products') }}
                </button>
            </div>

            <div class="db-card tab-content active" id="productSectionInformation">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ $t('label.information') }}</h3>
                </div>

                <div class="db-card-body">
                    <div class="row py-2">
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.name') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ productSection.name }}</span>
                            </div>
                        </div>
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.slug') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ productSection.slug }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.status') }}</span>
                                <span class="db-list-item-text">
                                    <span :class="statusClass(productSection.status)">{{
                                        enums.statusEnumArray[productSection.status]
                                    }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="db-card tab-content" id="productSectionProduct">
                <ProductSectionProductListComponent :productSection="parseInt($route.params.id)" />
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import targetService from "../../../services/targetService";
import statusEnum from "../../../enums/modules/statusEnum";
import ProductSectionProductListComponent from "./product/ProductSectionProductListComponent";

export default {
    name: "ProductSectionShowComponent",
    components: {
        LoadingComponent,
        ProductSectionProductListComponent
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
                },
            },
        }
    },
    computed: {
        productSection: function () {
            return this.$store.getters['productSection/show'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('productSection/show', this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        multiTargets: function (event, commonBtnClass, commonDivClass, targetID) {
            targetService.multiTargets(event, commonBtnClass, commonDivClass, targetID);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
    }
}

</script>
