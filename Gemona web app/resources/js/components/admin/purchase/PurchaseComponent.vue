<template>
    <div class="row">
        <div class="col-12">
            <BreadcrumbComponent />
        </div>
        <router-view></router-view>
    </div>
</template>

<script lang="js">
import BreadcrumbComponent from '../components/BreadcrumbComponent'
import appService from "../../../services/appService";

export default {
    name: 'PurchaseComponent',
    components: {
        BreadcrumbComponent,
    }
}


</script>