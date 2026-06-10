<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.benefits') }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12 sm:col-5">
                    <img class="db-image" alt="brand" :src="benefit.cover">
                </div>
                <div class="col-12 sm:col-7 md:pl-8">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ benefit.title }}</h3>
                    <label class="db-badge mb-3" :class="statusClass(benefit.status)">
                        {{ enums.statusEnumArray[benefit.status] }}
                    </label>
                    <p class="db-light-text">
                        {{ benefit.description }}
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
    name: "BenefitShowComponent",
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
        benefit: function () {
            return this.$store.getters['benefit/show'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('benefit/show', this.$route.params.id).then(res => {
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
