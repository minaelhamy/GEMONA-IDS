<template>
    <LoadingContentComponent :props="loading"/>
    <div v-if="status" class="ai-move" @click.prevent="openCanvas('ai-sidebar')">
        <span class="mb-1">
            <i class="lab-fill-ai lab-font-size-24 text-[#8B5CF6]"></i>
        </span>
        <span class="text-[10px] font-bold leading-none">
            <span class="text-[#8B5CF6]">{{ $t('label.ai') }}&nbsp;</span>
            <span class="text-slate-900">{{ $t('label.helper') }}</span>
        </span>
    </div>

    <div v-if="status" id="ai-sidebar" @click="closeBackdrop" class="fixed inset-0 z-50 invisible transition-all duration-500 opacity-0 bg-black/50">
        <div class="flex flex-col w-full max-w-xl transition-all duration-300 bg-white shadow-2xl h-dvh ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="z-10 flex items-center justify-between flex-shrink-0 p-6 bg-white border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800">{{ $t('label.ai_assistant') }}</h3>
                <button class="text-xl font-bold transition-colors fa-solid fa-xmark close-btn text-slate-500 hover:text-red-500" @click="reset"></button>
            </div>

            <div class="flex-1 overflow-y-auto thin-scrolling p-6 bg-[#F7F7FC]" ref="chatContainer">
                <div class="space-y-6">
                    <div v-for="message in messages" :key="message.id" class="w-full">
                        <div class="ml-auto flex w-fit justify-end bg-white text-slate-600 rounded-tl-none border border-slate-100 max-w-[85%] rounded-2xl p-4 text-sm font-medium leading-relaxed shadow-sm relative group">
                            {{ message.message }}
                        </div>

                        <div v-if="message.response" class="flex w-full justify-start bg-gray-200/70 text-black/60 rounded-tr-none mt-6 max-w-[85%] rounded-2xl p-4 text-sm font-medium leading-relaxed shadow-sm relative group">
                            <div v-html="message.response"></div>
                        </div>
                    </div>
                    <div v-if="isTyping" class="flex justify-start">
                        <div class="p-4 text-sm bg-white border rounded-tl-none text-slate-600 rounded-2xl border-slate-100">
                            <div class="flex gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-400 animate-bounce delay-0"></span>
                                <span class="w-2 h-2 rounded-full bg-slate-400 animate-bounce delay-0"></span>
                                <span class="w-2 h-2 rounded-full bg-slate-400 animate-bounce delay-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0 p-6 bg-white border-t border-slate-100">
                <form @submit.prevent="sendMessage" class="relative flex items-center">
                    <input v-model="chatInput" type="text" :placeholder="$t('message.ask_me_anything')" :disabled="isTyping" class="w-full pl-5 pr-12 py-4 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-[#1ABC9C] focus:ring-1 focus:ring-[#1ABC9C] text-sm text-slate-700 placeholder:text-slate-400 transition-all shadow-sm disabled:opacity-50">
                    <button type="submit" :disabled="!chatInput.trim() || isTyping" class="absolute right-3 p-2 text-slate-400 hover:text-[#1ABC9C] transition-colors rounded-lg hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="lab-fill-send lab-font-size-24 leading-[30px]"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingContentComponent from "../../admin/components/LoadingContentComponent.vue";
import {useCanvas} from "../../../composables/canvas.js";
import alertService from "../../../services/alertService.js";


export default {
    name: "BackendAiSidebarComponent",
    components: {LoadingContentComponent},
    data() {
        return {
            loading: {
                isActive: false
            },
            loadingComponent: {
                isActive: false
            },
            messages: [],
            chatInput: "",
            chatInputItem: "",
            isTyping: false,
            openCanvas: useCanvas().openCanvas,
            closeBackdrop: useCanvas().closeBackdrop
        }
    },
    computed: {
        status: function () {
            return this.$store.getters['ai/status'];
        }
    },
    mounted() {
        this.loadHistory();
    },
    methods: {
        loadHistory: function () {
            this.$store.dispatch('ai/fetchStatus').then((res) => {
                if(res.data.data) {
                    this.loading.isActive = true;
                    this.$store.dispatch('ai/fetchChatHistory').then((res) => {
                        this.loading.isActive = false;
                        if (res.data.data && res.data.data.length > 0) {
                            this.messages = res.data.data;
                        }
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                }
            }).catch((err) => {
                alertService.error(this.$t('message.something_wrong'));
            });
        },
        sendMessage: function () {
            this.$nextTick(() => {
                this.scrollToBottom();
            });
            this.$store.dispatch('ai/sendChatMessage', this.chatInput.trim()).then((res) => {
                if (res.data.data) {
                    this.messages.push(res.data.data);
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                    this.chatInputItem = this.chatInput;
                    this.chatInput     = "";
                    this.isTyping      = true;
                    this.$store.dispatch('ai/setChatResponse', {id: res.data.data.id, name: this.chatInputItem}).then((res) => {
                        this.isTyping = false;
                        this.messages.map((item, index) => {
                            if (index === this.messages.length - 1 && !item.response) {
                                item.response = res.data.data.response;
                            }
                        });
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }).catch((err) => {
                        this.isTyping = false;
                        alertService.error(this.$t('message.something_wrong'));
                    })
                }
            }).catch((err) => {
                this.isTyping = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        scrollToBottom: function () {
            const container = this.$refs.chatContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        reset: function () {
            useCanvas().closeCanvas('ai-sidebar');
        }
    }
}



</script>
