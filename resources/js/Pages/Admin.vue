<template>
    <div>
        <app-layout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Admin
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                    <jet-validation-errors class="mb-4" />

                    <!-- category wrapper -->
                    <div class="bg-white rounded shadow px-4 py-4">
                        <div class="flex items-center text-sm mt-2">
                            <span
                                >Vul de gewenste categorie in en druk op enter
                                om deze toe te voegen</span
                            >
                        </div>
                        <!-- <input type="text" placeholder="titel" class=" rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full mt-4" v-model="categoryTitle" @keydown.enter="addCategory"> -->
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input
                                v-model="categoryTitle"
                                @keydown.enter="addCategory"
                                type="text"
                                class="
                                    focus:ring-indigo-500
                                    focus:border-indigo-500
                                    block
                                    w-full
                                    pl-2
                                    pr-12
                                    sm:text-sm
                                    border-gray-300
                                    rounded-md
                                "
                                placeholder="Hoe heet je categorie?"
                            />
                        </div>

                        <!-- category list -->
                        <ul class="category-list mt-4">
                            <li
                                v-for="category in admin_categories"
                                v-bind:key="category.id"
                                class="flex justify-between items-center mt-3"
                            >
                                <div class="flex items-center">
                                    <div class="ml-3 text-sm font-semibold">
                                        {{ category.title }}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="p-2 bg-blue-300 text-blue-700 rounded-md text-sm">
                                        Admin {{ category.user.name }}
                                    </span>
                                    <button class="ml-2">
                                        <svg
                                            class="
                                                w-4
                                                h-4
                                                text-gray-600
                                                fill-current
                                            "
                                            @click="deleteCategory(category.id)"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>


                </div>
            </div>
        </app-layout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

export default {
    components: {
        AppLayout,
        Welcome,
        JetValidationErrors,
    },

    props: {
        admin_categories: Object,
    },


    data() {
        return {
            categoryTitle: "",
        };
    },

    methods: {
        addCategory() {
            this.$inertia.post("/category", {
                title: this.categoryTitle,
                is_admin: true
            });
            this.categoryTitle = "";
        },

        deleteCategory(id) {
            this.$inertia.post("/category/" + id, {
                is_admin: true,
                _method: 'DELETE'
            });
        },
    },
};
</script>
