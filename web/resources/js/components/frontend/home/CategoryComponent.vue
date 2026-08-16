<template>
    <LoadingComponent :props="loading" />
    <section v-if="categoryGroups.length > 0" class="marketplace-category-section">
        <div class="marketplace-container marketplace-category-grid">
            <article v-for="group in categoryGroups" :key="group.title" class="marketplace-category-card">
                <h2>{{ group.title }}</h2>
                <div class="marketplace-category-tiles">
                    <router-link
                        v-for="(category, index) in group.categories"
                        :key="category.id || category.slug"
                        :to="{ name: 'frontend.product', query: { category: category.slug } }"
                        class="marketplace-category-tile"
                        :class="{ 'marketplace-category-tile-wide': group.categories.length === 3 && index === 2 }"
                    >
                        <span class="marketplace-category-image-wrap">
                            <img
                                :src="category.thumb"
                                :alt="category.name"
                                class="marketplace-category-image"
                                loading="lazy"
                                decoding="async"
                            >
                        </span>
                        <span class="marketplace-category-name">{{ category.name }}</span>
                    </router-link>
                </div>
                <router-link :to="{ name: 'frontend.product' }" class="marketplace-card-link">
                    Shop all categories
                    <i class="lab-line-chevron-right"></i>
                </router-link>
            </article>
        </div>
    </section>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "CategoryComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: { isActive: false },
            groupDefinitions: [
                {
                    title: "Food & pantry",
                    names: ["Beverages", "Pantry & Cooking", "Snacks & Confectionery", "Breakfast & Bakery"],
                },
                {
                    title: "Tech & computing",
                    names: ["Mobiles & Tablets", "Computers & Office", "TVs & Audio", "Gaming & Electronics"],
                },
                {
                    title: "Appliances & home",
                    names: ["Large Appliances", "Kitchen Appliances", "Home & Kitchen", "Household & Cleaning"],
                },
                {
                    title: "Personal & family",
                    names: ["Personal Care", "Baby & Family", "Other"],
                },
            ],
        };
    },
    computed: {
        categories() {
            return this.$store.getters["frontendProductCategory/lists"] || [];
        },
        usableCategories() {
            return this.categories.filter((category) => {
                return category.thumb && !category.thumb.includes("/images/default/category/");
            });
        },
        categoryGroups() {
            const byName = new Map(this.usableCategories.map((category) => [category.name, category]));
            const grouped = this.groupDefinitions.map((group) => ({
                title: group.title,
                categories: group.names.map((name) => byName.get(name)).filter(Boolean),
            })).filter((group) => group.categories.length > 0);

            if (grouped.length >= 3) return grouped;

            const fallback = [];
            for (let index = 0; index < this.usableCategories.length; index += 4) {
                fallback.push({
                    title: index === 0 ? "Shop by category" : "More to explore",
                    categories: this.usableCategories.slice(index, index + 4),
                });
            }
            return fallback.slice(0, 4);
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendProductCategory/lists", {
            paginate: 1,
            per_page: 60,
            order_column: "id",
            order_type: "asc",
            parent_id: null,
            status: statusEnum.ACTIVE,
        }).finally(() => {
            this.loading.isActive = false;
        });
    },
};
</script>

<style scoped>
.marketplace-category-section {
    position: relative;
    z-index: 3;
    margin-top: -58px;
    padding-bottom: 22px;
}

.marketplace-container {
    width: min(100% - 24px, 1440px);
    margin-inline: auto;
}

.marketplace-category-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.marketplace-category-card {
    display: flex;
    min-width: 0;
    min-height: 392px;
    flex-direction: column;
    padding: 18px;
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 3px 14px rgba(23, 26, 45, 0.08);
}

.marketplace-category-card h2 {
    margin-bottom: 14px;
    color: #171a2d;
    font-size: 22px;
    font-weight: 750;
    line-height: 1.2;
}

.marketplace-category-tiles {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px 10px;
}

.marketplace-category-tile {
    min-width: 0;
    color: #171a2d;
}

.marketplace-category-tile-wide {
    grid-column: span 2;
}

.marketplace-category-image-wrap {
    display: block;
    height: 112px;
    overflow: hidden;
    border-radius: 4px;
    background: #f4f5f7;
}

.marketplace-category-tile-wide .marketplace-category-image-wrap {
    height: 104px;
}

.marketplace-category-image {
    width: 100%;
    height: 100%;
    padding: 6px;
    object-fit: contain;
    transition: transform 220ms ease;
}

.marketplace-category-tile:hover .marketplace-category-image {
    transform: scale(1.055);
}

.marketplace-category-name {
    display: block;
    margin-top: 6px;
    overflow: hidden;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.marketplace-category-tile:hover .marketplace-category-name,
.marketplace-card-link:hover {
    color: rgb(var(--primary));
}

.marketplace-card-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: auto;
    padding-top: 16px;
    color: #343952;
    font-size: 13px;
    font-weight: 700;
}

@media (max-width: 1100px) {
    .marketplace-category-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 639px) {
    .marketplace-category-section {
        margin-top: -28px;
        padding-bottom: 14px;
    }
    .marketplace-container { width: min(100% - 20px, 1440px); }
    .marketplace-category-grid {
        grid-auto-flow: column;
        grid-auto-columns: 88vw;
        grid-template-columns: none;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        scroll-snap-type: inline mandatory;
    }
    .marketplace-category-card {
        min-height: 374px;
        scroll-snap-align: center;
    }
}
</style>
