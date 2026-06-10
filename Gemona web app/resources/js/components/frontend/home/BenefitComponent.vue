<template>
    <LoadingComponent :props="loading" />
    <section v-if="benefits.length > 0" class="pt-8 pb-24 sm:py-12 border-t border-slate-100">
        <div class="container">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div v-for="benefit in benefits" class="w-full max-w-[236px] relative ltr:lg:pl-9 rtl:lg:pr-9">
                    <img :src="benefit.thumb" alt="benefit" class="w-6 mb-4 lg:mb-0 lg:absolute lg:top-0 ltr:lg:left-0 rtl:lg:right-0">
                    <h4 class="text-base font-semibold capitalize mb-2">{{ benefit.title }}</h4>
                    <p class="text-sm">{{ benefit.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "BenefitComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            }
        }
    },
    computed: {
        benefits: function () {
            return this.$store.getters["frontendBenefit/lists"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendBenefit/lists", {
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            status: statusEnum.ACTIVE,
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    }
}
</script>

