<template>
    <LoadingComponent :props="loading"/>

    <div class="p-6 rounded-xl mb-8 shadow-xs bg-white">
        <div class="flex flex-wrap gap-4 sm:gap-6">
            <img class="w-[120px] h-[120px] object-cover rounded-lg" alt="avatar" :src="supplier.image">
            <div>
                <h3 class="text-[26px] font-semibold font-rubik leading-[40px] capitalize">{{ supplier.name }}</h3>
                <label class="p-0.5 px-2 rounded text-[10px] leading-4 font-medium font-rubik uppercase mb-[22px] text-[#E89806] bg-[#FFF5DE]">
                    {{ $t('label.supplier') }}
                </label>
            </div>
        </div>
    </div>

    <div id="profile" class="profile-tabDiv active">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('label.basic_info') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row py-2">
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.company") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.company }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.email") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.email }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.phone") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.country_code }} {{ supplier.phone }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.city") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.city }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.state") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.state }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.zip_code") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.zip_code }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.address") }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ supplier.address }}</span>
                        </div>
                    </div>
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
    name: "SupplierShowComponent",
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
        supplier: function () {
            return this.$store.getters['supplier/show'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('supplier/show', this.$route.params.id).then(res => {
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
