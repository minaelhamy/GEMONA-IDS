<template>
    <section class="mb-10 sm:mb-20">
        <div class="container">
            <div class="row">
                <div class="col-3 hidden lg:block">
                    <AccountMenuComponent/>
                </div>
                <div class="col-12 lg:col-9">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import AccountMenuComponent from "./AccountMenuComponent";

export default {
    name: "AccountComponent",
    components: {
        AccountMenuComponent
    }
}
</script>
