<template>
    <LoadingComponent :props="loading" />

    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("label.outlet") }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12">
                    <ul class="db-list single">
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.name")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.name
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.latitude")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.latitude
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.longitude")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.longitude
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.email")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.email
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.phone")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.country_code + outlet.phone
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.city")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.city
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.state")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.state
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.zip_code")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.zip_code
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.address")
                            }}</span>
                            <span class="db-list-item-text">{{
                                outlet.address
                            }}</span>
                        </li>
                        <li class="db-list-item">
                            <span class="db-list-item-title">{{
                                $t("label.status")
                            }}</span>
                            <span class="db-list-item-text">
                                <span :class="statusClass(outlet.status)" class="db-badge">{{
                                    enums.statusEnumArray[outlet.status]
                                }}</span>
                            </span>
                        </li>
                    </ul>
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
    name: "OutletShowComponent",
    components: {
        LoadingComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
        };
    },
    computed: {
        outlet: function () {
            return this.$store.getters["outlet/show"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store
            .dispatch("outlet/show", this.$route.params.id)
            .then((res) => {
                this.loading.isActive = false;
            })
            .catch((error) => {
                this.loading.isActive = false;
            });
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
    },
};
</script>
