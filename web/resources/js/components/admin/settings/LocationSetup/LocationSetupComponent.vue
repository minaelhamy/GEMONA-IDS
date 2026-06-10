<template>
    <div class="db-tab-div font-medium active">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-5">

            <router-link :to="{ name: 'admin.settings.locationSetup.countries' }"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5">
                <i class="lab-line-cities text-sm"></i>
                {{ $t("menu.countries") }}
            </router-link>

            <router-link :to="{ name: 'admin.settings.locationSetup.states' }"  
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5">
                <i class="lab-line-cities text-sm"></i>
                {{ $t("menu.states") }}
            </router-link>

            <router-link :to="{ name: 'admin.settings.locationSetup.cities' }"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5">
                <i class="lab-line-cities text-sm"></i>
                {{ $t("menu.cities") }}
            </router-link>

        </div>
        <router-view></router-view>
    </div>
</template>

<script>
import CountryListComponent from "../countries/CountryListComponent";
import StateListComponent from "../states/StateListComponent";
import CityListComponent from "../cities/CityListComponent";

export default {
    name: "LocationSetupComponent",
    components: {
        CountryListComponent,
        StateListComponent,
        CityListComponent
    },
    data() {
        return {
            selectIndex: 1,
        }
    }
}
</script>