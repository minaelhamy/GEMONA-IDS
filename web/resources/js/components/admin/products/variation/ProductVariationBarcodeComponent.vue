<template>
    <LoadingComponent :props="loading" />

    <div id="variationBarcodeModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t('label.barcode') }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500" @click="reset">
                </button>
            </div>
            <div class="modal-body">
                <div class="row py-0">
                    <div class="col-12 md:col-6" id="variationBarcodePrint">
                        <img class="db-image" alt="product-variation-barcode" :src="barcodeProps.barcode_image" />
                        <span class="mt-2">{{ barcodeProps.sku }}</span>
                    </div>
                    <div class="col-12 md:col-6 hidden-print">
                        <div class="flex items-center justify-end gap-3">
                            <button v-if="barcodeProps.barcode_image" @click="downloadBarcode(barcodeProps.sku)"
                                type="button"
                                class="flex items-center justify-center gap-1.5 h-10 px-4 db-btn text-white bg-primary">
                                <i class="lab lab-fill-download"></i>
                                <span class="capitalize text-sm font-medium">{{ $t('label.download') }}</span>
                            </button>
                            <PrintButtonComponent :props="printObj"
                                :buttonClass="'flex items-center justify-center gap-1.5 h-10 px-4 db-btn text-white bg-success'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../../components/LoadingComponent.vue";
import PrintButtonComponent from "../../components/buttons/PrintButtonComponent.vue";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import _ from "lodash";

export default {
    name: "ProductVariationBarcodeComponent",
    components: { LoadingComponent, PrintButtonComponent },
    props: ["barcodeProps"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            printObj: {
                id: "variationBarcodePrint",
                popTitle: this.$t('menu.variations')
            },
            productId: null,
            variationProps: {
                form: {
                    attribute: null
                },
                productId: null,
            },
            errors: {},
        };
    },
    methods: {
        reset: function () {
            appService.modalHide('#variationBarcodeModal');

        },
        downloadBarcode: function (sku) {
            if (!isNaN(this.barcodeProps.variation_id)) {
                this.loading.isActive = true;
                this.$store.dispatch("productVariation/downloadBarcode", this.barcodeProps.variation_id).then((res) => {
                    this.loading.isActive = false;
                    appService.modalHide('#variationBarcodeModal');
                    let fileType = "";
                    if (res.data.type) {
                        let type = res.data.type;
                        type = type.split("/");
                        fileType = type[1];
                    }

                    if (res.data.size > 0) {
                        const url = window.URL.createObjectURL(
                            new Blob([res.data])
                        );
                        const link = document.createElement("a");
                        link.href = url;
                        link.download =
                            "" + sku + "." + fileType;
                        link.click();
                        URL.revokeObjectURL(link.href);
                    } else {
                        alertService.info(this.$t("menu.variations") + " " + this.$t('message.barcode_not_found'));
                    }

                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        },
    }
};
</script>