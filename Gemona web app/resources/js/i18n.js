import { createI18n } from "vue-i18n";

function loadMessages() {
    const modules = import.meta.glob("./languages/*.json", { eager: true });
    const messages = {};

    for (const path in modules) {
        const matched = path.match(/\.\/languages\/([a-z0-9-_]+)\.json$/i);
        if (matched && matched.length > 1) {
            const locale = matched[1];
            messages[locale] = modules[path].default || modules[path];
        }
    }

    return { messages };
}

const { messages } = loadMessages();

const i18n = createI18n({
    legacy: false,
    locale: "en",
    fallbackLocale: "en",
    messages: messages
});

export default i18n;